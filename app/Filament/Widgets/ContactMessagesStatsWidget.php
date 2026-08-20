<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\DB;

class ContactMessagesStatsWidget extends BaseWidget
{
    protected function getCards(): array
    {
        $totalMessages = ContactMessage::count();
        $unreadMessages = ContactMessage::unread()->count();
        $readMessages = ContactMessage::read()->count();
        $todayMessages = ContactMessage::whereDate('created_at', today())->count();

        // Get stats for chart (last 7 days)
        $messagesStats = ContactMessage::where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->select(DB::raw('COUNT(*) as count'))
            ->pluck('count')
            ->toArray();

        $unreadStats = ContactMessage::unread()
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->select(DB::raw('COUNT(*) as count'))
            ->pluck('count')
            ->toArray();

        return [
            Stat::make(__('message.Total Messages'), $totalMessages)
                ->description(__('message.All contact messages'))
                ->descriptionIcon('heroicon-m-envelope')
                ->chart($messagesStats)
                ->color('primary'),

            Stat::make(__('message.Unread Messages'), $unreadMessages)
                ->description(__('message.Messages waiting to be read'))
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->chart($unreadStats)
                ->color($unreadMessages > 0 ? 'danger' : 'success'),

            Stat::make(__('message.Read Messages'), $readMessages)
                ->description(__('message.Messages that have been read'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('message.Today Messages'), $todayMessages)
                ->description(__('message.Messages received today'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color($todayMessages > 0 ? 'info' : 'gray'),
        ];
    }

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return true;
    }
}
