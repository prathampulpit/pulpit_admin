<?php
$user = Auth::user();
$id = $user['id'];

$role_id = $user['role_id'];
$role_id_arr = explode(',', $role_id);

$role = \App\Models\Roles::find($role_id);
$user_role = $role['slug'];
?>

<!-- Sidemenu -->
<div class="main-sidebar main-sidebar-sticky side-menu">
    <div class="sidemenu-logo">
        <a class="main-logo" href="{{ route('admin.dashboard.index', ['panel' => Session::get('panel')]) }}">
            <img src="{{ asset('assets/img/brand/logo-light.png') }}" class="header-brand-img desktop-logo" alt="logo">
            <img src="{{ asset('assets/img/brand/icon-light.png') }}" class="header-brand-img icon-logo" alt="logo">
            <img src="{{ asset('assets/img/brand/icon.png') }}" class="header-brand-img desktop-logo theme-logo"
                 alt="logo">
            <img src="{{ asset('assets/img/brand/icon.png') }}" class="header-brand-img icon-logo theme-logo"
                 alt="logo">
        </a>
    </div>
    <div class="main-sidebar-body">
        <ul class="nav">
            <!-- <li class="nav-header"><span class="nav-label">{{ @trans('sidebar.dashboard') }}</span></li> -->

            <li class="nav-item @if (Route::currentRouteName() == 'admin.dashboard.index' ||
                Route::currentRouteName() == 'admin.dashboard.userShow') active @endif">
                <a class="nav-link" href="{{ route('admin.dashboard.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-home sidemenu-icon"></i>
                    <span class="sidemenu-label">{{ @trans('sidebar.dashboard') }}</span>
                </a>
            </li>

            <!-- @if (Route::currentRouteName() == 'admin.userType.index')
