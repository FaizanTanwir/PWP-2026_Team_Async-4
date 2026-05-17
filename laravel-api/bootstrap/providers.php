<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    IdeHelperServiceProvider::class,
];
