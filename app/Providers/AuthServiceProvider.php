<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Student;
use App\Models\Order;
use App\Policies\CartPolicy;
use App\Policies\OrderPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Cart is session-based, so we map the controller class
        \App\Http\Controllers\CartController::class => CartPolicy::class,
        // Order model policy
        Order::class => OrderPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register policies
        $this->registerPolicies();

        // Define Time-Based Order Restrictions Gates
        $this->defineOrderingGates();
    }

    /**
     * Define authorization gates for time-based ordering restrictions.
     */
    protected function defineOrderingGates(): void
    {
        /**
         * Gate: order-during-business-hours
         *
         * Operating Hours: 8:00 AM - 3:00 AM daily
         * This means orders are allowed from 8 AM to 3 AM next day (19 hours/day)
         * Closed: 3:00 AM - 8:00 AM (5 hours/day for kitchen prep)
         */
        Gate::define('order-during-business-hours', function (Student $student) {
            $currentHour = now()->hour;

            // Operating hours: 8 AM - 3 AM (8, 9, 10...23, 0, 1, 2)
            // Closed: 3 AM - 8 AM (3, 4, 5, 6, 7)
            $closedHours = [3, 4, 5, 6, 7];

            // Allow orders if NOT in closed hours
            return !in_array($currentHour, $closedHours);
        });

        /**
         * Gate: order-on-weekday
         *
         * Prevents ordering on weekends (optional restriction).
         * Remove or modify based on business requirements.
         */
        Gate::define('order-on-weekday', function (Student $student) {
            $dayOfWeek = now()->dayOfWeek;

            // 0 = Sunday, 6 = Saturday
            // Allow Monday (1) through Friday (5)
            return $dayOfWeek >= 1 && $dayOfWeek <= 5;
        });

        /**
         * Gate: order-not-on-holiday
         *
         * Prevents ordering during university holidays.
         * You can expand this with a database table of holidays.
         */
        Gate::define('order-not-on-holiday', function (Student $student) {
            // Example: Block ordering on specific dates
            $holidays = [
                '2025-12-25', // Christmas
                '2026-01-01', // New Year
                // Add more dates or fetch from database
            ];

            $today = now()->format('Y-m-d');

            return !in_array($today, $holidays);
        });

        /**
         * Gate: can-place-order
         *
         * Combined gate that checks all time-based restrictions.
         */
        Gate::define('can-place-order', function (Student $student) {
            // Check all time-based restrictions
            return Gate::check('order-during-business-hours')
                && Gate::check('order-not-on-holiday');
                // Uncomment if you want weekday restriction:
                // && Gate::check('order-on-weekday');
        });
    }
}