class="nav-item active"
@else
class="nav-item"
@endif -->
            <!-- <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.userType.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-user sidemenu-icon"></i><span class="sidemenu-label">{{ @trans('sidebar.manage_user_type') }}</span>
                </a>
            </li> -->

            @if (in_array('4', $role_id_arr) || $user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.customers.index' ||
                Route::currentRouteName() == 'admin.userType.index' ||
                Route::currentRouteName() == 'admin.offlineCustomer.index') active show @endif">
                <a class="nav-link with-sub" href="#"><span class="shape1"></span><span
                        class="shape2"></span><i class="ti-user sidemenu-icon"></i><span
                        class="sidemenu-label">Riders</span></a>
                <ul class="nav-sub">

                    <li class="nav-sub-item  @if (Route::currentRouteName() == 'admin.customers.index' || Route::currentRouteName() == 'admin.customers.show') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.customers.index', ['panel' => Session::get('panel')]) }}">
                            Application
                        </a>
                    </li>
                    <li class="nav-sub-item  @if (Route::currentRouteName() == 'admin.offlineCustomer.index' ||
                        Route::currentRouteName() == 'admin.offlineCustomer.show') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.offlineCustomer.index', ['panel' => Session::get('panel')]) }}">
                            Offline Customer
                        </a>
                    </li>

                    <!-- <li class="nav-sub-item @if (Route::currentRouteName() == 'admin.userType.index' || Route::currentRouteName() == 'admin.userType.show') active @endif">
                    <a class="nav-sub-link" href="{{ route('admin.userType.index', ['panel' => Session::get('panel')]) }}">
                        {{ @trans('sidebar.manage_user_type') }}
                    </a>
                </li> -->
                </ul>
            </li>

            <li class="nav-item @if (Route::currentRouteName() == 'admin.driver.index' ||
                Route::currentRouteName() == 'admin.agent.index' ||
                Route::currentRouteName() == 'admin.travel.index' ||
                Route::currentRouteName() == 'admin.driver_cum_owner.index' ||
                Route::currentRouteName() == 'admin.driver_cum_owner.show' ||
                Route::currentRouteName() == 'admin.travel.show' ||
                Route::currentRouteName() == 'admin.agent.show' ||
                Route::currentRouteName() == 'admin.driver.show') active show @endif">
                <a class="nav-link with-sub" href="#"><span class="shape1"></span><span
                        class="shape2"></span><i class="ti-user sidemenu-icon"></i><span
                        class="sidemenu-label">Partners</span></a>
                <ul class="nav-sub">

                    <li class="nav-sub-item  @if (Route::currentRouteName() == 'admin.travel.index' || Route::currentRouteName() == 'admin.travel.show') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.travel.index', ['panel' => Session::get('panel')]) }}">
                            Travel
                        </a>
                    </li>
                    <li class="nav-sub-item  @if (Route::currentRouteName() == 'admin.agent.index' || Route::currentRouteName() == 'admin.agent.show') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.agent.index', ['panel' => Session::get('panel')]) }}">
                            Agent
                        </a>
                    </li>
                    <li class="nav-sub-item  @if (Route::currentRouteName() == 'admin.driver_cum_owner.index' ||
                        Route::currentRouteName() == 'admin.driver_cum_owner.show') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.driver_cum_owner.index', ['panel' => Session::get('panel')]) }}">
                            Driver Cum Owner
                        </a>
                    </li>

                    <li class="nav-sub-item  @if (Route::currentRouteName() == 'admin.driver.index' || Route::currentRouteName() == 'admin.driver.show') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.driver.index', ['panel' => Session::get('panel')]) }}">
                            Driver
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item @if (Route::currentRouteName() == 'admin.register.index' ||
                Route::currentRouteName() == 'admin.register.show' ||
                Route::currentRouteName() == 'admin.register.create') active @endif">
                <a class="nav-link" href="{{ route('admin.register.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-user sidemenu-icon"></i>
                    <span class="sidemenu-label">New Registers</span>
                </a>
            </li>
            @endif

            @if (in_array('5', $role_id_arr) || $user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.vehicles.index' ||
                Route::currentRouteName() == 'admin.vehicles.show' ||
                Route::currentRouteName() == 'admin.vehicles.create' ||
                Route::currentRouteName() == 'admin.vehicles.edit') active @endif">
                <a class="nav-link" href="{{ route('admin.vehicles.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-car sidemenu-icon"></i>
                    <span class="sidemenu-label">Vehicle</span>
                </a>
            </li>
            @endif

            @if ($user_role == 'administrator')
                <!-- <li class="nav-header"><span class="nav-label">Applications</span></li> -->
            <li class="nav-item @if (Route::currentRouteName() == 'admin.vehicleBrands.index' ||
                Route::currentRouteName() == 'admin.vehicleBrandModels.index' ||
                Route::currentRouteName() == 'admin.vehicleColour.index' ||
                Route::currentRouteName() == 'admin.vehicleTypes.index' ||
                Route::currentRouteName() == 'admin.vehicleFuelType.index' ||
                Route::currentRouteName() == 'admin.vehicleBrandModels.create' ||
                Route::currentRouteName() == 'admin.vehicleBrandModels.edit' ||
                Route::currentRouteName() == 'admin.vehicleBrands.create' ||
                Route::currentRouteName() == 'admin.vehicleBrands.edit' ||
                Route::currentRouteName() == 'admin.vehicleColour.create' ||
                Route::currentRouteName() == 'admin.vehicleColour.edit' ||
                Route::currentRouteName() == 'admin.vehicleTypes.create' ||
                Route::currentRouteName() == 'admin.vehicleTypes.edit' ||
                Route::currentRouteName() == 'admin.vehicleFuelType.create' ||
                Route::currentRouteName() == 'admin.vehicleFuelType.edit') active show @endif">
                <a class="nav-link with-sub" href="#"><span class="shape1"></span><span
                        class="shape2"></span><i class="ti-car sidemenu-icon"></i><span
                        class="sidemenu-label">Manage Vehicle</span></a>
                <ul class="nav-sub">

                    <!-- <li class="nav-sub-item @if (Route::currentRouteName() == 'admin.vehicles.index' || Route::currentRouteName() == 'admin.vehicles.show') active @endif">
                    <a class="nav-sub-link" href="{{ route('admin.vehicles.index', ['panel' => Session::get('panel')]) }}">
                        {{ @trans('sidebar.manage_vehicles') }}
                    </a>
                </li> -->

                    <li class="nav-sub-item @if (Route::currentRouteName() == 'admin.vehicleBrands.index' ||
                        Route::currentRouteName() == 'admin.vehicleBrands.show' ||
                        Route::currentRouteName() == 'admin.vehicleBrands.create' ||
                        Route::currentRouteName() == 'admin.vehicleBrands.edit') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.vehicleBrands.index', ['panel' => Session::get('panel')]) }}">
                            {{ @trans('sidebar.manage_vehicle_brands') }}
                        </a>
                    </li>

                    <li class="nav-sub-item @if (Route::currentRouteName() == 'admin.vehicleBrandModels.index' ||
                        Route::currentRouteName() == 'admin.vehicleBrandModels.show' ||
                        Route::currentRouteName() == 'admin.vehicleBrandModels.create' ||
                        Route::currentRouteName() == 'admin.vehicleBrandModels.edit') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.vehicleBrandModels.index', ['panel' => Session::get('panel')]) }}">
                            {{ @trans('sidebar.manage_vehicle_brand_models') }}
                        </a>
                    </li>

                    <li class="nav-sub-item @if (Route::currentRouteName() == 'admin.vehicleColour.index' ||
                        Route::currentRouteName() == 'admin.vehicleColour.show' ||
                        Route::currentRouteName() == 'admin.vehicleColour.create' ||
                        Route::currentRouteName() == 'admin.vehicleColour.edit') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.vehicleColour.index', ['panel' => Session::get('panel')]) }}">
                            {{ @trans('sidebar.manage_vehicle_colour') }}
                        </a>
                    </li>

                    <!-- @if (Route::currentRouteName() == 'admin.vehicleTypes.index')
