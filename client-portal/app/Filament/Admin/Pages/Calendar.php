<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\LawFirmCalendarWidget;
use Filament\Pages\Page;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.admin.pages.calendar';

    protected static ?string $navigationGroup = 'Calendar & Time';

    protected static ?int $navigationSort = 0;

    protected static ?string $title = 'Calendar';

    protected static ?string $navigationLabel = 'Calendar';

    protected function getHeaderWidgets(): array
    {
        return [
            LawFirmCalendarWidget::class,
        ];
    }
}