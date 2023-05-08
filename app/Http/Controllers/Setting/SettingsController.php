<?php

namespace App\Http\Controllers\Setting;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\StoreSetting;
use App\Models\Settings;
use App\Models\Roles;
use App\Models\AppconfigVersions;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Password;
use Session;

class SettingsController extends Controller
{
    protected $settingsRepository;

    public function __construct(
        SettingsRepository $settingsRepository
    ) {
        $this->settingsRepository = $settingsRepository;
    }

    public function index()
    {
        $profile_path = config('custom.upload.user.profile');
        $file_path = env('APP_URL') . '/storage/app/public/' . $profile_path . "/";

        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $user_role = $role['slug'];

        $params = [];
        $params['id'] = '1';
        $params['response_type'] = "single";
        $settings = $this->settingsRepository->getByParams($params);

        if ($user_role == 'administrator') {
            return view('admin.modules.setting.index', compact('settings', 'file_path', 'user_role'));
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
            $total = $this->userRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->userRepository->getPanelUsers($request, $params);
        return $users;
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
                $settings = $this->settingsRepository->getByParams($params);
            }

            $profile_path = config('custom.upload.user.profile');
            $file_path = env('APP_URL') . '/storage/app/public/' . $profile_path . "/";

            return view('admin.modules.setting.store', [
                'settings' => $settings, 'file_path' => $file_path, 'id' => $id,
            ]);
        } else {
            abort(403);
        }
    }

    public function show($panel, $id)
    {
        $params = [];
        $params['id'] = $id;
        $params['response_type'] = "single";
        $settings = $this->settingsRepository->getByParams($params);

        $profile_path = config('custom.upload.user.profile');
        $file_path = env('APP_URL') . '/storage/app/public/' . $profile_path . "/";

        return view('admin.modules.settings.show', [
            'settings' => $settings, 'file_path' => $file_path
        ]);
    }

    public function store(StoreSetting $request)
    {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $user_role = $role['slug'];
        if ($user_role == 'administrator') {
            $user = array();
            $user['id'] = $request->get('id');
            $user['digest_spike'] = $request->get('digest_spike');
            $user['graphs_months_interval'] = $request->get('graphs_months_interval');
            $user['minimum_funds_for_add_card'] = $request->get('minimum_funds_for_add_card');
            $user['bubble_text'] = $request->get('bubble_text');
            $user['bubble_text_sw'] = $request->get('bubble_text_sw');
            $user['refer_friend_text'] = $request->get('refer_friend_text');
            $user['ara_to_other_country'] = $request->get('ara_to_other_country');
            $user['agent_locator_distance'] = $request->get('agent_locator_distance');
            $user['total_otp_attempt'] = $request->get('total_otp_attempt');
            $user['otp_attempt_min_time'] = $request->get('otp_attempt_min_time');
            $user['refer_friend_error_message_en'] = $request->get('refer_friend_error_message_en');
            $user['refer_friend_error_message_sw'] = $request->get('refer_friend_error_message_sw');
            $user['maximum_referral_request_limit'] = $request->get('maximum_referral_request_limit');
            $user['maximum_referral_request_message_en'] = $request->get('maximum_referral_request_message_en');
            $user['maximum_referral_request_message_sw'] = $request->get('maximum_referral_request_message_sw');
            $user['maximum_share_number_limit'] = $request->get('maximum_share_number_limit');
            $user['referral_instruction_en'] = $request->get('referral_instruction_en');
            $user['referral_instruction_sw'] = $request->get('referral_instruction_sw');
            $user['referral_instruction_title_en'] = $request->get('referral_instruction_title_en');
            $user['referral_instruction_title_sw'] = $request->get('referral_instruction_title_sw');
            $user['referral_welcome_message_en'] = $request->get('referral_welcome_message_en');
            $user['referral_welcome_message_sw'] = $request->get('referral_welcome_message_sw');
            $user['referral_request_message_en'] = $request->get('referral_request_message_en');
            $user['referral_request_message_sw'] = $request->get('referral_request_message_sw');
            $user['referral_request_screen_title_en'] = $request->get('referral_request_screen_title_en');
            $user['referral_request_screen_title_sw'] = $request->get('referral_request_screen_title_sw');
            $user['referral_request_screen_content_en'] = $request->get('referral_request_screen_content_en');
            $user['referral_request_screen_content_sw'] = $request->get('referral_request_screen_content_sw');
            $user['contact_list_screen_message_en'] = $request->get('contact_list_screen_message_en');
            $user['contact_list_screen_message_sw'] = $request->get('contact_list_screen_message_sw');
            $user['refer_a_friend_success_message_en'] = $request->get('refer_a_friend_success_message_en');
            $user['refer_a_friend_success_message_sw'] = $request->get('refer_a_friend_success_message_sw');
            $user['max_no_of_physical_cards'] = $request->get('max_no_of_physical_cards');
            $user['otp_timer'] = $request->get('otp_timer');
            $this->settingsRepository->save($user);

            $version = AppconfigVersions::find(3);
            $version->version = $version['version'] + 1;
            $version->save();

            if (!empty($request->get('id'))) {
                $message = 'Settings Updated Successfully';
            } else {
                $message = 'Settings Added Successfully';
            }

            return redirect(route('admin.settings.index', ['panel' => Session::get('panel')]))->withMessage($message);
        } else {
            abort(403);
        }
    }

    /**
     * Referal Status
     */
    public function changeReferralStatus(Request $request)
    {
        $id = $request->get('id');
        $referral_enable = $request->get('referral_enable');
        $user = Settings::find($id);
        $user->referral_enable = $referral_enable;
        $user->save();

        $version = AppconfigVersions::find(3);
        $version->version = $version['version'] + 1;
        $version->save();

        $message = 'Status change successfully.';
        echo "success";
    }
}