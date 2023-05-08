<?php

namespace App\Http\Controllers\AddMoneyLabels;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddMoneyLabels\StoreAddMoneyLabels;
use App\Models\User;
use App\Models\Roles;
use App\Models\AppconfigVersions;
use App\Repositories\AddMoneyLabelsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Password;
use Session;

class AddMoneyLabelsController extends Controller
{
    protected $addMoneyLabelsRepository;

    public function __construct(
        AddMoneyLabelsRepository $addMoneyLabelsRepository
    ) {
        $this->addMoneyLabelsRepository = $addMoneyLabelsRepository;
    }

    public function index()
    {
        $biller_path = config('custom.upload.addmoney');
        $file_path = env('APP_URL') . '/storage/app/public/' . $biller_path . "/";

        return view('admin.modules.addmoneylabels.index', compact('file_path'));
    }

    public function index_json(Request $request)
    {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->addMoneyLabelsRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $cms = $this->addMoneyLabelsRepository->getPanelUsers($request, $params);
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
            $item = $this->addMoneyLabelsRepository->getByParams($params);
        }
        return view('admin.modules.addmoneylabels.store', [
            'item' => $item,
            'id' => $id,
        ]);
    }

    public function store(StoreAddMoneyLabels $request)
    {
        $validator = Validator::make($request->all(), [
            'icon' => 'mimes:jpg,jpeg,png'
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.addmoneylabels.edit', ['panel' => Session::get('panel'), 'id' => $request->get('id', null)]))->withMessage("The icon must be a file of type: jpg, jpeg, png.");
        }

        $user = array();
        $user['id'] = $request->get('id', null);
        if ($request->has('icon')) {
            $file = $request->file('icon');
            $uploadPath = config('custom.upload.addmoney');
            $path = $this->addMoneyLabelsRepository->upload($file, $uploadPath);
            $user['icon'] = $path;
        }
        $user['display_name'] = $request->get('display_name');
        $user['display_name_sw'] = $request->get('display_name_sw');
        $user['sub_title'] = $request->get('sub_title');
        $user['sub_title_sw'] = $request->get('sub_title_sw');
        $this->addMoneyLabelsRepository->save($user);

        $version = AppconfigVersions::find(3);
        $version->version = $version['version'] + 1;
        $version->save();

        if (!empty($request->get('id'))) {
            $message = 'Record updated Successfully';
        } else {
            $message = 'Record added Successfully';
        }

        return redirect(route('admin.addmoneylabels.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function toggleStatus($panel, $id)
    {
        $version = AppconfigVersions::find(3);
        $version->version = $version['version'] + 1;
        $version->save();

        $result = $this->addMoneyLabelsRepository->toggleStatus($id);
        return (int) $result;
    }

    public function delete_icon()
    {
        $id = request('id');
        $result = $this->addMoneyLabelsRepository->removeLogo($id);
        return (int) $result;
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
}