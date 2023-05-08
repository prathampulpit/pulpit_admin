<?php

namespace App\Http\Controllers\BillProducts;

use App\Http\Controllers\Controller;
use App\Http\Requests\CmsPages\StoreCmsPage;
use App\Models\User;
use App\Models\Roles;
use App\Models\AppconfigVersions;
use App\Models\PortalActivities;
use App\Repositories\BillpaymentProductsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Password;
use Session;

class BillProductsController extends Controller
{
    protected $billpaymentProductsRepository;

    public function __construct(
        BillpaymentProductsRepository $billpaymentProductsRepository
    ) {
        $this->billpaymentProductsRepository = $billpaymentProductsRepository;
    }

    public function index()
    {
        /* $user = Auth::user();
        $role_id = $user['role_id'];
        
        $role = Roles::find($role_id);
        $this->user_role = $role['slug']; */

        $biller_path = config('custom.upload.billerImages');
        $file_path = env('APP_URL') . '/storage/app/public/' . $biller_path . "/";

        return view('admin.modules.bill_products.index', compact('file_path'));
    }

    public function index_json(Request $request)
    {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->billpaymentProductsRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $cms = $this->billpaymentProductsRepository->getPanelUsers($request, $params);
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
            $item = $this->billpaymentProductsRepository->getByParams($params);
        }
        return view('admin.modules.bill_products.store', [
            'item' => $item,
            'id' => $id,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'icon' => 'mimes:jpg,jpeg,png'
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.bill_products.edit', ['panel' => Session::get('panel'), 'id' => $request->get('id', null)]))->withMessage("The icon must be a file of type: jpg, jpeg, png.");
        }

        $user = array();
        $user['id'] = $request->get('id', null);
        if ($request->has('icon')) {
            $file = $request->file('icon');
            $uploadPath = config('custom.upload.billerImages');
            $path = $this->billpaymentProductsRepository->upload($file, $uploadPath);
            $user['icon'] = $path;
        }
        $user['name'] = $request->get('name');
        $user['short_name'] = $request->get('short_name');
        $user['no_of_transaction'] = $request->get('no_of_transaction');
        $user['amount_limit_per_transaction'] = $request->get('amount_limit_per_transaction');
        $this->billpaymentProductsRepository->save($user);

        $billPayment = AppconfigVersions::find(1);
        $billPayment->version = $billPayment['version'] + 1;
        $billPayment->save();

        if (!empty($request->get('id'))) {
            $message = 'Record updated Successfully';
        } else {
            $message = 'Record added Successfully';
        }

        $user = Auth::user();
        $loginsert = new PortalActivities();
        $loginsert->user_id = $user['id'];
        $loginsert->module_name = 'Edit Bill Product';
        $loginsert->request_data = json_encode($_POST);
        $loginsert->response_data = 'NA';
        $loginsert->save();

        return redirect(route('admin.bill_products.index', ['panel' => Session::get('panel')]))->withMessage($message);
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

    public function toggleStatus($panel, $id)
    {
        $billPayment = AppconfigVersions::find(1);
        $billPayment->version = $billPayment['version'] + 1;
        $billPayment->save();

        $result = $this->billpaymentProductsRepository->toggleStatus($id);
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

    public function showChangePasswordForm()
    {
        return view('admin.modules.user.change_password');
    }

    public function showChangeProfileForm()
    {
        return view('admin.modules.user.change_profile');
    }
}