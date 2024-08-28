<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Admin' => 'App\Policies\AdminPolicy',
        'App\Models\Permission' => 'App\Policies\PermissionsPolicy',
        'App\Models\Role' => 'App\Policies\RolePolicy',
        'App\Models\Setting' => 'App\Policies\SiteSettingsPolicy',
        'App\Models\Product' => 'App\Policies\ProductsPolicy',

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
