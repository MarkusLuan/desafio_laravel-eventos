<?php

namespace App\Filament\Widgets;

use Filament\Widgets\AccountWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserInfoWidget extends AccountWidget
{
    protected static string $view = 'filament.widgets.user-info-widget';
}
