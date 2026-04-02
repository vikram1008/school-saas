<?php

use App\Providers\AppServiceProvider;
use App\Providers\MenuServiceProvider;
use App\Providers\TenancyServiceProvider;

return [
    AppServiceProvider::class,
    MenuServiceProvider::class,
    TenancyServiceProvider::class,
];
