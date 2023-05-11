<?php

namespace App\Http\Controllers\Vehicles;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use App\Models\User;
use App\Models\VehicleBrandModels;
use App\Models\VehicleBrands;
use App\Models\Vehicles;
use App\Models\States;
use App\Models\Cities;
use App\Models\VehicleTypes;
use App\Models\Roles;
use App\Models\PortalActivities;
use App\Models\VehiclePhotoMapping;
use App\Repositories\VehiclesRepository;
use App\VehicleBrands as AppVehicleBrands;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;
use Illuminate\Support\Facades\Response;
use DB;

class VehiclesController extends Controller {

    protected $vehiclesRepository;

    public function __construct(
            VehiclesRepository $vehiclesRepository
    ) {
        $this->vehiclesRepository = $vehiclesRepository;
    }

    public function vehicleBrandModels(Request $request) {
        $user_id = $request->vehicle_id;
        $vehicle = array();
        if ($user_id) {
            $vehicle['user_id'] = $user_id;
        } else {
            $vehicle['user_id'] = $request->user_id;
        }
        $vehicle['vehicle_number'] = $request->vehicle_number;
        $vehicle['brand_id'] = $request->brand_id;
        $vehicle['model_id'] = $request->model_name;
        $vehicle['vehicle_type_id'] = $request->vehicle_type;
        $vehicle['fuel_type_id'] = $request->vehicle_fuel_type;

        $insurance_exp_date = str_replace('/', '-', $request->insurance_exp_date);
        $vehicle['insurance_exp_date'] = $insurance_exp_date;

        $permit_exp_date = str_replace('/', '-', $request->permit_exp_date);
        $vehicle['permit_exp_date'] = $permit_exp_date;

        $fitness_exp_date = str_replace('/', '-', $request->fitness_exp_date);
        $vehicle['fitness_exp_date'] = $fitness_exp_date;

        $puc_exp_date = str_replace('/', '-', $request->puc_exp_date);
        $vehicle['puc_exp_date'] = $puc_exp_date;

        $vehicle['registration_year'] = $request->registration_year;
        if ($request->hasFile('rc_front_url')) {
            $file = $request->file('rc_front_url');
            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehicle['rc_front_url'] = env('S3_BUCKET_URL') . $filePath;
        } else {
            if ($user_id) {
                $vehicle['rc_front_url'] = "";
            }
        }
        if ($request->hasFile('rc_back_url')) {
            $file = $request->file('rc_back_url');
            $driving_license_front_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $driving_license_front_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehicle['rc_back_url'] = env('S3_BUCKET_URL') . $filePath;
        } else {
            if ($user_id) {
                $vehicle['rc_back_url'] = "";
            }
        }

        if ($request->hasFile('insurance_doc_url')) {
            $file = $request->file('insurance_doc_url');
            $driving_license_back_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $driving_license_back_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehicle['insurance_doc_url'] = env('S3_BUCKET_URL') . $filePath;
        } else {
            if ($user_id) {
                $vehicle['insurance_doc_url'] = "";
            }
        }

        if ($request->hasFile('permit_doc_url')) {
            $file = $request->file('permit_doc_url');
            $permit_doc_url_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $permit_doc_url_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehicle['permit_doc_url'] = env('S3_BUCKET_URL') . $filePath;
        } else {
            if ($user_id) {
                $vehicle['permit_doc_url'] = "";
            }
        }

        if ($request->hasFile('fitness_doc_url')) {
            $file = $request->file('fitness_doc_url');
            $pan_card = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $pan_card;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehicle['fitness_doc_url'] = env('S3_BUCKET_URL') . $filePath;
        } else {
            if ($user_id) {
                $vehicle['fitness_doc_url'] = "";
            }
        }

        if ($request->hasFile('puc_doc_url')) {
            $file = $request->file('puc_doc_url');
            $adhar_card = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $adhar_card;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehicle['puc_doc_url'] = env('S3_BUCKET_URL') . $filePath;
        } else {
            if ($user_id) {
                $vehicle['puc_doc_url'] = "";
            }
        }

        if ($request->hasFile('agreement_doc_url')) {
            $file = $request->file('agreement_doc_url');
            $cheque_book = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $cheque_book;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehicle['agreement_doc_url'] = env('S3_BUCKET_URL') . $filePath;
        } else {
            if ($user_id) {
                $vehicle['agreement_doc_url'] = "";
            }
        }
        if ($user_id) {
            $message = 'Vehicle Added Successfully...';
            $data = Vehicles::create($vehicle);
        } else {
            if ($request->id) {
                $data = Vehicles::where('id', $request->id)->update($vehicle);
                $data = Vehicles::where('id', $request->id)->first();
            } else {
                $data = Vehicles::where('user_id', $request->vehicle_id)->update($vehicle);
                $data = Vehicles::where('user_id', $request->vehicle_id)->first();
            }
            $message = 'Vehicle Updated Successfully...';
        }
        if ($request->hasFile('Front')) {
            $file = $request->file('Front');
            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $front = env('S3_BUCKET_URL') . $filePath;
            if ($request->id) {
                VehiclePhotoMapping::where([['vehicle_id', $request->id], ['vehicle_photos_view_master_id', 1]])->delete();
            }
            $vehicleMapping = new VehiclePhotoMapping;
            $vehicleMapping->vehicle_id = $data->id;
            $vehicleMapping->vehicle_photos_view_master_id = 1;
            $vehicleMapping->image_url = $front;
            $vehicleMapping->image_url_status = 1;
            $vehicleMapping->save();
        }

        if ($request->hasFile('Back')) {
            $file = $request->file('Back');
            $driving_license_front_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $driving_license_front_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $back = env('S3_BUCKET_URL') . $filePath;
            if ($request->id) {
                VehiclePhotoMapping::where([['vehicle_id', $request->id], ['vehicle_photos_view_master_id', 2]])->delete();
            }
            $vehicleMapping = new VehiclePhotoMapping;
            $vehicleMapping->vehicle_id = $data->id;
            $vehicleMapping->vehicle_photos_view_master_id = 2;
            $vehicleMapping->image_url = $back;
            $vehicleMapping->image_url_status = 1;
            $vehicleMapping->save();
        }

        if ($request->hasFile('Desktop')) {
            $file = $request->file('Desktop');
            $driving_license_back_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $driving_license_back_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $desktop = env('S3_BUCKET_URL') . $filePath;
            if ($request->id) {
                VehiclePhotoMapping::where([['vehicle_id', $request->id], ['vehicle_photos_view_master_id', 3]])->delete();
            }
            $vehicleMapping = new VehiclePhotoMapping;
            $vehicleMapping->vehicle_id = $data->id;
            $vehicleMapping->vehicle_photos_view_master_id = 3;
            $vehicleMapping->image_url = $desktop;
            $vehicleMapping->image_url_status = 1;
            $vehicleMapping->save();
        }

        if ($request->hasFile('Left')) {
            $file = $request->file('Left');
            $pan_card = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $pan_card;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $left = env('S3_BUCKET_URL') . $filePath;
            if ($request->id) {
                VehiclePhotoMapping::where([['vehicle_id', $request->id], ['vehicle_photos_view_master_id', 4]])->delete();
            }
            $vehicleMapping = new VehiclePhotoMapping;
            $vehicleMapping->vehicle_id = $data->id;
            $vehicleMapping->vehicle_photos_view_master_id = 4;
            $vehicleMapping->image_url = $left;
            $vehicleMapping->image_url_status = 1;
            $vehicleMapping->save();
        }

        if ($request->hasFile('Right')) {
            $file = $request->file('Right');
            $adhar_card = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $adhar_card;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $right = env('S3_BUCKET_URL') . $filePath;
            if ($request->id) {
                VehiclePhotoMapping::where([['vehicle_id', $request->id], ['vehicle_photos_view_master_id', 5]])->delete();
            }
            $vehicleMapping = new VehiclePhotoMapping;
            $vehicleMapping->vehicle_id = $data->id;
            $vehicleMapping->vehicle_photos_view_master_id = 5;
            $vehicleMapping->image_url = $right;
            $vehicleMapping->image_url_status = 1;
            $vehicleMapping->save();
        }

        if ($request->hasFile('Interior')) {
            $file = $request->file('Interior');
            $permit_doc_url_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $permit_doc_url_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $interior = env('S3_BUCKET_URL') . $filePath;
            if ($request->id) {
                VehiclePhotoMapping::where([['vehicle_id', $request->id], ['vehicle_photos_view_master_id', 6]])->delete();
            }
            $vehicleMapping = new VehiclePhotoMapping;
            $vehicleMapping->vehicle_id = $data->id;
            $vehicleMapping->vehicle_photos_view_master_id = 6;
            $vehicleMapping->image_url = $interior;
            $vehicleMapping->image_url_status = 1;
            $vehicleMapping->save();
        }

        if ($request->vehicle_id) {
            $userData = User::where('id', $request->vehicle_id)->first();
            if ($userData) {
                if ($userData->user_type_id == 2) {
                    return redirect(route('admin.agent.show', ['panel' => Session::get('panel'), 'id' => $request->vehicle_id]))->withMessage($message);
                } elseif ($userData->user_type_id == 3) {
                    return redirect(route('admin.travel.show', ['panel' => Session::get('panel'), 'id' => $request->vehicle_id]))->withMessage($message);
                } else {
                    return redirect(route('admin.register.show', ['panel' => Session::get('panel'), 'id' => $request->vehicle_id]))->withMessage($message);
                }
            } else {
                return redirect(route('admin.register.show', ['panel' => Session::get('panel'), 'id' => $request->vehicle_id]))->withMessage($message);
            }
        } else {
            return redirect(route('admin.vehicles.index', ['panel' => Session::get('panel')]))->withMessage($message);
        }
    }

