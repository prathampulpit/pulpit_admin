<?php

namespace App\Http\Controllers\CmsPages;

use App\Http\Controllers\Controller;
use App\Http\Requests\CmsPages\StoreCmsPage;
use App\Models\User;
use App\Models\Roles;
use App\Repositories\CmsPagesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Password;
use Session;

class CmsPagesController extends Controller
{
    protected $cmsPagesRepository;

    public function __construct(
        CmsPagesRepository $cmsPagesRepository
    ) {
        $this->cmsPagesRepository = $cmsPagesRepository;
    }

    public function index()
    {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $user_role = $role['slug'];

        if ($user_role == 'administrator') {
            return view('admin.modules.cms_pages.index', ['user_role' => $user_role]);
        } else {
            abort(403);
        }
    }

    public function index_json(Request $request)
    {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->cmsPagesRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $cms = $this->cmsPagesRepository->getPanelUsers($request, $params);
        return $cms;
    }

    public function createEdit($panel, $id = null)
    {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $user_role = $role['slug'];

        if ($user_role == 'administrator') {

            $params = [];

            $item = null;
            if ($id) {
                $params = [];
                $params['id'] = $id;
                $params['response_type'] = "single";
                $item = $this->cmsPagesRepository->getByParams($params);
            }
            return view('admin.modules.cms_pages.store', [
                'item' => $item,
                'id' => $id,
            ]);
        } else {
            abort(403);
        }
    }

    public function store(StoreCmsPage $request)
    {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $user_role = $role['slug'];

        if ($user_role == 'administrator') {
            $user = array();
            $user['id'] = $request->get('id', null);
            $page_name = $request->get('page_name');
            $user['page_name'] = $page_name;
            if (empty($request->get('id'))) {
                $user['slug'] = $this->clean($page_name);
            }
            $user['content'] = $request->get('content');
            $user['content_sw'] = $request->get('content_sw');
            $this->cmsPagesRepository->save($user);

            if (!empty($request->get('id'))) {
                $message = 'Cms page updated Successfully';
            } else {
                $message = 'Cms page added Successfully';
            }

            return redirect(route('admin.cms_pages.index', ['panel' => Session::get('panel')]))->withMessage($message);
        } else {
            abort(403);
        }
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

    public function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return strtolower($string);
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
}