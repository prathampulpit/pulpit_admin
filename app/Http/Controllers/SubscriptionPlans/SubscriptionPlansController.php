<?php

namespace App\Http\Controllers\SubscriptionPlans;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use App\Models\SubscriptionPlans;
use App\Models\VehicleTypes;
use App\Models\Roles;
use App\Models\PortalActivities;
use App\Models\PlanPremiumBenifitsMaster;
use App\Models\planPremiumBenifitsMapping;
use App\Repositories\SubscriptionPlansRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Password;
use Session;
use URL;
use DB;

class SubscriptionPlansController extends Controller
{
    protected $subscriptionPlansRepository;

    public function __construct(
        SubscriptionPlansRepository $subscriptionPlansRepository
    ) {
        $this->subscriptionPlansRepository = $subscriptionPlansRepository;
    }

    public function index()
    {
        return view('admin.modules.subscriptionPlans.index');
    }

    public function index_json(Request $request)
    {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->subscriptionPlansRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->subscriptionPlansRepository->getPanelUsers($request, $params);
        return $users;
    }

    public function createEdit($panel, $id = null)
    {
        $admin = Auth::user();
        $role_id = $admin['role_id'];

        $role = Roles::find($role_id);
        $user_role = $role['slug'];

        $params = [];

        $data = null;
        $map_ids = array();
        if ($id) {
            $params = [];
            $params['id'] = $id;
            $params['response_type'] = "single";
            $data = $this->subscriptionPlansRepository->getByParams($params);
            $planPremiumBenifitsMapping = planPremiumBenifitsMapping::where('subscription_plan_id', $id)->get();

            if (!empty($planPremiumBenifitsMapping)) {
                foreach ($planPremiumBenifitsMapping as $val) {
                    $map_ids[] = $val['plan_premium_benefits_plan_id'];
                }
            }
        }

        $vehicleTypes = VehicleTypes::where('status', '=', '1')->get();
        $planPremiumBenifitsMaster = PlanPremiumBenifitsMaster::all();
        //$planPremiumBenifitsMapping = planPremiumBenifitsMapping;

        /* echo "<pre>";
        print_r($vehicleBrands);
        exit; */
        return view('admin.modules.subscriptionPlans.store', [
            'data' => $data,
            'id' => $id,
            'user_role' => $user_role,
            'admin' => $admin,
            'vehicleTypes' => $vehicleTypes,
            'planPremiumBenifitsMaster' => $planPremiumBenifitsMaster,
            'map_ids' => $map_ids
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $role_id = $user['role_id'];
        $admin_id = $user['id'];
        \DB::enableQueryLog();
        $user = array();
        $user['id'] = $request->get('id', null);
        $user['name'] = $request->get('name');
        $user['is_agent'] = $request->get('is_agent');
        if ($user['is_agent'] == 0) {
            $user['vehicle_type_id'] = $request->get('vehicle_type_id');
        } else {
            $user['vehicle_type_id'] = '0';
        }
        $user['plan_validity'] = $request->get('plan_validity');
        $user['price'] = $request->get('price');

        $user['updated_at'] = date("Y-m-d H:i:s");
        if (empty($request->get('id'))) {
            $order = SubscriptionPlans::where('vehicle_type_id', $request->get('vehicle_type_id'))->orderBy('id', 'DESC')->first();
            $old_order = $order['order'] + 1;
            $user['order'] = $old_order;

            $user['created_at'] = date("Y-m-d H:i:s");
        }

        $result = $this->subscriptionPlansRepository->save($user);

        if (!empty($request->get('id'))) {

            $benifit_arr = $request->get('benifit_id');
            foreach ($benifit_arr as $val) {
                $check = planPremiumBenifitsMapping::where('subscription_plan_id', '=', $request->get('id'))->where('plan_premium_benefits_plan_id', '=', $val)->first();
                if (empty($check)) {
                    $add_benifits = new planPremiumBenifitsMapping();
                    $add_benifits->plan_premium_benefits_plan_id = $val;
                    $add_benifits->subscription_plan_id = $request->get('id');
                    $add_benifits->updated_at = date("Y-m-d H:i:s");
                    $add_benifits->save();
                }
            }

            $message = 'Subscription plan Updated Successfully';
        } else {
            $benifit_arr = $request->get('benifit_id');
            foreach ($benifit_arr as $val) {
                $add_benifits = new planPremiumBenifitsMapping();
                $add_benifits->plan_premium_benefits_plan_id = $val;
                $add_benifits->subscription_plan_id = $result->id;
                $add_benifits->created_at = date("Y-m-d H:i:s");
                $add_benifits->updated_at = date("Y-m-d H:i:s");
                $add_benifits->save();
            }
            $message = 'Subscription plan Added Successfully';
        }

        /* $log_update = new PortalActivities();
        $log_update->response_data = $message;
        $log_update->save(); */

        return redirect(route('admin.subscriptionPlans.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function show($panel, $id)
    {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['user_id'] = $id;
        $params['response_type'] = "single";
        $user = $this->subscriptionPlansRepository->getByParams($params);

        $profile_path = config('custom.upload.user.profile');

        return view('admin.modules.user.show', [
            'user' => $user, 'user_role' => $this->user_role
        ]);
    }

    /**
     * Change user status
     */
    public function changeStatus(Request $request)
    {
        $id = $request->get('id');
        $user = User::find($id);
        $user->user_status = $request->get('user_status');
        $user->save();

        $message = 'Status change successfully!';
        return redirect(route('admin.users.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
    }

    /**
     * Reset attempt
     */
    public function resetAttempt(Request $request)
    {
        $id = $request->get('user_id');
        $user = User::find($id);
        $user->login_attempt = $request->get('login_attempt');
        $user->save();

        $message = 'Attemp reset successfully!';
        echo "success";
    }

    /**
     * USSD Status
     */
    public function changeUssdStatus(Request $request)
    {
        $id = $request->get('user_id');
        $ussd_enable = $request->get('ussd_enable');
        $user = User::find($id);
        $user->ussd_enable = $ussd_enable;
        $user->save();

        $message = 'USSD status change successfully.';
        echo "success";
    }

    public function toggleStatus($panel, $id)
    {
        $result = $this->subscriptionPlansRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id)
    {
        $result = $this->subscriptionPlansRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        $result = $this->subscriptionPlansRepository->updateStatus($id);
        return (int) $result;
    }
}