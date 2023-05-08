<?php

namespace App\Http\Controllers\TripFare;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use Aws\Exception\AwsException;
use App\Models\TripFare;
use App\Models\Roles;
use App\Models\User;
use App\Models\Cities;
use App\Models\VehicleTypes;
use App\Models\PortalActivities;
use App\Models\PolygonRecords;
use App\Repositories\TripFareRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;

class TripFareController extends Controller
{
    protected $tripFareRepository;

    public function __construct(
        TripFareRepository $tripFareRepository
    ) {
        $this->tripFareRepository = $tripFareRepository;
    }

    public function index()
    {
        return view('admin.modules.tripFare.index');
    }

    public function index_json(Request $request)
    {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->tripFareRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->tripFareRepository->getPanelUsers($request, $params);
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
        if ($id) {
            $params = [];
            $params['id'] = $id;
            $params['response_type'] = "single";
            $data = $this->tripFareRepository->getByParams($params);
        }

        $city = Cities::all();
        $vehicleTypes = VehicleTypes::all();
        $polygon_records = PolygonRecords::where('status', '!=', '2')->get();
        return view('admin.modules.tripFare.store', [
            'data' => $data,
            'id' => $id,
            'city' => $city,
            'vehicleTypes' => $vehicleTypes,
            'polygon_records' => $polygon_records
        ]);
    }

    public function store(Request $request)
    {
        $user = array();
        $user['id'] = $request->get('id', null);
        $user['city_id'] = $request->get('city_id');
        $user['vehicle_type_id'] = $request->get('vehicle_type_id');
        $user['base_fare'] = $request->get('base_fare');
        $user['minimum_fare'] = $request->get('minimum_fare');
        $user['base_distance'] = $request->get('base_distance');
        $user['base_distance_fare'] = $request->get('base_distance_fare');
        $user['base_time'] = $request->get('base_time');
        $user['base_time_fare'] = $request->get('base_time_fare');
        $user['break_one_distance'] = $request->get('break_one_distance');
        $user['break_one_distance_fare'] = $request->get('break_one_distance_fare');
        $user['break_one_time'] = $request->get('break_one_time');
        $user['break_one_time_fare'] = $request->get('break_one_time_fare');
        $user['break_two_distance'] = $request->get('break_two_distance');
        $user['break_two_distance_fare'] = $request->get('break_two_distance_fare');
        $user['break_two_time'] = $request->get('break_two_time');
        $user['break_two_time_fare'] = $request->get('break_two_time_fare');
        $user['waiting_time'] = $request->get('waiting_time');
        $user['waiting_time_fare'] = $request->get('waiting_time_fare');
        $user['price_surge'] = $request->get('price_surge');
        $user['polygon_record_id'] = $request->get('polygon_record_id');
        $user['gst'] = $request->get('gst');

        if (empty($request->get('id'))) {
            $user['created_at'] = date("Y-m-d H:i:s");
        }
        $user['updated_at'] = date("Y-m-d H:i:s");
        $this->tripFareRepository->save($user);

        if (!empty($request->get('id'))) {
            $message = 'Trip Fare Updated Successfully';
        } else {
            $message = 'Trip Fare Added Successfully';
        }

        return redirect(route('admin.tripFare.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function show($panel, $id)
    {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['id'] = $id;
        $params['response_type'] = "single";
        $tripFare = $this->tripFareRepository->getByParams($params);

        $profile_path = config('custom.upload.user.profile');

        return view('admin.modules.tripFare.show', [
            'tripFare' => $tripFare, 'user_role' => $this->user_role
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
        return redirect(route('admin.tripFare.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
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
        $result = $this->tripFareRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id)
    {
        $result = $this->tripFareRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        $result = TripFare::where('id', $id)->delete();
        //$result = $this->tripFareRepository->updateStatus($id);
        return (int) $result;
    }
}