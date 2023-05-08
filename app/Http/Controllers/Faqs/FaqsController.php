<?php

namespace App\Http\Controllers\Faqs;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use Aws\Exception\AwsException;
use App\Models\Faqs;
use App\Models\Roles;
use App\Models\PortalActivities;
use App\Models\FaqMedia;
use App\Repositories\FaqsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;
use DB;

class FaqsController extends Controller
{
    protected $faqsRepository;

    public function __construct(
        FaqsRepository $faqsRepository
    ) {
        $this->faqsRepository = $faqsRepository;
    }

    public function index()
    {
        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if ($user_role == 'administrator') {
            return view('admin.modules.faqs.index');
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
            $total = $this->faqsRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->faqsRepository->getPanelUsers($request, $params);
        return $users;
    }

    public function createEdit($panel, $id = null)
    {
        $admin = Auth::user();
        $role_id = $admin['role_id'];

        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if ($user_role == 'administrator') {
            $params = [];

            $data = null;
            if ($id) {
                $params = [];
                $params['id'] = $id;
                $params['response_type'] = "single";
                $data = $this->faqsRepository->getByParams($params);
            }

            $faq_media = FaqMedia::where('faq_id', $id)->where('is_video', '1')->get();
            $faq_media_img = FaqMedia::where('faq_id', $id)->where('is_video', '0')->get();
            return view('admin.modules.faqs.store', [
                'data' => $data,
                'id' => $id,
                'user_role' => $user_role,
                'admin' => $admin,
                'faq_media' => $faq_media,
                'faq_media_img' => $faq_media_img
            ]);
        } else {
            abort(403);
        }
    }

    public function store(Request $request)
    {
        $youtube = $request->get('youtube');

        $user = array();
        $user['id'] = $request->get('id', null);
        $user['question'] = $request->get('question');
        $user['answer'] = $request->get('answer');
        if (empty($request->get('id'))) {
            $user['datetime'] = date("Y-m-d H:i:s");
        }
        $result = $this->faqsRepository->save($user);

        if (!empty($request->get('id'))) {

            $youtube_id = $request->get('youtube_id');
            $youtube = $request->get('youtube');

            if (!empty($youtube)) {
                $i = 0;
                foreach ($youtube as $v) {
                    if (!empty($v)) {

                        DB::table('faq_media')->where('id', '=', $youtube_id[$i])->delete();

                        DB::table('faq_media')->insert([
                            'faq_id' => $result->id,
                            'media_url' => $v,
                            'is_video' => 1
                        ]);

                        $i++;
                    }
                }
            }

            $youtube_id_img = $request->get('youtube_id_img');

            $image1 = $request->file('image1');
            if (!empty($image1)) {
                $image1_name = rand('111', '999') . time() . $image1->getClientOriginalName();
                $filePath1 = "/" . $image1_name;
                Storage::disk('s3')->put($filePath1, file_get_contents($image1));
                $img_name1 = env('S3_BUCKET_URL') . $filePath1;

                DB::table('faq_media')->where('id', '=', $youtube_id_img[0])->delete();

                DB::table('faq_media')->insert([
                    'faq_id' => $result->id,
                    'media_url' => $img_name1,
                    'is_video' => 0
                ]);
            }

            $image2 = $request->file('image2');
            if (!empty($image2)) {
                $image2_name = rand('111', '999') . time() . $image2->getClientOriginalName();
                $filePath2 = "/" . $image2_name;
                Storage::disk('s3')->put($filePath2, file_get_contents($image2));
                $img_name2 = env('S3_BUCKET_URL') . $filePath2;

                DB::table('faq_media')->where('id', '=', $youtube_id_img[1])->delete();

                DB::table('faq_media')->insert([
                    'faq_id' => $result->id,
                    'media_url' => $img_name2,
                    'is_video' => 0
                ]);
            }

            $image3 = $request->file('image3');
            if (!empty($image3)) {
                $image3_name = rand('111', '999') . time() . $image3->getClientOriginalName();
                $filePath3 = "/" . $image3_name;
                Storage::disk('s3')->put($filePath3, file_get_contents($image3));
                $img_name3 = env('S3_BUCKET_URL') . $filePath3;

                DB::table('faq_media')->where('id', '=', $youtube_id_img[2])->delete();

                DB::table('faq_media')->insert([
                    'faq_id' => $result->id,
                    'media_url' => $img_name3,
                    'is_video' => 0
                ]);
            }

            $image4 = $request->file('image4');
            if (!empty($image4)) {
                $image4_name = rand('111', '999') . time() . $image4->getClientOriginalName();
                $filePath4 = "/" . $image4_name;
                Storage::disk('s3')->put($filePath4, file_get_contents($image4));
                $img_name4 = env('S3_BUCKET_URL') . $filePath4;

                DB::table('faq_media')->where('id', '=', $youtube_id_img[3])->delete();

                DB::table('faq_media')->insert([
                    'faq_id' => $result->id,
                    'media_url' => $img_name4,
                    'is_video' => 0
                ]);
            }

            $image5 = $request->file('image5');
            if (!empty($image5)) {
                $image5_name = rand('111', '999') . time() . $image5->getClientOriginalName();
                $filePath5 = "/" . $image5_name;
                Storage::disk('s3')->put($filePath5, file_get_contents($image5));
                $img_name5 = env('S3_BUCKET_URL') . $filePath5;

                DB::table('faq_media')->where('id', '=', $youtube_id_img[4])->delete();

                DB::table('faq_media')->insert([
                    'faq_id' => $result->id,
                    'media_url' => $img_name5,
                    'is_video' => 0
                ]);
            }

            $message = 'Faq Updated Successfully';
        } else {
            if (!empty($youtube)) {
                foreach ($youtube as $v) {
                    if (!empty($v)) {
                        DB::table('faq_media')->insert([
                            'faq_id' => $result->id,
                            'media_url' => $v,
                            'is_video' => 1
                        ]);
                    }
                }
            }

            $image1 = $request->file('image1');
            if (!empty($image1)) {
                $image1_name = rand('111', '999') . time() . $image1->getClientOriginalName();
                $filePath1 = "/" . $image1_name;
                Storage::disk('s3')->put($filePath1, file_get_contents($image1));
                $img_name1 = env('S3_BUCKET_URL') . $filePath1;

                DB::table('faq_media')->insert([
                    'faq_id' => $result->id,
                    'media_url' => $img_name1,
                    'is_video' => 0
                ]);
            }

            $image2 = $request->file('image2');
            if (!empty($image2)) {
                $image2_name = rand('111', '999') . time() . $image2->getClientOriginalName();
                $filePath2 = "/" . $image2_name;
                Storage::disk('s3')->put($filePath2, file_get_contents($image2));
                $img_name2 = env('S3_BUCKET_URL') . $filePath2;

                DB::table('faq_media')->insert([
                    'faq_id' => $result->id,
                    'media_url' => $img_name2,
                    'is_video' => 0
                ]);
            }

            $image3 = $request->file('image3');
            if (!empty($image3)) {
                $image3_name = rand('111', '999') . time() . $image3->getClientOriginalName();
                $filePath3 = "/" . $image3_name;
                Storage::disk('s3')->put($filePath3, file_get_contents($image3));
                $img_name3 = env('S3_BUCKET_URL') . $filePath3;

                DB::table('faq_media')->insert([
                    'faq_id' => $result->id,
                    'media_url' => $img_name3,
                    'is_video' => 0
                ]);
            }

            $image4 = $request->file('image4');
            if (!empty($image4)) {
                $image4_name = rand('111', '999') . time() . $image4->getClientOriginalName();
                $filePath4 = "/" . $image4_name;
                Storage::disk('s3')->put($filePath4, file_get_contents($image4));
                $img_name4 = env('S3_BUCKET_URL') . $filePath4;

                DB::table('faq_media')->insert([
                    'faq_id' => $result->id,
                    'media_url' => $img_name4,
                    'is_video' => 0
                ]);
            }

            $image5 = $request->file('image5');
            if (!empty($image5)) {
                $image5_name = rand('111', '999') . time() . $image5->getClientOriginalName();
                $filePath5 = "/" . $image5_name;
                Storage::disk('s3')->put($filePath5, file_get_contents($image5));
                $img_name5 = env('S3_BUCKET_URL') . $filePath5;

                DB::table('faq_media')->insert([
                    'faq_id' => $result->id,
                    'media_url' => $img_name5,
                    'is_video' => 0
                ]);
            }

            $message = 'Faq Added Successfully';
        }

        return redirect(route('admin.faqs.index', ['panel' => Session::get('panel')]))->withMessage($message);
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
        $cabs = $this->faqsRepository->getByParams($params);

        $profile_path = config('custom.upload.user.profile');

        return view('admin.modules.faqs.show', [
            'faqs' => $faqs, 'user_role' => $this->user_role
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
        $result = $this->faqsRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id)
    {
        $result = $this->faqsRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        $result = $this->faqsRepository->updateStatus($id);
        return (int) $result;
    }
}
