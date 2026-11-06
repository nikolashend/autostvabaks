<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $unsent = Lead::query()->whereNull('mail_sent_at')->count();

        return array_filter([
            Stat::make('Заявок сегодня', Lead::query()->whereDate('created_at', today())->count()),
            Stat::make('За последние 7 дней', Lead::query()->where('created_at', '>=', now()->subDays(7))->count()),
            Stat::make('Всего заявок', Lead::query()->count()),
            $unsent > 0
                ? Stat::make('Письмо не отправлено', $unsent)
                    ->description('Проверьте настройки почты – заявки сохранены в разделе «Заявки»')
                    ->color('danger')
                : null,
        ]);
    }
}
