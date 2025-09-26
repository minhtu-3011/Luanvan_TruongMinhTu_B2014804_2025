<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Model::class => Policy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // // Định nghĩa Gate ví dụ:
        // Gate::define('modules', function (User $user, $permissionName) {
        //     dd($permissionName);
        //     dd($user);
        // });
        Gate::define('modules', function ($user, $permissionName) {
            $user = Auth::user();   // hoặc auth()->user()

            if ($user->publish == 0) return false;

            $permission = $user->user_catalogues->permissions;

            if ($permission->contains('canonical', $permissionName)) {
                return true;
            }

            return false;
        });
    }
}
