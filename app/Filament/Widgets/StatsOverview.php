<?php

namespace App\Filament\Widgets;

use App\Models\Alumni;
use App\Models\FormSubmission;
use App\Models\Project;
use App\Models\SponsorLead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $thisWeekSubmissions = FormSubmission::where('created_at', '>=', now()->startOfWeek())->count();
        $lastWeekSubmissions = FormSubmission::whereBetween('created_at', [
            now()->subWeek()->startOfWeek(),
            now()->subWeek()->endOfWeek(),
        ])->count();

        $thisMonthLeads = SponsorLead::where('created_at', '>=', now()->startOfMonth())->count();
        $lastMonthLeads = SponsorLead::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth(),
        ])->count();

        $weekTrend = $this->trend($thisWeekSubmissions, $lastWeekSubmissions);
        $leadTrend = $this->trend($thisMonthLeads, $lastMonthLeads);

        return [
            Stat::make('Public Mezunlar', Alumni::public()->count())
                ->description('Sektörde aktif')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Aktif Projeler', Project::active()->count())
                ->description('Ar-Ge takımları')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('info'),

            Stat::make('Bu Hafta Başvuru', $thisWeekSubmissions)
                ->description($weekTrend['label'])
                ->descriptionIcon($weekTrend['icon'])
                ->color($weekTrend['color']),

            Stat::make('Bu Ay Sponsor Lead', $thisMonthLeads)
                ->description($leadTrend['label'])
                ->descriptionIcon($leadTrend['icon'])
                ->color($leadTrend['color']),
        ];
    }

    private function trend(int $current, int $previous): array
    {
        if ($previous === 0 && $current === 0) {
            return ['label' => 'Hareket yok', 'icon' => 'heroicon-m-minus', 'color' => 'gray'];
        }
        if ($previous === 0) {
            return ['label' => "+{$current} yeni", 'icon' => 'heroicon-m-arrow-trending-up', 'color' => 'success'];
        }
        $pct = round((($current - $previous) / $previous) * 100);
        if ($pct > 0) {
            return ['label' => "+%{$pct} önceki periyoda göre", 'icon' => 'heroicon-m-arrow-trending-up', 'color' => 'success'];
        }
        if ($pct < 0) {
            return ['label' => "%{$pct} önceki periyoda göre", 'icon' => 'heroicon-m-arrow-trending-down', 'color' => 'danger'];
        }

        return ['label' => 'Aynı seviyede', 'icon' => 'heroicon-m-minus', 'color' => 'gray'];
    }
}
