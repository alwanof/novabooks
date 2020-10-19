<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Silvanite\Brandenburg\Traits\ValidatesPermissions;

class AuthServiceProvider extends ServiceProvider
{
    use ValidatesPermissions;
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        'App\Task' => 'App\Policies\TaskPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        collect([
            'viewTask',
            'manageTask',
        ])->each(function ($permission) {
            Gate::define($permission, function ($user) use ($permission) {
                // if ($this->nobodyHasAccess($permission)) {
                //     return true;
                // }

                return $user->hasRoleWithPermission($permission);
            });
        });
        $this->registerPolicies();

        //
    }
}