    public function vehicleBrandModelsSelect(Request $request) {

        try {
            // dd($request->all());
            // $model1 = VehicleBrands::where('id',$request->brand_id)->first();
            $model = VehicleBrandModels::where('brand_id', $request->brand_id)->get();
            // dd($model);
            return Response::json(array('success' => true, 'model' => $model));
        } catch (\Throwable $th) {
            return Response::json(array('success' => 408));
        }
    }

    public function vehicleTypeBrandSelect(Request $request) {

        try {
            // dd($request->all());
            // $model1 = VehicleBrands::where('id',$request->brand_id)->first();
            $model = VehicleBrandModels::where('vehicle_type_id', $request->type_id)->join('vehicle_brands', 'vehicle_brand_models.brand_id', 'vehicle_brands.id')->select('vehicle_brands.id as id', 'vehicle_brands.name as name')->groupBy('id')->get();
            // dd($model);
            return Response::json(array('success' => true, 'model' => $model));
        } catch (\Throwable $th) {
            return Response::json(array('success' => 408));
        }
    }

    public function index($panel, Request $request, $param = null) {
        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];
        $states = States::all();
        $state_id = $request->state_id;
        $city_id = $request->city_id;
        $vehicle_type_id = $request->vehicle_type_id;
        if (isset($request->city_id)) {
            $state = States::where('id', $state_id)->first();
            if (!empty($state)) {
                $cities = Cities::where('stateCode', $state->isoCode)->get();
            } else {
                $cities = Cities::all();
            }
        } else {
            $cities = Cities::all();
        }
        $vehicle_types = VehicleTypes::all();
        if (in_array("5", $role_id_arr) || $user_role == 'administrator') {
            return view('admin.modules.vehicles.index', compact('param', 'vehicle_types', 'vehicle_type_id', 'state_id', 'states', 'cities', 'city_id'));
        } else {
            abort(403);
        }
    }

    public function index_json($panel, Request $request, $param = null) {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->vehiclesRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        if (!empty($param)) {
            $params['all_document_verify'] = $param;
        }
        $users = $this->vehiclesRepository->getPanelUsers($request, $params);
        return $users;
    }

    public function createEdit($panel, $id = null) {
        $admin = Auth::user();
        $role_id = $admin['role_id'];

        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("5", $role_id_arr) || $user_role == 'administrator') {
            $params = [];

            $data = null;
            if ($id) {
                $params = [];
                $params['id'] = $id;
                $params['response_type'] = "single";
                $data = $this->vehiclesRepository->getByParams($params);
            }
            $vehicleBrands = VehicleBrands::where('status', '=', '1')->get();
            $vehicleId = '';
            $vehicle = DB::table('vehicle_photos_view_master')
                    // ->join('vehicle_photos_view_master', 'vehicle_photos_view_master.id', '=', 'vehicle_photo_mapping.vehicle_photos_view_master_id')
                    // ->select('vehicle_photo_mapping.image_url', 'vehicle_photos_view_master.view_name', 'vehicle_photo_mapping.vehicle_photos_view_master_id','vehicle_photo_mapping.image_url_status', 'vehicle_photo_mapping.id')
                    // ->where('vehicle_photo_mapping.vehicle_id', '=', $id)
                    ->get();
            $vehicle_doc_type = [];
            if ($vehicle) {
                $vehicle_doc_type = DB::table('vehicle_photos_view_master')->get();
            }
            return view('admin.modules.vehicles.store', [
                'data' => $data,
                'id' => $id,
                'vehicle_id' => $vehicleId,
                'vehicle' => $vehicle,
                'vehicle_doc_type' => $vehicle_doc_type,
                'user_role' => $user_role,
                'admin' => $admin,
                'vehicleBrands' => $vehicleBrands
            ]);
        } else {
            abort(403);
        }
    }

    public function create($panel, $id = null) {
        $admin = Auth::user();
        $role_id = $admin['role_id'];

        $role_id_arr = explode(",", $role_id);
        $user_id = '';
        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("5", $role_id_arr) || $user_role == 'administrator') {
            $params = [];

            $data = null;
            if ($id) {
                $params = [];
                $params['id'] = $id;
                $params['vehicle_id'] = $id;
                $params['response_type'] = "single";
                $data = $this->vehiclesRepository->getByParams($params);
            }
            $vehicleBrands = VehicleBrands::where('status', '=', '1')->get();
            $vehicle = DB::table('vehicle_photo_mapping')
                    ->join('vehicle_photos_view_master', 'vehicle_photos_view_master.id', '=', 'vehicle_photo_mapping.vehicle_photos_view_master_id')
                    ->select('vehicle_photo_mapping.image_url', 'vehicle_photos_view_master.view_name', 'vehicle_photo_mapping.image_url_status', 'vehicle_photo_mapping.vehicle_photos_view_master_id', 'vehicle_photo_mapping.id')
                    ->where('vehicle_photo_mapping.vehicle_id', '=', $id)
                    ->get();
            return view('admin.modules.vehicles.store', [
                'data' => $data,
                'id' => $user_id,
                'vehicle_id' => $id,
                'vehicle' => $vehicle,
                'user_role' => $user_role,
                'admin' => $admin,
                'vehicleBrands' => $vehicleBrands
            ]);
        } else {
            abort(403);
        }
    }

    public function store(Request $request) {
        // dd($request->all());
        $user = Auth::user();
        $role_id = $user['role_id'];
        $admin_id = $user['id'];
        $vehicle = Vehicles::where('id', $request->get('vehicle_id'))->first();
        $user = array();
        $user['id'] = $request->get('id', null);
        if ($vehicle) {
            $user['user_id'] = $vehicle->user_id;
        }
        $user['name'] = $request->get('name');
        $user['brand_id'] = $request->get('brand_id');
        $user['updated_at'] = date("Y-m-d H:i:s");
        if (empty($request->get('id'))) {
            $user['created_at'] = date("Y-m-d H:i:s");
        }
        $this->vehiclesRepository->save($user);

        if (!empty($request->get('id'))) {
            $message = 'Vehicle Brand Models Updated Successfully';
        } else {
            $message = 'Vehicle Brand Models Added Successfully';
        }

        $log_update = new PortalActivities();
        $log_update->response_data = $message;
        $log_update->save();

        return redirect(route('admin.vehicles.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function show($panel, $id) {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['id'] = $id;
        $params['response_type'] = "single";
        $user = $this->vehiclesRepository->getByParams($params);
        //echo "<pre>"; print_r($user); exit;
        $profile_path = config('custom.upload.user.profile');

        $vehicle = DB::table('vehicle_photo_mapping')
                ->join('vehicle_photos_view_master', 'vehicle_photos_view_master.id', '=', 'vehicle_photo_mapping.vehicle_photos_view_master_id')
                ->select('vehicle_photo_mapping.image_url', 'view_name', 'vehicle_photo_mapping.image_url_status', 'vehicle_photo_mapping.id')
                ->where('vehicle_photo_mapping.vehicle_id', '=', $id)
                ->get();

        return view('admin.modules.vehicles.show', [
            'user' => $user, 'vehicle' => $vehicle, 'user_role' => $this->user_role
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
        return redirect(route('admin.users.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
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
        $result = $this->vehiclesRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id) {
        $result = $this->vehiclesRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id) {
        $result = $this->vehiclesRepository->updateStatus($id);
        return (int) $result;
    }

    //****************************************Manage**************************************************/

    public function manage(Request $request) {


        $id = $request->id;
        $user_id = $request->user_id;
        $admin = Auth::user();
        $role_id = $admin['role_id'];

        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        $params = [];

        $vehicle_number = $request->vehicle_number;

        if (!empty($request->vehicle_number)) {
            $vehicle = $this->getVehicleLicenceDetails($vehicle_number);
        } else {
            $vehicleMapping = DB::table('vehicle_photo_mapping')
                    ->join('vehicle_photos_view_master', 'vehicle_photos_view_master.id', '=', 'vehicle_photo_mapping.vehicle_photos_view_master_id')
                    ->select('vehicle_photo_mapping.image_url', 'vehicle_photos_view_master.view_name', 'vehicle_photo_mapping.image_url_status', 'vehicle_photo_mapping.vehicle_photos_view_master_id', 'vehicle_photo_mapping.id')
                    ->where('vehicle_photo_mapping.vehicle_id', '=', $id)
                    ->get();
            $vehicle = Vehicles::find($id);
            $vehicle_number = !empty($vehicle->vehicle_number) ? $vehicle->vehicle_number : '';
            $vehicle = array('status' => 'success', "data" => $vehicle, "message" => "Record found");
        }


        $vehicleBrands = VehicleBrands::where('status', '=', '1')->get();

        return view('admin.modules.vehicles.manage', [
            'user_id' => $user_id,
            'id' => $id,
            'vehicle' => $vehicle,
            'vehicle_number' => $vehicle_number,
            'user_role' => $user_role,
            'admin' => $admin,
            'vehicleBrands' => $vehicleBrands
        ]);
    }

    public function getVehicleLicenceDetails($vehicle_number) {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://vehicle-rc-information.p.rapidapi.com',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"VehicleNumber":"' . $vehicle_number . '"}',
            CURLOPT_HTTPHEADER => array(
                'X-RapidAPI-Host: vehicle-rc-information.p.rapidapi.com',
                'X-RapidAPI-Key: 333ee70dc4msh1fef8639ef8c6d9p144d24jsnc55290059f47',
                'Content-Type: application/json'
            ),
        ));

        $json_response = curl_exec($curl);
        curl_close($curl);

        //  $json_response = '{"result":{"npermit_issued_by":null,"variant":null,"current_address":"VILL BHURA, TEH JHADOL, , Udaipur, Rajasthan, 313001","permit_no":"RJ2021-CC-7519B","status":"id_found","is_financed":null,"noc_details":null,"father_name":"GOVIND SINGH  RAJPUT","noc_valid_upto":null,"registration_date":"2012-08-17","colour":"SUPER WHITE","puc_number":"D38RJ27110967","registered_place":"UDAIPUR RTO","seating_capacity":"7","mv_tax_upto":"2013-02-16","norms_type":"EURO 3","body_type":"SALOON","owner_serial_number":"3","wheelbase":"2450","fitness_upto":"2024-04-25","financer":"","fuel_type":"DIESEL","puc_valid_upto":"2023-09-14","status_verification":null,"npermit_no":null,"npermit_upto":null,"manufacturer_model":"INNOVA 2.5G","permit_issue_date":null,"state":null,"cubic_capacity":"2494","vehicle_class":"LPV","insurance_validity":"2023-08-28","noc_issue_date":null,"owner_name":"SOHAN SINGH RAJPUT","manufacturer":"TOYOTA KIRLOSKAR MOTOR PVT LTD","vehicle_category":"LPV","permanent_address":"VILL BHURA, TEH JHADOL, , Udaipur, Rajasthan, 313001","insurance_name":"Oriental Insurance Co. Ltd.","owner_mobile_no":"","unladden_weight":"1655","chassis_number":"MBJ11JV4007347972~0712","engine_number":"2KDU080488","blacklist_status":null,"permit_validity_upto":"2026-12-08","permit_validity_from":null,"status_verfy_date":"2023-05-08","masked_name":false,"insurance_policy_no":"242594\/31\/2023\/394","m_y_manufacturing":"2012-07","number_of_cylinder":"4","gross_vehicle_weight":"2300","registration_number":"RJ27TA4151","sleeper_capacity":"0","standing_capacity":"0","status_message":null,"permit_type":"TEMPORARY PERMIT","noc_status":null}}';
        $data = json_decode($json_response);
        $results = !empty($data->result) ? $data->result : '';
        $status = !empty($data->error) ? 'error' : 'success';
        $message = !empty($data->message) ? $data->message : '';
        return array('status' => $status, "data" => $results, "message" => $message);
    }

    public function save(Request $request) {

        $user_id = $request->user_id;
        $id = $request->id;
        $vehicle_id = $request->vehicle_id;
        $vehicle_number = $request->vehicle_number;
        $owner_name = $request->owner_name;
        $owner_mobile_no = $request->owner_mobile_no;
        $permit_no = $request->permit_no;
        $puc_number = $request->puc_number;
        $vehicle_type = $request->vehicle_type;
        $brand_id = $request->brand_id;
        $model_name = $request->model_name;
        $vehicle_fuel_type = $request->vehicle_fuel_type;
        $insurance_exp_date = $request->insurance_exp_date;
        $permit_exp_date = $request->permit_exp_date;
        $fitness_exp_date = $request->fitness_exp_date;
        $puc_exp_date = $request->puc_exp_date;
        $registration_year = $request->registration_year;
        $city = $request->city;
        $state = $request->state;
        $pincode = $request->pincode;
        $permantent_address = $request->street_address;
        $current_address = $request->current_address;
        if (empty($id)) {
            $vehiclesObject = new Vehicles();
        } else {
            $vehiclesObject = Vehicles::find($id);
        }
        $vehiclesObject->user_id = $user_id;
        $vehiclesObject->vehicle_number = $vehicle_number;
        $vehiclesObject->owner_name = $owner_name;
        $vehiclesObject->vehicle_type_id = $vehicle_type;
        $vehiclesObject->brand_id = $brand_id;
        $vehiclesObject->model_id = $model_name;
        $vehiclesObject->fuel_type_id = $vehicle_fuel_type;
        $vehiclesObject->insurance_exp_date = $insurance_exp_date;
        $vehiclesObject->permit_exp_date = $permit_exp_date;
        $vehiclesObject->fitness_exp_date = $fitness_exp_date;
        $vehiclesObject->puc_exp_date = $puc_exp_date;
        $vehiclesObject->registration_year = $registration_year;
        $vehiclesObject->state = $state;
        $vehiclesObject->city = $city;

        $vehiclesObject->owner_mobile_no = $owner_mobile_no;
        $vehiclesObject->permit_no = $permit_no;
        $vehiclesObject->puc_number = $puc_number;
        $vehiclesObject->pincode = $pincode;
        $vehiclesObject->permantent_address = $permantent_address;
        $vehiclesObject->current_address = $current_address;

        if ($request->hasFile('rc_front_url')) {
            $file = $request->file('rc_front_url');
            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehiclesObject->rc_front_url = env('S3_BUCKET_URL') . $filePath;
        }
        if ($request->hasFile('rc_back_url')) {
            $file = $request->file('rc_back_url');
            $driving_license_front_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $driving_license_front_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehiclesObject->rc_back_url = env('S3_BUCKET_URL') . $filePath;
        }

        $vehiclesObject->save();

        $id = $vehiclesObject->id;
        if ($request->hasFile('Front')) {
            $file = $request->file('Front');
            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $front = env('S3_BUCKET_URL') . $filePath;

            if ($id) {
                $vehicle_image = VehiclePhotoMapping::where([['vehicle_id', $id], ['vehicle_photos_view_master_id', 1]])->first();
                if (!empty($vehicle_image)) {
                    if (!empty($vehicle_image->image_url)) {
                        echo $path = $vehicle_image->image_url;
                        $path = str_replace(env('S3_BUCKET_URL') . "/", '', $path);
                        Storage::disk('s3')->delete($path);
                    }
                }
                VehiclePhotoMapping::where([['vehicle_id', $id], ['vehicle_photos_view_master_id', 1]])->delete();
            }
            $vehicleMapping = new VehiclePhotoMapping;
            $vehicleMapping->vehicle_id = $id;
            $vehicleMapping->vehicle_photos_view_master_id = 1;
            $vehicleMapping->image_url = $front;
            $vehicleMapping->image_url_status = 1;
            $vehicleMapping->save();
        }

        if ($request->hasFile('Back')) {
            $file = $request->file('Back');
            $driving_license_front_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $driving_license_front_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $back = env('S3_BUCKET_URL') . $filePath;
            if ($id) {
                $vehicle_image = VehiclePhotoMapping::where([['vehicle_id', $id], ['vehicle_photos_view_master_id', 2]])->first();
                if (!empty($vehicle_image)) {
                    if (!empty($vehicle_image->image_url)) {
                        echo $path = $vehicle_image->image_url;
                        $path = str_replace(env('S3_BUCKET_URL') . "/", '', $path);
                        Storage::disk('s3')->delete($path);
                    }
                }
                VehiclePhotoMapping::where([['vehicle_id', $id], ['vehicle_photos_view_master_id', 2]])->delete();
            }
            $vehicleMapping = new VehiclePhotoMapping;
            $vehicleMapping->vehicle_id = $id;
            $vehicleMapping->vehicle_photos_view_master_id = 2;
            $vehicleMapping->image_url = $back;
            $vehicleMapping->image_url_status = 1;
            $vehicleMapping->save();
        }

        if ($request->hasFile('Desktop')) {
            $file = $request->file('Desktop');
            $driving_license_back_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $driving_license_back_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $desktop = env('S3_BUCKET_URL') . $filePath;
            if ($id) {
                $vehicle_image = VehiclePhotoMapping::where([['vehicle_id', $id], ['vehicle_photos_view_master_id', 3]])->first();
                if (!empty($vehicle_image)) {
                    if (!empty($vehicle_image->image_url)) {
                        echo $path = $vehicle_image->image_url;
                        $path = str_replace(env('S3_BUCKET_URL') . "/", '', $path);
                        Storage::disk('s3')->delete($path);
                    }
                }
                VehiclePhotoMapping::where([['vehicle_id', $id], ['vehicle_photos_view_master_id', 3]])->delete();
            }
            $vehicleMapping = new VehiclePhotoMapping;
            $vehicleMapping->vehicle_id = $id;
            $vehicleMapping->vehicle_photos_view_master_id = 3;
            $vehicleMapping->image_url = $desktop;
            $vehicleMapping->image_url_status = 1;
            $vehicleMapping->save();
        }

        if ($request->hasFile('Left')) {
            $file = $request->file('Left');
            $pan_card = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $pan_card;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $left = env('S3_BUCKET_URL') . $filePath;
            if ($id) {
                $vehicle_image = VehiclePhotoMapping::where([['vehicle_id', $id], ['vehicle_photos_view_master_id', 4]])->first();
                if (!empty($vehicle_image)) {
                    if (!empty($vehicle_image->image_url)) {
                        $path = $vehicle_image->image_url;
                        $path = str_replace(env('S3_BUCKET_URL') . "/", '', $path);
                        Storage::disk('s3')->delete($path);
                    }
                }
                $vehicle_image = VehiclePhotoMapping::where([['vehicle_id', $id], ['vehicle_photos_view_master_id', 4]])->delete();
            }
            $vehicleMapping = new VehiclePhotoMapping;
            $vehicleMapping->vehicle_id = $id;
            $vehicleMapping->vehicle_photos_view_master_id = 4;
            $vehicleMapping->image_url = $left;
            $vehicleMapping->image_url_status = 1;
            $vehicleMapping->save();
        }

        if ($request->hasFile('Right')) {
            $file = $request->file('Right');
            $adhar_card = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $adhar_card;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $right = env('S3_BUCKET_URL') . $filePath;
            if ($id) {
                $vehicle_image = VehiclePhotoMapping::where([['vehicle_id', $id], ['vehicle_photos_view_master_id', 5]])->first();
                if (!empty($vehicle_image)) {
                    if (!empty($vehicle_image->image_url)) {
                        echo $path = $vehicle_image->image_url;
                        $path = str_replace(env('S3_BUCKET_URL') . "/", '', $path);
                        Storage::disk('s3')->delete($path);
                    }
                }
                VehiclePhotoMapping::where([['vehicle_id', $id], ['vehicle_photos_view_master_id', 5]])->delete();
            }
            $vehicleMapping = new VehiclePhotoMapping;
            $vehicleMapping->vehicle_id = $id;
            $vehicleMapping->vehicle_photos_view_master_id = 5;
            $vehicleMapping->image_url = $right;
            $vehicleMapping->image_url_status = 1;
            $vehicleMapping->save();
        }

        if ($request->hasFile('Interior')) {
            $file = $request->file('Interior');
            $permit_doc_url_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $permit_doc_url_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $interior = env('S3_BUCKET_URL') . $filePath;
            if ($id) {
                $vehicle_image = VehiclePhotoMapping::where([['vehicle_id', $id], ['vehicle_photos_view_master_id', 6]])->first();
                if (!empty($vehicle_image)) {
                    if (!empty($vehicle_image->image_url)) {
                        echo $path = $vehicle_image->image_url;
                        $path = str_replace(env('S3_BUCKET_URL') . "/", '', $path);
                        Storage::disk('s3')->delete($path);
                    }
                }
                VehiclePhotoMapping::where([['vehicle_id', $id], ['vehicle_photos_view_master_id', 6]])->delete();
            }
            $vehicleMapping = new VehiclePhotoMapping;
            $vehicleMapping->vehicle_id = $id;
            $vehicleMapping->vehicle_photos_view_master_id = 6;
            $vehicleMapping->image_url = $interior;
            $vehicleMapping->image_url_status = 1;
            $vehicleMapping->save();
        }

        return redirect('/super-admin/travel/show_v2/' . $user_id)->withMessage("Record saved successfully");
    }

}
