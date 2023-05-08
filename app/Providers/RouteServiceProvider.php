<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider {

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot() {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map() {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes() {
        Route::group([
            'namespace' => $this->namespace,
            'middleware' => ['web'],
                ], function ($router) {
                    require base_path('routes/modules/auth.php');
                    require base_path('routes/modules/dashboard.php');
                    require base_path('routes/modules/user.php');
                    require base_path('routes/modules/userType.php');
                    require base_path('routes/modules/vehicleBrands.php');
                    require base_path('routes/modules/vehicleBrandModels.php');
                    require base_path('routes/modules/vehicleColour.php');
                    require base_path('routes/modules/vehicleTypes.php');
                    require base_path('routes/modules/vehicleFuelType.php');
                    require base_path('routes/modules/vehicles.php');
                    require base_path('routes/modules/trip.php');
                    require base_path('routes/modules/setting.php');
                    require base_path('routes/modules/cms_page.php');
                    require base_path('routes/modules/category.php');
                    require base_path('routes/modules/customer.php');
                    require base_path('routes/modules/subscriptionPlans.php');
                    require base_path('routes/modules/transactions.php');
                    require base_path('routes/modules/referralMasters.php');
                    require base_path('routes/modules/cabs.php');
                    require base_path('routes/modules/faqs.php');
                    require base_path('routes/modules/notifications.php');
                    require base_path('routes/modules/tripFare.php');
                    require base_path('routes/modules/websiteTripFare.php');
                    require base_path('routes/modules/admin.php');
                    require base_path('routes/modules/partner.php');
                    require base_path('routes/modules/travel.php');
                    require base_path('routes/modules/agent.php');
                    require base_path('routes/modules/driver.php');
                    require base_path('routes/modules/driver_cum_owner.php');
                    require base_path('routes/modules/offlineCustomer.php');
                    require base_path('routes/modules/register.php');
                    require base_path('routes/modules/parivarvahan.php');
                    require base_path('routes/modules/claimedRewards.php');
                    require base_path('routes/modules/subscriptionCoupons.php');
                    require base_path('routes/modules/subscriptionUsedCoupon.php');
                    require base_path('routes/modules/offers.php');
                    require base_path('routes/modules/polygonRecords.php');
                    require base_path('routes/modules/deleteuser.php');
                    require base_path('routes/modules/messages.php'); 
                    require base_path('routes/modules/blogs.php');
                    require base_path('routes/web.php');
                });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes() {
        Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
    }

}
