<?php

namespace App\Http\Controllers\Topics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Topics\StoreTopics;
use App\Models\User;
use App\Models\Roles;
use App\Models\AppconfigVersions;
use App\Repositories\TopicsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Password;
use Session;

class TopicsController extends Controller
{
    protected $topicsRepository;

    public function __construct(
        TopicsRepository $topicsRepository
    ) {
        $this->topicsRepository = $topicsRepository;
    }

    public function index()
    {
        return view('admin.modules.topics.index');
    }

    public function index_json(Request $request)
    {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->topicsRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $cms = $this->topicsRepository->getPanelUsers($request, $params);
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
            $item = $this->topicsRepository->getByParams($params);
        }
        return view('admin.modules.topics.store', [
            'item' => $item,
            'id' => $id,
        ]);
    }

    public function store(StoreTopics $request)
    {
        $user = array();
        $user['id'] = $request->get('id', null);
        $user['name_en'] = $request->get('name_en');
        $user['name_sw'] = $request->get('name_sw');
        $user['type'] = $request->get('type');
        $user['updated_at'] = date("Y-m-d H:i:s");

        if (empty($request->get('id'))) {
            $user['created_at'] = date("Y-m-d H:i:s");
        }
        $this->topicsRepository->save($user);

        $version = AppconfigVersions::find(7);
        $version->version = $version['version'] + 1;
        $version->save();

        if (!empty($request->get('id'))) {
            $message = 'Record updated Successfully';
        } else {
            $message = 'Record added Successfully';
        }

        return redirect(route('admin.topics.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function toggleStatus($panel, $id)
    {
        $version = AppconfigVersions::find(3);
        $version->version = $version['version'] + 1;
        $version->save();

        $result = $this->topicsRepository->toggleStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        if (!Auth::user()->hasPermission('user-delete')) {
            abort(403);
        }

        $result = $this->topicsRepository->delete($id);
        return (int) $result;
    }

    public function delete_icon()
    {
        $id = request('id');
        $result = $this->topicsRepository->removeLogo($id);
        return (int) $result;
    }

    public function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return strtolower($string);
    }
}