<?php

namespace App\Http\Controllers\Offers;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use Aws\Exception\AwsException;
use App\Models\Offers;
use App\Models\Roles;
use App\Models\PortalActivities;
use App\Repositories\OffersRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;

class OffersController extends Controller {

    protected $offersRepository;

    public function __construct(
            OffersRepository $offersRepository
    ) {
        $this->offersRepository = $offersRepository;
    }

    public function index() {
        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("5", $role_id_arr) || $user_role == 'administrator') {
            return view('admin.modules.offers.index');
        } else {
            abort(403);
        }
    }

    public function index_json(Request $request) {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->offersRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->offersRepository->getPanelUsers($request, $params);
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
                $data = $this->offersRepository->getByParams($params);
            }

            return view('admin.modules.offers.store', [
                'data' => $data,
                'id' => $id,
                'user_role' => $user_role,
                'admin' => $admin
            ]);
        } else {
            abort(403);
        }
    }

    public function store(Request $request) {
        $user = Auth::user();
        $role_id = $user['role_id'];
        $admin_id = $user['id'];

        $user = array();
        $user['id'] = $request->get('id', null);
        $user['offer_type'] = $request->get('offer_type');
        $user['status'] = '1';
        $user['offer_for'] = $request->get('offer_for');

        $user['updated_at'] = date("Y-m-d H:i:s");
        if (empty($request->get('id'))) {
            $user['created_at'] = date("Y-m-d H:i:s");
        }
        if ($request->has('logo')) {

            if (!empty($user['id'])) { 
                $offerStoreObject = Offers::where('id', $user['id'])->first(); 
                if (!empty($offerStoreObject->offer_url)) {
                    $path = $offerStoreObject->offer_url; 
                    $path = str_replace(env('S3_BUCKET_URL')."/", '', $path); 
                    Storage::disk('s3')->delete($path);
                }
            }
 
            $file = $request->file('logo');
            $file_extention =  $file->getClientOriginalExtension();
             
            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = 'images/' . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $fullpath = env('S3_BUCKET_URL') . '/' . $filePath;
            $user['offer_url'] = $fullpath;
            $user['file_extention'] =$file_extention;
        }
        $this->offersRepository->save($user);

        if (!empty($request->get('id'))) {
            $message = 'Offer Updated Successfully';
        } else {
            $message = 'Offer Added Successfully';
        }
 
        return redirect(route('admin.offers.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function show($panel, $id) {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['user_id'] = $id;
        $params['response_type'] = "single";
        $user = $this->offersRepository->getByParams($params);

        $profile_path = config('custom.upload.user.profile');

        return view('admin.modules.user.show', [
            'user' => $user, 'user_role' => $this->user_role
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
        $result = $this->offersRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id) {
        $result = $this->offersRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id) {
        $result = $this->offersRepository->updateStatus($id);
        return (int) $result;
    }

}
