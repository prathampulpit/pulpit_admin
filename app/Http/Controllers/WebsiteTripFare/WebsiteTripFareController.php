<?php

namespace App\Http\Controllers\WebsiteTripFare;

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
use App\Models\WebsiteLocalTripFare;
use App\Models\WebsiteLocalTripFareRanges;
use App\Models\PortalActivities;
use App\Models\PolygonRecords;
use App\Repositories\TripFareRepository;
use App\Repositories\WebsiteLocalTripFareRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;

class WebsiteTripFareController extends Controller {

    protected $WebsiteLocalTripFareRepository;

    public function __construct(
            WebsiteLocalTripFareRepository $WebsiteLocalTripFareRepository
    ) {
        $this->WebsiteLocalTripFareRepository = $WebsiteLocalTripFareRepository;
    }

    public function index() {
        return view('admin.modules.websiteTripFare.index');
    }

    public function index_json(Request $request) {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->WebsiteLocalTripFareRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->WebsiteLocalTripFareRepository->getPanelUsers($request, $params);
        return $users;
    }

    public function createEdit($panel, $id = null) {
        $admin = Auth::user();
        $role_id = $admin['role_id'];

        $role = Roles::find($role_id);
        $user_role = $role['slug'];

        $params = [];

        $data = null;
        $ranges = array();
        if ($id) {
            $params = [];
            $params['id'] = $id;
            $params['response_type'] = "single";
            $data = $this->WebsiteLocalTripFareRepository->getByParams($params);
            $ranges = WebsiteLocalTripFareRanges::where('website_local_trip_fare_id', $id)->orderBy('from_km_range', 'asc')->get();
        }

        $vehicleTypes = VehicleTypes::all();

        return view('admin.modules.websiteTripFare.store', [
            'data' => $data,
            'id' => $id,
            'vehicleTypes' => $vehicleTypes,
            'ranges' => $ranges
        ]);
    }

    public function store(Request $request) {

        $user = array();
        $user['id'] = $request->get('id', null);
        $user['vehicle_type_id'] = $request->get('vehicle_type_id');
        $user['base_fare'] = $request->get('base_fare');
        $user['per_km'] = $request->get('per_km');
        $user['advance_booking'] = $request->get('advance_booking');
        $user['description'] = $request->get('description');
        $user['gst'] = $request->get('gst');

        if (empty($request->get('id'))) {
            $user['created_at'] = date("Y-m-d H:i:s");
        }
        $user['updated_at'] = date("Y-m-d H:i:s");

        $data = $this->WebsiteLocalTripFareRepository->save($user);

        $from_km_range = $request->from_km_range;
        $to_km_range = $request->to_km_range;
        $per_km = $request->per_km_price;

        WebsiteLocalTripFareRanges::where('website_local_trip_fare_id', $data->id)->delete();
        foreach ($from_km_range as $key => $value) {
            $WebsiteLocalTripFareRanges = new WebsiteLocalTripFareRanges();
            $WebsiteLocalTripFareRanges->website_local_trip_fare_id = $data->id;
            $WebsiteLocalTripFareRanges->from_km_range = $from_km_range[$key];
            $WebsiteLocalTripFareRanges->to_km_range = $to_km_range[$key];
            $WebsiteLocalTripFareRanges->per_km = !empty($per_km[$key])? $per_km[$key] : 0;
            $WebsiteLocalTripFareRanges->created_at = date('Y-m-d H:i:s');
            $WebsiteLocalTripFareRanges->updated_at = date('Y-m-d H:i:s');
            $WebsiteLocalTripFareRanges->save();
        }

        if (!empty($request->get('id'))) {
            $message = 'Trip Fare Updated Successfully';
        } else {
            $message = 'Trip Fare Added Successfully';
        }

        return redirect(route('admin.websiteTripFare.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function show($panel, $id) {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['id'] = $id;
        $params['response_type'] = "single";
        $tripFare = $this->WebsiteLocalTripFareRepository->getByParams($params);
        $ranges = WebsiteLocalTripFareRanges::where('website_local_trip_fare_id', $id)->orderBy('from_km_range', 'asc')->get();

        $profile_path = config('custom.upload.user.profile');

        return view('admin.modules.websiteTripFare.show', [
            'tripFare' => $tripFare, 'user_role' => $this->user_role,'ranges'=>$ranges
        ]);
    }

    /**
     * Change user status
     */
    public function changeStatus(Request $request) {
        $id = $request->get('id');
        $user = User::find($id);
        $user->user_status = $request->get('user_status');
        $user->save();

        $message = 'Status change successfully!';
        return redirect(route('admin.websiteTripFare.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
    }

    /**
     * Reset attempt
     */
    public function resetAttempt(Request $request) {
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
    public function changeUssdStatus(Request $request) {
        $id = $request->get('user_id');
        $ussd_enable = $request->get('ussd_enable');
        $user = User::find($id);
        $user->ussd_enable = $ussd_enable;
        $user->save();

        $message = 'USSD status change successfully.';
        echo "success";
    }

    public function toggleStatus($panel, $id) {
        $result = $this->WebsiteLocalTripFareRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id) {
        $result = $this->WebsiteLocalTripFareRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id) {
        $result = WebsiteLocalTripFareRanges::where('website_local_trip_fare_id', $id)->delete();
        $result = WebsiteLocalTripFare::where('id', $id)->delete();
        //$result = $this->WebsiteLocalTripFareRepository->updateStatus($id);
        return (int) $result;
    }

}
