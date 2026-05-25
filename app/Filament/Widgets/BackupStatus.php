<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Storage;

class BackupStatus extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0] ?? 'local');
        $name = config('backup.backup.name', 'GEMDTEK');

        try {
            $files = collect($disk->allFiles($name))
                ->filter(fn ($f) => str_ends_with($f, '.zip'))
                ->map(fn ($f) => [
                    'path' => $f,
                    'modified' => $disk->lastModified($f),
                    'size_mb' => round($disk->size($f) / 1024 / 1024, 1),
                ])
                ->sortByDesc('modified')
                ->values();

            $count = $files->count();
            $last = $files->first();

            if (! $last) {
                return [
                    Stat::make('Yedekleme', 'Hiç yedek yok')
                        ->description('php artisan backup:run komutunu çalıştır')
                        ->descriptionIcon('heroicon-o-exclamation-triangle')
                        ->color('danger'),
                ];
            }

            $lastDate = Carbon::createFromTimestamp($last['modified']);
            $hoursAgo = $lastDate->diffInHours(now());
            $color = match (true) {
                $hoursAgo <= 36 => 'success',
                $hoursAgo <= 72 => 'warning',
                default => 'danger',
            };

            return [
                Stat::make('Son yedek', $lastDate->diffForHumans())
                    ->description($lastDate->format('d M Y H:i'))
                    ->descriptionIcon('heroicon-o-clock')
                    ->color($color),
                Stat::make('Yedek dosya sayısı', (string) $count)
                    ->description('Disk üzerinde saklı')
                    ->descriptionIcon('heroicon-o-archive-box')
                    ->color('info'),
                Stat::make('Son yedek boyutu', $last['size_mb'].' MB')
                    ->description('DB + storage/app/public arşivi')
                    ->descriptionIcon('heroicon-o-cube')
                    ->color('gray'),
            ];
        } catch (\Throwable $e) {
            return [
                Stat::make('Yedekleme', 'Hata')
                    ->description(class_basename($e))
                    ->descriptionIcon('heroicon-o-x-circle')
                    ->color('danger'),
            ];
        }
    }
}
