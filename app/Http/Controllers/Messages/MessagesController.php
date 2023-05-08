<?php

namespace App\Http\Controllers\Messages;

use App\Repositories\MessagesRepository;
use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use Aws\Exception\AwsException;
use App\Models\TripFare;
use App\Models\Roles;
use App\Models\User;
use App\Models\Cities;
use App\Models\VehicleTypes;
use App\Models\WebsiteLocalTripFare;
use App\Models\PortalActivities;
use App\Models\PolygonRecords;
use App\Repositories\TripFareRepository;
use App\Repositories\WebsiteLocalTripFareRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;

class MessagesController extends Controller {

    protected $MessagesRepository;

    public function __construct(MessagesRepository $MessagesRepository) {
        $this->MessagesRepository = $MessagesRepository;
    }

    public function index($slug = null, $lang = null) {
        return view('admin.modules.messages.index');
    }

    public function index_json(Request $request) {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->MessagesRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->MessagesRepository->getPanelUsers($request, $params);
        return $users;
    }
 

     

    public function show($panel, $id) {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $params = [];
        $params['id'] = $id;
        $params['response_type'] = "single";
        $data = $this->MessagesRepository->getByParams($params);

        return view('admin.modules.messages.show', [
            'data' => $data
        ]);
    }

    public function destroy($panel, $id) {
        $result = \App\Models\Messages::where('id', $id)->delete();
        return (int) $result;
    }
    
    

}
