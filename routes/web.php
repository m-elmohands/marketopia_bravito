<?php

use App\Http\Controllers\Admin\InvoiceController;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Route;
use Spatie\Browsershot\Browsershot;


Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/lang/{lang}', function ($lang) {
    session()->put('lang', $lang);
    return redirect()->back();
})->name('lang')->middleware('webLang');

// اختبار Telegram Bot
Route::get('/test-telegram', function (TelegramService $telegram) {
    $message = "<b>🧪 رسالة اختبار</b>\n\n";
    $message .= "هذه رسالة اختبار للتأكد من عمل Telegram Bot بشكل صحيح.\n";
    $message .= "التوقيت: " . now()->format('Y-m-d H:i:s') . "\n";
    $message .= "الحالة: ✅ نجح الاتصال";
    
    $result = $telegram->sendMessage($message);
    
    if ($result) {
        return response()->json([
            'success' => true,
            'message' => '✅ تم إرسال الرسالة بنجاح إلى Telegram!'
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => '❌ فشل في إرسال الرسالة إلى Telegram!'
        ]);
    }
});

Route::get('/orders/{order}/invoice', [InvoiceController::class, 'show'])
    ->name('admin.orders.invoice');
