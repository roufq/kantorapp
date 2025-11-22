<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LocationChangeRequest;
use App\Policies\AttendancePolicy;
use App\Policies\EmployeePolicy;
use App\Policies\LocationChangeRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Employee::class => EmployeePolicy::class,
        Attendance::class => AttendancePolicy::class,
        LocationChangeRequest::class => LocationChangeRequestPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        // Location-aware gates
        Gate::define('view-any-user', function ($authUser) {
            // Super Admin can view anyone
            return $authUser->hasRole('Super Admin');
        });

        Gate::define('manage-user', function ($authUser, \App\Models\User $target) {
            if ($authUser->hasRole('Super Admin')) {
                return true;
            }
            // Location Admin may manage users within the same location
            if ($authUser->hasRole('Admin Lokasi') && $authUser->location_id && $target->location_id) {
                return (int)$authUser->location_id === (int)$target->location_id;
            }
            return false;
        });

        Gate::define('update-user-location', function ($authUser, \App\Models\User $target) {
            if ($authUser->hasRole('Super Admin')) {
                return true;
            }
            // Location Admin can transfer users only from their own location
            if ($authUser->hasRole('Admin Lokasi') && $authUser->location_id && $target->location_id) {
                return (int)$authUser->location_id === (int)$target->location_id;
            }
            return false;
        });

        Gate::define('create-user', function ($authUser, ?int $locationId = null) {
            if ($authUser->hasRole('Super Admin')) {
                return true;
            }
            if ($authUser->hasRole('Admin Lokasi')) {
                return $locationId !== null && (int)$authUser->location_id === (int)$locationId;
            }
            return false;
        });
    }
}
