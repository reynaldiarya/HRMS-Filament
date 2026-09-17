<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\EmployeePanelProvider;
use App\Providers\Filament\HrPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    EmployeePanelProvider::class,
    HrPanelProvider::class,
];