class="nav-sub-item active"
@else
class="nav-sub-item"
@endif -->
                    <li class="nav-sub-item @if (Route::currentRouteName() == 'admin.vehicleTypes.index' ||
                        Route::currentRouteName() == 'admin.vehicleTypes.show' ||
                        Route::currentRouteName() == 'admin.vehicleTypes.create' ||
                        Route::currentRouteName() == 'admin.vehicleTypes.edit') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.vehicleTypes.index', ['panel' => Session::get('panel')]) }}">
                            {{ @trans('sidebar.manage_vehicle_types') }}
                        </a>
                    </li>

                    <!-- @if (Route::currentRouteName() == 'admin.vehicleFuelType.index')
class="nav-sub-item active"
@else
class="nav-sub-item"
@endif -->
                    <li class="nav-sub-item @if (Route::currentRouteName() == 'admin.vehicleFuelType.index' ||
                        Route::currentRouteName() == 'admin.vehicleFuelType.show' ||
                        Route::currentRouteName() == 'admin.vehicleFuelType.create' ||
                        Route::currentRouteName() == 'admin.vehicleFuelType.edit') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.vehicleFuelType.index', ['panel' => Session::get('panel')]) }}">
                            {{ @trans('sidebar.manage_vehicle_fuel_types') }}
                        </a>
                    </li>

                </ul>
            </li>
            @endif

            @if (in_array('6', $role_id_arr) || $user_role == 'administrator')
            <!-- <li
                class="nav-item @if (Route::currentRouteName() == 'admin.trip.index' || Route::currentRouteName() == 'admin.trip.create') || Route::currentRouteName()=='admin.trip.show') active @endif">
                <a class="nav-link" href="{{ route('admin.trip.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-map-alt sidemenu-icon"></i>
                    <span class="sidemenu-label">{{ @trans('sidebar.trip') }} </span>
                </a>
            </li> -->
            <li class="nav-item @if (Route::currentRouteName() == 'admin.trip.index' ||
                Route::currentRouteName() == 'admin.customer_trip.index' ||
                Route::currentRouteName() == 'admin.customer_trip.show' ||
                Route::currentRouteName() == 'admin.trip.show' ||
                Route::currentRouteName() == 'admin.trip.create') active show @endif">
                <a class="nav-link with-sub" href="#"><span class="shape1"></span><span
                        class="shape2"></span> <i class="ti-map-alt sidemenu-icon"></i> <span
                        class="sidemenu-label">Manage Trips</span></a>
                <ul class="nav-sub">

                    <li
                        class="nav-sub-item @if (Route::currentRouteName() == 'admin.trip.index' || Route::currentRouteName() == 'admin.trip.create') || Route::currentRouteName()=='admin.trip.show') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.trip.index', ['panel' => Session::get('panel')]) }}">
                           <!-- <span class="shape1"></span><span class="shape2"></span>
                           
                           <span class="sidemenu-label"> -->
                            Partner Trip
                            <!-- </span> -->
                        </a>
                    </li> 
                    <li
                        class="nav-sub-item @if(Route::currentRouteName()=='admin.customer_trip.index' || Route::currentRouteName()=='admin.customer_trip.create') || Route::currentRouteName()=='admin.customer_trip.show') active @endif">
                        <a class="nav-sub-link" href="{{ route('admin.customer_trip.index',['panel' => Session::get('panel')]) }}">
                            <!-- <span class="shape1"></span><span class="shape2"></span> -->
                            <!-- <i class="ti-map-alt sidemenu-icon"></i> -->
                            <!-- <span class="sidemenu-label"> -->
                            Customer Trip

                            <!-- </span> -->
                        </a>
                    </li>

                </ul>
            </li>
            @endif

            @if ($user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.subscriptionCoupons.index' ||
                Route::currentRouteName() == 'admin.subscriptionCoupons.show' ||
                Route::currentRouteName() == 'admin.subscriptionCoupons.create' ||
                Route::currentRouteName() == 'admin.subscriptionCoupons.edit') active @endif">
                <a class="nav-link"
                   href="{{ route('admin.subscriptionCoupons.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-money sidemenu-icon"></i>
                    <span class="sidemenu-label">Renew Subscription</span>
                </a>
            </li>
            @endif

            @if ($user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.subscriptionUsedCoupon.index' ||
                Route::currentRouteName() == 'admin.subscriptionUsedCoupon.show' ||
                Route::currentRouteName() == 'admin.subscriptionUsedCoupon.create' ||
                Route::currentRouteName() == 'admin.subscriptionUsedCoupon.edit') active @endif">
                <a class="nav-link"
                   href="{{ route('admin.subscriptionUsedCoupon.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-money sidemenu-icon"></i>
                    <span class="sidemenu-label">Used Renew Coupon</span>
                </a>
            </li>
            @endif

            @if ($user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.offers.index' ||
                Route::currentRouteName() == 'admin.offers.show' ||
                Route::currentRouteName() == 'admin.offers.create' ||
                Route::currentRouteName() == 'admin.offers.edit') active @endif">
                <a class="nav-link" href="{{ route('admin.offers.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-gift sidemenu-icon"></i>
                    <span class="sidemenu-label">Ad Offers</span>
                </a>
            </li>
            @endif

            @if ($user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.subscriptionPlans.index' ||
                Route::currentRouteName() == 'admin.subscriptionPlans.show' ||
                Route::currentRouteName() == 'admin.subscriptionPlans.create' ||
                Route::currentRouteName() == 'admin.subscriptionPlans.edit') active @endif">
                <a class="nav-link"
                   href="{{ route('admin.subscriptionPlans.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-money sidemenu-icon"></i>
                    <span class="sidemenu-label">{{ @trans('sidebar.manage_subscription_plans') }}</span>
                </a>
            </li>
            @endif

            @if ($user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.parivarvahan.index' ||
                Route::currentRouteName() == 'admin.parivarvahan.show' ||
                Route::currentRouteName() == 'admin.parivarvahan.create' ||
                Route::currentRouteName() == 'admin.parivarvahan.edit') active @endif">
                <a class="nav-link"
                   href="{{ route('admin.parivarvahan.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-car sidemenu-icon"></i>
                    <span class="sidemenu-label">Parivahan</span>
                </a>
            </li>
            @endif

            <!-- @if ($user_role == 'administrator')
