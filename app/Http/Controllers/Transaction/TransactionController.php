<?php

namespace App\Http\Controllers\Transaction;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\Transaction\StoreUser;
use App\Models\User;
//use App\Repositories\Upload\UploadRepository;
use App\Repositories\TransactionsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Password;
use Session;

class TransactionController extends Controller
{
    protected $transactionsRepository;

    public function __construct(
        TransactionsRepository $transactionsRepository
    ) {
        $this->transactionsRepository = $transactionsRepository;
    }

    public function index()
    {
        $profile_path = config('custom.upload.user.profile');
        $file_path = env('APP_URL') . '/storage/app/public/' . $profile_path . "/";
        return view('admin.modules.transaction.index', compact('file_path'));
    }

    public function index_json(Request $request)
    {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->transactionsRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $params['trans_status'] = '1';
        $transactions = $this->transactionsRepository->getPanelUsers($request, $params);
        return $transactions;
    }

    public function createEdit($panel, $id = null)
    {
        $params = [];

        $user = null;
        if ($id) {
            $params = [];
            $params['id'] = $id;
            $user = $this->userRepository->getByParams($params);
        }
        return view('admin.modules.user.store', [
            'user' => $user,
            'id' => $id,
        ]);
    }

    public function store(StoreUser $request)
    {
        $user = array();
        $user['id'] = $request->get('id', null);
        $user['user_type'] = Auth::user()->user_type;
        $user['first_name'] = $request->get('first_name');
        $user['last_name'] = $request->get('last_name');
        $user['pin'] = "123456";
        $user['reward_balance'] = "0";
        $user['email'] = $request->get('email');
        $user['mobile'] = $request->get('mobile');

        if (!empty($request->get('password'))) {
            $user['password'] = bcrypt($request->get('password'));
        }

        if (empty($request->get('id'))) {
            $user['email_verified_at'] = Carbon::now();
        }

        $this->userRepository->save($user);

        if (!empty($request->get('id'))) {
            $message = 'User Updated Successfully';
        } else {
            $message = 'User Added Successfully';
        }

        return redirect(route('admin.users.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function show($panel, $id)
    {
        $params = [];
        $params['id'] = $id;
        $params['response_type'] = "single";
        $trans = $this->transactionsRepository->getByParams($params);
        /* echo "<pre>";
        print_r($trans);
        exit; */
        $profile_path = config('custom.upload.user.profile');
        $file_path = env('APP_URL') . '/storage/app/public/' . $profile_path . "/";

        $document_path = config('custom.upload.user.document_path');
        $document_file_path = env('APP_URL') . '/storage/app/public/' . $document_path . "/";

        $document_permits_path = config('custom.upload.user.document_permits');
        $document_permits_file_path = env('APP_URL') . '/storage/app/public/' . $document_permits_path . "/";

        return view('admin.modules.transaction.show', [
            'trans' => $trans, 'file_path' => $file_path, 'document_file_path' => $document_file_path, 'document_permits_file_path' => $document_permits_file_path
        ]);
    }

    public function destroy($panel, $id)
    {
        if (!Auth::user()->hasPermission('user-delete')) {
            abort(403);
        }

        $result = $this->userRepository->delete($id);
        return (int) $result;
    }
    public function showChangePasswordForm()
    {
        return view('admin.modules.user.change_password');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        if (!(\Hash::check($request->get('current_password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with('message', trans('Incorrect current password.'));
        }

        if (strcmp($request->get('current_password'), $request->get('password')) == 0) {
            //Current password and new password are same
            return redirect()->back()->with('message', trans('Password are not match.'));
        }
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));
        $user->password_changed_at = Carbon::now();
        $user->save();

        return redirect()->back()->with("message", trans("Password changed successfully !"));
    }

    public function showChangeProfileForm()
    {
        return view('admin.modules.user.change_profile');
    }

    public function changeUserProfile(ChangeProfileRequest $request)
    {
        $id = $request->get('id');

        $array['first_name'] = $request->get('first_name');
        $array['last_name'] = $request->get('last_name');
        $array['email'] = $request->get('email');
        $array['mobile'] = $request->get('mobile');

        $save = $this->userRepository->changeProfile($array, $id);
        if ($save) {
            session()->flash('message', 'Details edited successfully');
            return redirect()->back();
        }
    }

    public function uploadprofile(Request $request)
    {
        $profileUpdate = null;
        $uploadPath = config('custom.upload.user.profile');
        $base64_image = request('image');
        if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
            $data = substr($base64_image, strpos($base64_image, ',') + 1);

            $file = base64_decode($data);
            $path = $uploadPath . "/" . auth()->user()->id;

            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $fileName = $this->uploadRepository->uploadProfile($file, $path);

            if (auth()->user()->profile_image == "") {
                $profileUpdate = event(new ProfileUpdated('profilepic', 1.5));
            } else {
                $user = Auth::user();
                $user->profile_image = $fileName;
                $user->save();

                $profileUpdate = '';
            }

            return [
                'score' => $profileUpdate,
                'image' => auth()->user()->profile_image_formatted,
            ];
        }
    }
}
