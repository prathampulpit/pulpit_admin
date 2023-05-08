<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use Aws\Rekognition\RekognitionClient;
use Aws\Textract\TextractClient;
use App\Repositories\UserRepository;
use Intervention\Image\Facades\Image;
use App\Models\User;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;
use DB;

class LocatorController extends BaseController
{
    protected $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
        $this->user_id = $_POST['user_id'];
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function locator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $user_id = $input['user_id'];
        if (isset($input['latitude'])) {
            $latitude = $input['latitude'];
        } else {
            $latitude = "0.00";
        }

        if (isset($input['longitude'])) {
            $longitude = $input['longitude'];
        } else {
            $longitude = "0.00";
        }

        $settings = Settings::find('1');
        $dist = $settings['agent_locator_distance'];

        $sqlDistance = DB::raw('( 6373 * acos( cos( radians(' . $latitude . ') ) 
        * cos( radians( locators.latitude ) ) 
        * cos( radians( locators.longitude ) 
        - radians(' . $longitude  . ') ) 
        + sin( radians(' . $latitude  . ') ) 
        * sin( radians( locators.latitude ) ) ) )');

        if ($dist != '0') {
            $atm_arr =  DB::table('locators')
                ->selectRaw("{$sqlDistance} AS distance, latitude, longitude, name, address, phone_number, status")
                ->where('status', '1')
                ->where('type', '1')
                ->having("distance", "<", $dist)
                ->orderBy('distance')
                ->get();
        } else {
            $atm_arr =  DB::table('locators')
                ->selectRaw("{$sqlDistance} AS distance, latitude, longitude, name, address, phone_number, status")
                ->where('status', '1')
                ->where('type', '1')
                ->orderBy('distance')
                ->get();
        }

        $json_atm = array();
        if (!empty($atm_arr)) {
            foreach ($atm_arr as $val) {
                $d['name'] = $val->name;
                $d['address'] = $val->address;
                $d['latitude'] = $val->latitude;
                $d['longitude'] = $val->longitude;
                $d['distance'] = $val->distance;
                $json_atm[] = $d;
            }
        }

        if ($dist != '0') {
            $agent_arr =  DB::table('locators')
                ->selectRaw("{$sqlDistance} AS distance, latitude, longitude, name, address, phone_number, status")
                ->where('status', '1')
                ->where('type', '2')
                ->having("distance", "<", $dist)
                ->orderBy('distance')
                ->get();
        } else {
            $agent_arr =  DB::table('locators')
                ->selectRaw("{$sqlDistance} AS distance, latitude, longitude, name, address, phone_number, status")
                ->where('status', '1')
                ->where('type', '2')
                ->orderBy('distance')
                ->get();
        }

        $json_agent = array();
        if (!empty($agent_arr)) {
            foreach ($agent_arr as $rec) {
                $r['name'] = $rec->name;
                $r['address'] = $rec->address;
                $r['latitude'] = $rec->latitude;
                $r['longitude'] = $rec->longitude;
                $r['distance'] = $rec->distance;
                $json_agent[] = $r;
            }
        }

        /* List of ATM locator */
        /* $arm_json = '{"reference":null,"resultcode":"200","result":"SUCCESS","message":"fetch successful","data":[{"id":"3","name":"AMANI PLACE AKIBA","address":"Amani Place, ground floor, Ohio St, DSM","phoneNumber":"NA","gpsLat":"-6.8099436","gpsLong":"39.2865069","status":"1"}]}';
        $atm_arr = json_decode($arm_json,true); */
        /* $external_id = rand(1000,9999).time().rand(1000,9999);
        $param['externalId'] = $external_id;
        $json_request = json_encode($param);
        $atm_arr = $this->selcomApi('config/atm-locations',$json_request,$this->user_id,"GET");
        
        $json_atm = array();
        if(!empty($atm_arr)){
            foreach($atm_arr['data'] as $val){
                $d['name'] = $val['name'];
                $d['address'] = $val['address'];
                $d['latitude'] = $val['gpsLat'];
                $d['longitude'] = $val['gpsLong'];
                $json_atm[] = $d;
            }
        } */

        /* List of Agent locator */
        //$agent_json = '{"reference":null,"resultcode":"200","result":"SUCCESS","message":"fetch successful","data":[{"id":"1","name":"JOHN GEOFREY CHIKOMA","address":"KARIAKOO, DSM","phoneNumber":"255XYZXYZ","gpsLat":"-6.857103","gpsLong":"39.202234","status":"1"},{"id":"2","name":"ELIMILIKI DAUDI KALEKELA","address":"MABIBI MWISHO, UBUNGO, DSM","phoneNumber":"255XYZXYZ","gpsLat":"-6.484332","gpsLong":"39.131229","status":"1"}]}';

        /* $external_id = rand(1000,9999).time().rand(1000,9999);
        $payment_reference = rand(1000,9999).time().rand(1000,9999);
        $param['externalId'] = $external_id;
        $json_request = json_encode($param);
        $agent_arr = $this->selcomApi('config/agent-locations',$json_request,$this->user_id,"GET");
        $json_agent = array();
        if(!empty($agent_arr)){
            foreach($agent_arr['data'] as $rec){
                $r['name'] = $rec['name'];
                $r['address'] = $rec['address'];
                $r['latitude'] = $rec['gpsLat'];
                $r['longitude'] = $rec['gpsLong'];
                $json_agent[] = $r;
            }
        } */
        $response = array();
        $response['atm'] = $json_atm;
        $response['agent'] = $json_agent;
        return $this->sendResponse('1', $response, trans('message.list_of_location'));
    }
}