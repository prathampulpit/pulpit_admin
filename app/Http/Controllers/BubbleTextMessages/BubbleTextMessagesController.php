<?php

namespace App\Http\Controllers\BubbleTextMessages;

use App\Http\Controllers\Controller;
use App\Http\Requests\BubbleTextMessages\StoreBubbleTextMessage;
use App\Models\User;
use App\BubbleTextMessageDetails;
use App\Repositories\BubbleTextMessagesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Password;
use Session;
use DB;

class BubbleTextMessagesController extends Controller
{
    protected $bubbleTextMessagesRepository;

    public function __construct(
        BubbleTextMessagesRepository $bubbleTextMessagesRepository
    ) {
        $this->bubbleTextMessagesRepository = $bubbleTextMessagesRepository;
    }

    public function index()
    {
        return view('admin.modules.bubble_text_messages.index');
    }

    public function index_json(Request $request)
    {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->bubbleTextMessagesRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $cms = $this->bubbleTextMessagesRepository->getPanelUsers($request, $params);
        return $cms;
    }

    public function createEdit($panel, $id = null)
    {
        $params = [];

        $u_id_arr = array();
        $users = array();
        $item = null;
        if ($id) {
            $params = [];
            $params['id'] = $id;
            $params['response_type'] = "single";
            $item = $this->bubbleTextMessagesRepository->getByParams($params);

            $model = BubbleTextMessageDetails::where('bubble_text_message_id', '=', $id)->get();

            foreach ($model as $r) {
                $u_id_arr[] = $r['user_id'];
            }
            //$u_id_arr = explode(",", $u_id_arr);
            /* echo "<pre>";
            print_r($u_id_arr);
            exit; */
        }
        $users = User::where('role_id', '=', '0')->get();

        return view('admin.modules.bubble_text_messages.store', [
            'item' => $item,
            'users' => $users,
            'u_id_arr' => $u_id_arr,
            'id' => $id,
        ]);
    }

    public function store(StoreBubbleTextMessage $request)
    {
        $user = array();
        $user['id'] = $request->get('id', null);
        $user['bubble_text_en'] = $request->get('bubble_text_en');
        $user['bubble_text_sw'] = $request->get('bubble_text_sw');
        if (empty($request->get('id'))) {
            //$user['slug'] = $this->clean($page_name);
        }
        $user['expiry_date'] = $request->get('expiry_date');
        $user['created_at'] = date("Y-m-d H:i:s");
        $user['updated_at'] = date("Y-m-d H:i:s");

        $user_id_arr = $request->get('user_id');

        $insert = $this->bubbleTextMessagesRepository->save($user);

        if (!empty($request->get('id'))) {

            $message_id = $request->get('id');
            DB::table('bubble_text_message_details')->where('bubble_text_message_id', $message_id)->delete();
            foreach ($user_id_arr as $val) {
                $record = BubbleTextMessageDetails::Where('user_id', '=', $val)->first();
                if (empty($record)) {
                    $model = new BubbleTextMessageDetails();
                    $model->user_id = $val;
                    $model->bubble_text_message_id = $message_id;
                    $model->created_at = date("Y-m-d H:i:s");
                    $model->updated_at = date("Y-m-d H:i:s");
                    $model->save();
                } else {
                    $id = $record['id'];
                    $model = BubbleTextMessageDetails::find($id);
                    $model->bubble_text_message_id = $message_id;
                    $model->updated_at = date("Y-m-d H:i:s");
                    $model->save();
                }
            }

            $message = 'Record updated Successfully';
        } else {
            $message_id = $insert['id'];
            foreach ($user_id_arr as $val) {

                $record = BubbleTextMessageDetails::Where('user_id', '=', $val)->first();
                if (empty($record)) {
                    $model = new BubbleTextMessageDetails();
                    $model->user_id = $val;
                    $model->bubble_text_message_id = $message_id;
                    $model->created_at = date("Y-m-d H:i:s");
                    $model->updated_at = date("Y-m-d H:i:s");
                    $model->save();
                } else {
                    $id = $record['id'];
                    $model = BubbleTextMessageDetails::find($id);
                    $model->bubble_text_message_id = $message_id;
                    $model->updated_at = date("Y-m-d H:i:s");
                    $model->save();
                }
            }
            $message = 'Record added Successfully';
        }

        return redirect(route('admin.bubble_text_messages.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function show($panel, $id)
    {
        $record = BubbleTextMessageDetails::Where('bubble_text_message_id', '=', $id)->get();
        $u_id_arr = array();
        foreach ($record as $r) {
            $u_id_arr[] = $r['user_id'];
        }

        $users = User::whereIn('users.id', $u_id_arr)->join('user_accounts', 'users.id', '=', 'user_accounts.user_id')->get();
        /* echo "<pre>";
            print_r($users);
            exit; */

        $profile_path = config('custom.upload.user.profile');
        $file_path = env('S3_BUCKET_URL') . 'user';

        $document_path = config('custom.upload.user.document_path');
        $document_file_path = env('APP_URL') . '/storage/app/public/' . $document_path . "/";

        $document_permits_path = config('custom.upload.user.document_permits');
        $document_permits_file_path = env('APP_URL') . '/storage/app/public/' . $document_permits_path . "/";

        return view('admin.modules.bubble_text_messages.show', [
            'users' => $users, 'file_path' => $file_path, 'document_file_path' => $document_file_path, 'document_permits_file_path' => $document_permits_file_path
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