<li
                class="nav-item @if (Route::currentRouteName() == 'admin.claimedRewards.index' ||
                    Route::currentRouteName() == 'admin.claimedRewards.show' ||
                    Route::currentRouteName() == 'admin.claimedRewards.create' ||
                    Route::currentRouteName() == 'admin.claimedRewards.edit') active @endif">
                <a class="nav-link" href="{{ route('admin.claimedRewards.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-money sidemenu-icon"></i>
                    <span class="sidemenu-label">Claimed Rewards</span>
                </a>
            </li>
@endif -->

            @if (in_array('8', $role_id_arr) || $user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.transactions.index' ||
                Route::currentRouteName() == 'admin.transactions.show') active @endif">
                <a class="nav-link"
                   href="{{ route('admin.transactions.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-menu-alt sidemenu-icon"></i>
                    <span class="sidemenu-label">{{ @trans('sidebar.manage_transactions') }}</span>
                </a>
            </li>
            @endif

            @if ($user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.referralMasters.index' ||
                Route::currentRouteName() == 'admin.referralMasters.show' ||
                Route::currentRouteName() == 'admin.referralMasters.create' ||
                Route::currentRouteName() == 'admin.referralMasters.edit') active @endif">
                <a class="nav-link"
                   href="{{ route('admin.referralMasters.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-menu-alt sidemenu-icon"></i>
                    <span class="sidemenu-label">Manage Referral</span>
                </a>
            </li>
            @endif

            @if (in_array('7', $role_id_arr) || $user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.cabs.index' || Route::currentRouteName() == 'admin.cabs.show') active @endif">
                <a class="nav-link" href="{{ route('admin.cabs.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-menu-alt sidemenu-icon"></i>
                    <span class="sidemenu-label">{{ @trans('sidebar.manage_cabs') }}</span>
                </a>
            </li>
            @endif

            @if ($user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.faqs.index' || Route::currentRouteName() == 'admin.faqs.show') active @endif">
                <a class="nav-link" href="{{ route('admin.faqs.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-menu-alt sidemenu-icon"></i>
                    <span class="sidemenu-label">{{ @trans('sidebar.manage_faqs') }}</span>
                </a>
            </li>
            @endif

            @if ($user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.notifications.index' ||
                Route::currentRouteName() == 'admin.notifications.show') active @endif">
                <a class="nav-link"
                   href="{{ route('admin.notifications.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-comment sidemenu-icon"></i>
                    <span class="sidemenu-label">{{ @trans('sidebar.manage_notifications') }}</span>
                </a>
            </li>
            @endif

            @if ($user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.polygonRecords.index' ||
                Route::currentRouteName() == 'admin.polygonRecords.show') active @endif">
                <a class="nav-link"
                   href="{{ route('admin.polygonRecords.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-map-alt sidemenu-icon"></i>
                    <span class="sidemenu-label">Polygon Records</span>
                </a>
            </li>
            @endif

            @if ($user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.tripFare.index' || Route::currentRouteName() == 'admin.tripFare.show') active @endif">
                <a class="nav-link"
                   href="{{ route('admin.tripFare.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-map-alt sidemenu-icon"></i>
                    <span class="sidemenu-label">{{ @trans('sidebar.manage_trip_fare') }}</span>
                </a>
            </li>

            <!--                <li class="nav-item @if (Route::currentRouteName() == 'admin.websiteTripFare.index' || Route::currentRouteName() == 'admin.websiteTripFare.show') active @endif">
                                <a class="nav-link"
                                    href="{{ route('admin.websiteTripFare.index', ['panel' => Session::get('panel')]) }}">
                                    <span class="shape1"></span><span class="shape2"></span>
                                    <i class="ti-map-alt sidemenu-icon"></i>
                                    <span class="sidemenu-label">{{ @trans('sidebar.website_trip_fare') }}</span>
                                </a>
                            </li>-->
            @endif

            @if ($user_role == 'administrator')
            <li class="nav-item @if (Route::currentRouteName() == 'admin.admin.index' || Route::currentRouteName() == 'admin.admin.show') active @endif">
                <a class="nav-link" href="{{ route('admin.admin.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-user sidemenu-icon"></i>
                    <span class="sidemenu-label">{{ @trans('sidebar.manage_admin') }}</span>
                </a>
            </li>

            <li class="nav-item @if (Route::currentRouteName() == 'admin.deleteuser.create') active @endif">
                <a class="nav-link"
                   href="{{ route('admin.deleteuser.create', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-user sidemenu-icon"></i>
                    <span class="sidemenu-label">Delete User</span>
                </a>
            </li>
            @endif
            @if ($user_role == 'administrator')  
            <li class="nav-item @if (Route::currentRouteName() == 'admin.websiteTripFare.index' || Route::currentRouteName() == 'admin.websiteTripFare.show') active @endif">
                <a class="nav-link with-sub" href="#"><span class="shape1"></span><span
                        class="shape2"></span><i class="ti-car sidemenu-icon"></i><span
                        class="sidemenu-label">{{ @trans('sidebar.website_trip_fare') }}</span></a>
                <ul class="nav-sub">

                    <li class="nav-sub-item @if (Route::currentRouteName() == 'admin.websiteTripFare.index') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.websiteTripFare.index', ['panel' => Session::get('panel')]) }}"> {{ @trans('tripFare.local') }}
                        </a>
                    </li>
                    <li class="nav-sub-item @if (Route::currentRouteName() == 'admin.websiteRentalTripFare.index') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.websiteRentalTripFare.index', ['panel' => Session::get('panel')]) }}"> {{ @trans('tripFare.rental') }}
                        </a>
                    </li>
                    <li class="nav-sub-item @if (Route::currentRouteName() == 'admin.websiteOutStationTripFare.index') active @endif">
                        <a class="nav-sub-link"
                           href="{{ route('admin.websiteOutStationTripFare.index', ['panel' => Session::get('panel')]) }}"> {{ @trans('tripFare.outstation') }}
                        </a>
                    </li> 
                </ul>
            </li>
            <li class="nav-item @if(Route::currentRouteName()=='admin.customer_trip.customer_trip_offline' || Route::currentRouteName()=='admin.customer_trip.customer_trip_offline_create') || Route::currentRouteName()=='admin.customer_trip.customer_trip_offline_show') active @endif">
                <a class="nav-link" href="{{ route('admin.customer_trip.customer_trip_offline',['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-user sidemenu-icon"></i>
                    <span class="sidemenu-label">Website Trip</span>
                </a>
            </li>
            <li class="nav-item nav-visitor-trip-customer">
                <a class="nav-link" href="{{ route('admin.customer_trip.customer_trip_offline_visitor',['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-user sidemenu-icon"></i>
                    <span class="sidemenu-label">Website Trip Visitor</span>
                </a>
            </li>

            <li class="nav-item @if (Route::currentRouteName() == 'admin.blogs.index' || Route::currentRouteName() == 'admin.blogs.show') active @endif">
                <a class="nav-link" href="{{ route('admin.blogs.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-user sidemenu-icon"></i>
                    <span class="sidemenu-label">Blogs</span>
                </a>
            </li>
            <li class="nav-item nav_messages">
                <a class="nav-link" href="{{ route('admin.messages.index', ['panel' => Session::get('panel')]) }}">
                    <span class="shape1"></span><span class="shape2"></span>
                    <i class="ti-user sidemenu-icon"></i>
                    <span class="sidemenu-label">Messages</span>
                </a>
            </li>
            @endif


            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout', ['panel' => Session::get('panel')]) }}"
                   data-toggle="modal" data-target="#modalLogoutConfirm"><span class="shape1"></span><span
                        class="shape2"></span><i class="ti-power-off sidemenu-icon"></i><span
                        class="sidemenu-label">{{ @trans('sidebar.sign_out') }}</span></a>
            </li>
        </ul>
    </div>
</div>
<!-- End Sidemenu -->
