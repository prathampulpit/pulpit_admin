<?php

namespace App\Http\Controllers\WithdrawMoneyLabels;

use App\Http\Controllers\Controller;
use App\Http\Requests\WithdrawMoneyLabels\StoreWithdrawMoneyLabels;
use App\Models\User;
use App\Models\Roles;
use App\Models\AppconfigVersions;
use App\Repositories\WithdrawMoneyLabelsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Password;
use Session;

class WithdrawMoneyLabelsController extends Controller
{
    protected $withdrawMoneyLabelsRepository;

    public function __construct(
        WithdrawMoneyLabelsRepository $withdrawMoneyLabelsRepository
    ) {
        $this->withdrawMoneyLabelsRepository = $withdrawMoneyLabelsRepository;
    }

    public function index()
    {
        $biller_path = config('custom.upload.withdrawmoney');
        $file_path = env('APP_URL') . '/storage/app/public/' . $biller_path . "/";

        return view('admin.modules.withdrawmoneylabels.index', compact('file_path'));
    }

    public function index_json(Request $request)
    {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->withdrawMoneyLabelsRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $cms = $this->withdrawMoneyLabelsRepository->getPanelUsers($request, $params);
        return $cms;
    }

    public function createEdit($panel, $id = null)
    {
        $params = [];

        $item = null;
        if ($id) {
            $params = [];
            $params['id'] = $id;
            $params['response_type'] = "single";
            $item = $this->withdrawMoneyLabelsRepository->getByParams($params);
        }
        return view('admin.modules.withdrawmoneylabels.store', [
            'item' => $item,
            'id' => $id,
        ]);
    }

    public function store(StoreWithdrawMoneyLabels $request)
    {
        $validator = Validator::make($request->all(), [
            'icon' => 'mimes:jpg,jpeg,png'
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.withdrawmoneylabels.edit', ['panel' => Session::get('panel'), 'id' => $request->get('id', null)]))->withMessage("The icon must be a file of type: jpg, jpeg, png.");
        }

        $user = array();
        $user['id'] = $request->get('id', null);
        if ($request->has('icon')) {
            $file = $request->file('icon');
            $uploadPath = config('custom.upload.withdrawmoney');
            $path = $this->withdrawMoneyLabelsRepository->upload($file, $uploadPath);
            $user['icon'] = $path;
        }
        $user['display_name'] = $request->get('display_name');
        $user['display_name_sw'] = $request->get('display_name_sw');
        $user['sub_title'] = $request->get('sub_title');
        $user['sub_title_sw'] = $request->get('sub_title_sw');
        $this->withdrawMoneyLabelsRepository->save($user);

        $version = AppconfigVersions::find(3);
        $version->version = $version['version'] + 1;
        $version->save();

        if (!empty($request->get('id'))) {
            $message = 'Record updated Successfully';
        } else {
            $message = 'Record added Successfully';
        }

        return redirect(route('admin.withdrawmoneylabels.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function toggleStatus($panel, $id)
    {
        $version = AppconfigVersions::find(3);
        $version->version = $version['version'] + 1;
        $version->save();

        $result = $this->withdrawMoneyLabelsRepository->toggleStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        if (!Auth::user()->hasPermission('user-delete')) {
            abort(403);
        }

        $result = $this->withdrawMoneyLabelsRepository->delete($id);
        return (int) $result;
    }

    public function delete_icon()
    {
        $id = request('id');
        $result = $this->withdrawMoneyLabelsRepository->removeLogo($id);
        return (int) $result;
    }

    public function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return strtolower($string);
    }
}