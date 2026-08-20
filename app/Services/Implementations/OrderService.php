<?php

namespace App\Services\Implementations;

use App\Helpers\PriceHelpers;
use App\Repository\Contracts\AddressRepositoryInterface;
use App\Repository\Contracts\AuthRepositoryInterface;
use App\Repository\Contracts\CartRepositoryInterface;
use App\Repository\Contracts\OrderRepositoryInterface;
use App\Repository\Contracts\PaymobPaymentRepositoryInterface;
use App\Services\Contracts\DeliveryFeeServiceInterface;
use App\Services\Contracts\FirebaseServiceInterface;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\TelegramService;
use App\Services\Contracts\PaymobServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService implements OrderServiceInterface
{
    protected $orderRepo;
    protected $deliveryFeeService;
    protected $cartRepo;
    protected $addressRepo;
    protected $paymobService;
    protected $paymentRepo;
    protected $firebaseService;
    protected $authRepo;
    protected $telegramService;

    public function __construct(
        OrderRepositoryInterface $orderRepo,
        DeliveryFeeServiceInterface $deliveryFeeService,
        CartRepositoryInterface $cartRepo,
        AddressRepositoryInterface $addressRepo,
        PaymobServiceInterface $paymobService,
        PaymobPaymentRepositoryInterface $paymentRepositor,
        FirebaseServiceInterface $firebaseService,
        AuthRepositoryInterface $authRepo,
        TelegramService $telegramService
    ) {
        $this->orderRepo = $orderRepo;
        $this->deliveryFeeService = $deliveryFeeService;
        $this->cartRepo = $cartRepo;
        $this->addressRepo = $addressRepo;
        $this->paymobService = $paymobService;
        $this->paymentRepo = $paymentRepositor;
        $this->firebaseService = $firebaseService;
        $this->authRepo = $authRepo;
        $this->telegramService = $telegramService;
    }
    public function create(array $data)
    {
        $carts = $this->cartRepo->getUserCart($data['user_id']);

        if ($carts->isEmpty())
            return [
                'success' => false,
                'message' => __('message.Cart Is Empty'),
            ];

        $addresses = $this->addressRepo->getAddresses($data['user_id']);
        $addressFound = false;
        foreach ($addresses as $address) {
            if ($address->id == $data['address_id']) {
                $addressFound = true;
                break;
            }
        }

        if (!$addressFound)
            return [
                'success' => false,
                'message' => __('message.Address Not Found'),
            ];

        $subtotal = PriceHelpers::calcolatePriceForCart($carts);
        $address = $this->addressRepo->find($data['address_id']);

        if ($address->city->min_price > $subtotal['total_price_after_discount'])
            return [
                'success' => false,
                'message' => __('message.Minimum Price For Orders') . ' ' . $address->city->min_price,
            ];

        $devaliveryFee = $this->deliveryFeeService->calculateDeliveryFee($data['address_id'], $data['area_id']);

        if (!$devaliveryFee['success'])
            return [
                'success' => false,
                'message' => $devaliveryFee['message']
            ];

        $subtotal['total_price_after_discount'] += $devaliveryFee['data'];

        // Apply coupon discount
        $coupon = null;
        $couponDiscount = 0;
        if (!empty($data['coupon_id'])) {
            $coupon = \App\Models\Coupon::find($data['coupon_id']);
            if ($coupon) {
                if ($coupon->discount_type->value === 'percentage') {
                    $couponDiscount = $subtotal['total_price_after_discount'] * ($coupon->discount_value / 100);
                } else {
                    $couponDiscount = $coupon->discount_value;
                }
                $subtotal['total_price_after_discount'] = max(0, $subtotal['total_price_after_discount'] - $couponDiscount);
                $subtotal['discount'] += $couponDiscount;
            }
        }

        $order = DB::transaction(function () use ($carts, $data, $subtotal, $devaliveryFee) {
            $paymentData = null;
            $order = $this->orderRepo->create([
                'user_id' => $data['user_id'],
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'subtotal' => $subtotal['total_price'],
                'total' => $subtotal['total_price_after_discount'],
                'notes' => $data['notes'] ?? null,
                'order_type' => 'online',
                'coupon_id' => $data['coupon_id'] ?? null,
                'discount' => $subtotal['discount'],
                'delivery_fee' => $devaliveryFee['data'],
                'area_id' => $data['area_id'] ?? null,
                'deliveryman_id' => null,
                'created_by' => $data['user_id'],
                'address_id' => $data['address_id'],
            ]);
            foreach ($carts as $cart) {
                $orderDetail = $order->orderDetails()->create([
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->product->price,
                ]);

                if ($cart->productAttributeValues)
                    foreach ($cart->productAttributeValues as $attributeValue) {
                        $orderDetail->attributeValues()->create([
                            'attribute_value_id' => $attributeValue->attribute_value_id,
                        ]);
                    }
            }
            if ($order->payment_method == 'cash' || $order->payment_method == 'wallet')
                $carts->each->delete();

            if ($order->payment_method != 'cash') {
                $paymentData = $this->paymobService->generatePaymentLink(
                    (string)$order->id,
                    $order->payment_method == 'visa' ? 'card' : 'wallet',
                    (float)$order->total,
                    [
                        'name' => $order->user->name,
                        'email' => $order->user->email,
                        'phone' => $order->user->phone
                    ],
                    $order->payment_method == 'visa' ? 'card' : 'wallet',
                    $data['wallet_number']
                );
            }

            if ($paymentData)
                $this->paymentRepo->create([
                    'user_id' => $order->user_id,
                    'amount' => $order->total,
                    'payment_method' => $order->payment_method,
                    'status' => 'pending',
                    'order_id' => $paymentData['order_id'],
                    'pending' => true,
                    'success' => false,
                    'my_order_id' => $order->id
                ]);
            return [
                'order' => $order,
                'paymentData' => $paymentData
            ];
        });

        $this->authRepo->getAdmins()->each(function ($admin) use ($order) {
            $this->firebaseService->sendNotification(
                __('message.New Order'),
                __('message.New Order Created') . ' #' . $order['order']->id,
                'user',
                $admin->fcm_token ?? '',
                false,
                $admin->id,
                null,
            );
        });

        // Send Telegram notification (in separate try-catch to avoid breaking order creation)
        try {
            $this->sendOrderToTelegram($order['order']);
        } catch (\Exception $e) {
            Log::error('Telegram notification failed for order ' . $order['order']->id . ': ' . $e->getMessage());
        }

        return [
            'success' => true,
            'message' => __('message.Success'),
            'data' => [
                'order_id' => $order['order']->id,
                'link' => $order['paymentData']['payment_url'] ?? null,
            ],
        ];
    }
    public function getOrders(int $userId, int $perPage, ?string $status, ?string $paymentStatus, ?string $paymentMethod, ?string $search)
    {
        $orders = $this->orderRepo->getOrders($userId, $perPage, $status, $paymentStatus, $paymentMethod, $search);
        $orders->map(function ($order) {
            $order->orderDetails->map(function ($orderDetail) {
                $orderDetail->product->image = $orderDetail->product->images->first()?->image;
                unset($orderDetail->product->images);
            });
        });
        return [
            'success' => true,
            'message' => __('message.Success'),
            'data' => $orders
        ];
    }
    public function getOrderById(int $id)
    {
        $order = $this->orderRepo->getOrderById($id);
        return [
            'success' => true,
            'message' => __('message.Success'),
            'data' => $order
        ];
    }
    public function getDeliverymanOrders(int $deliverymanId, int $perPage, ?string $status = null, ?string $paymentStatus, ?string $paymentMethod, ?string $search)
    {
        $orders = $this->orderRepo->getDeliverymanOrders($deliverymanId, $perPage, $status, $paymentStatus, $paymentMethod, $search);
        $orders->map(function ($order) {
            $order->orderDetails->map(function ($orderDetail) {
                $orderDetail->product->image = $orderDetail->product->images->first()?->image;
                unset($orderDetail->product->images);
            });
        });

        return [
            'success' => true,
            'message' => __('message.Success'),
            'data' => $orders
        ];
    }
    public function updateOrderStatusByDeliveryman(int $orderId, string $status)
    {
        $order = $this->orderRepo->getOrderById($orderId);

        if ($order->deliveryman_id != auth('api')->user()->id)
            return [
                'success' => false,
                'message' => __('message.Unauthorized'),
            ];

        if ($order->status == 'delivered')
            return [
                'success' => false,
                'message' => __('message.Order Already Delivered'),
            ];

        if ($order->status == 'ready' && $status != 'on_delivery')
            return [
                'success' => false,
                'message' => __('message.Invalid Status Change'),
            ];

        if ($order->status == 'on_delivery' && $status != 'delivered')
            return [
                'success' => false,
                'message' => __('message.Invalid Status Change'),
            ];

        if (!in_array($status, ['on_delivery', 'delivered']))
            return [
                'success' => false,
                'message' => __('message.Invalid Status'),
            ];

        $this->orderRepo->updateOrderStatus($orderId, $status);

        return [
            'success' => true,
            'message' => __('message.Success')
        ];
    }

   
    private function sendOrderToTelegram($order)
    {
        try {
            $order->load(['user', 'address', 'orderDetails.product.images', 'area']);
            
            $message = "<b>� New Customer Order!</b>\n\n";
            $message .= "<b>📋 Customer Details:</b>\n";
            $message .= "Name: {$order->user->name}\n";
            $message .= "Phone: {$order->user->phone}\n";
            $message .= "Email: {$order->user->email}\n\n";
            
            $message .= "<b>📍 Address:</b>\n";
            $message .= "Address: {$order->address->address}\n";
            if ($order->area) {
                $message .= "Area: {$order->area->name}\n";
            }
            $message .= "\n";
            
            $message .= "<b>🛒 Order Details:</b>\n";
            foreach ($order->orderDetails as $detail) {
                $totalPrice = $detail->price * $detail->quantity;
                
                // Get product name based on app locale, with fallback
                $productName = app()->getLocale() == 'ar' 
                    ? ($detail->product->name_ar ?: $detail->product->name_en ?: $detail->product->name ?: 'Unknown Product')
                    : ($detail->product->name_en ?: $detail->product->name_ar ?: $detail->product->name ?: 'Unknown Product');
                
                // Generate product URL
                $productUrl = "https://com2-phi.vercel.app" . "/product/{$detail->product->id}";
                
                // Create clickable product name with link
                $productLink = "<a href='{$productUrl}'>{$productName}</a>";
                
                $message .= "• {$productLink} × {$detail->quantity} = {$totalPrice} EGP\n";
                $message .= "   🔗 <a href='{$productUrl}'>View Product</a>\n";
                
                // Add product image if available
                if ($detail->product->images && $detail->product->images->first()) {
                    $imageUrl = config('app.url') . '/public/storage/' . $detail->product->images->first()->image;
                    $message .= "   📸 <a href='{$imageUrl}'>Product Image</a>\n";
                }
                $message .= "\n";
            }
            $message .= "\n";
            
            $message .= "<b>💰 Financial Details:</b>\n";
            $message .= "Subtotal: {$order->subtotal} EGP\n";
            $message .= "Delivery Fee: {$order->delivery_fee} EGP\n";
            if ($order->discount > 0) {
                $message .= "Discount: -{$order->discount} EGP\n";
            }
            $message .= "Total: {$order->total} EGP\n\n";
            
            $message .= "<b>💳 Payment Method:</b> " . ucfirst($order->payment_method) . "\n";
            $message .= "<b>📦 Order Number:</b> #{$order->id}\n";
            $message .= "<b>🌐 Source:</b> Mobile App/Website\n";
            
            if ($order->notes) {
                $message .= "<b>📝 Notes:</b> {$order->notes}\n";
            }

            $this->telegramService->sendMessage($message);
            
            // Send product images as separate photos if available
            // $this->sendProductImages($order);
            
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram message: ' . $e->getMessage());
        }
    }

    private function sendProductImages($order)
    {
        try {
            // Get bot token and chat ID from environment
            $botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
            $chatId = config('services.telegram.chat_id', env('TELEGRAM_CHAT_ID'));

            foreach ($order->orderDetails as $detail) {
                if ($detail->product->images && $detail->product->images->first()) {
                    $imagePath = storage_path('app/public/' . $detail->product->images->first()->image);
                    
                    // Check if the image file exists
                    if (file_exists($imagePath)) {
                        $productName = app()->getLocale() == 'ar' 
                            ? ($detail->product->name_ar ?: $detail->product->name_en ?: $detail->product->name ?: 'Unknown Product')
                            : ($detail->product->name_en ?: $detail->product->name_ar ?: $detail->product->name ?: 'Unknown Product');
                        
                        $productUrl = "https://com2-phi.vercel.app" . "/product/{$detail->product->id}";
                        $caption = "🛍️ {$productName}\n💰 {$detail->price} EGP\n🔗 <a href='{$productUrl}'>View Details</a>";
                        
                        $this->sendTelegramPhoto($botToken, $chatId, $imagePath, $caption);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send product images: ' . $e->getMessage());
        }
    }

    private function sendTelegramPhoto($botToken, $chatId, $imagePath, $caption)
    {
        $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";
        
        $postData = [
            'chat_id' => $chatId,
            'caption' => $caption,
            'parse_mode' => 'HTML',
            'photo' => new \CURLFile($imagePath)
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode === 200 && $response !== false) {
            Log::info('Product image sent successfully to Telegram');
        } else {
            Log::error('Failed to send product image to Telegram');
            Log::error('HTTP Code: ' . $httpCode);
            Log::error('Response: ' . $response);
            Log::error('cURL Error: ' . $error);
        }
    }
}
