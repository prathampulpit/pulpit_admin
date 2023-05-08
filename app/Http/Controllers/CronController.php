<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;
use App\Models\UserWorkProfile;
use App\Models\AgentUsers;
use App\Models\BankAccount;
use App\Models\Drivers;
use App\Models\Vehicles;
use App\Models\VehiclePhotoMapping;
use App\Models\VehicleDrivingMapping;
use App\Models\UserBankMapping;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Password;
use Session;
use URL;
use DB;

class CronController extends Controller
{
    public function __construct()
    {
    }

    public function index($slug = null, $lang = null)
    {
    }
   public function users_approved() {
        
           $users = User::where(function ($query) {
                    $query->where('is_approved','!=', 1);
                    $query->orWhere('is_approved', 2);
  		    $query->orWhere('is_approved', 0);                  
                })->where('type', 'user')
              //  ->where('id',14380)
                ->get();

        foreach ($users as $row) {
            $user_id = $row->id;
             
            $agent_users = DB::table('agent_users')
                            ->join('user_work_profile', 'user_work_profile.profile_id', '=', 'agent_users.id')
                            ->select('agent_users.*')
                            ->where('user_work_profile.user_type_id', 3)->where('user_work_profile.status', 1)
                            ->where('user_work_profile.user_id', $user_id)
                            ->where('agent_users.all_document_verify', 1)->count();

            $vehicles_users = DB::table('vehicles')->where('vehicles.user_id', $user_id)
                            ->where('vehicles.all_document_verify', 1)->count();

            $drivers_users = DB::table('drivers')
                            ->join('user_work_profile', 'user_work_profile.profile_id', '=', 'drivers.id')
                            ->where('user_work_profile.user_type_id', 4)->where('user_work_profile.status', 1)
                            ->where('user_work_profile.user_id', $user_id)
                            ->where('drivers.all_document_verify', 1)->count();

            if ($agent_users >= 1 && $vehicles_users >= 1 && $drivers_users >= 1) {
                     \Log::info('-------Run Cron Job Users '.$user_id." Is Approved->".$is_approved);
 
                $userObject = User::find($user_id);
                $userObject->is_approved = 1;
                $userObject->save();
                \Log::info('-------Run Cron Job Users Approved Its User Id '.$user_id);
            }
        }
    }
    public function updateApprovedRequest()
    {
        $offset = '4000';
        $limit = '1000';
        $users = User::where('status', 1)->where('type', 'user')->where('is_approved', '2')->offset($offset)->limit($limit)->orderBy('id', 'ASC')->get();
        //$users = User::where('status',1)->where('type','user')->where('is_approved','2')->where('id','309')->get();
        //echo "<pre>"; print_r($users); exit;

        foreach ($users as $val) {
            $user_id = $val['id'];
            $user_type_id = $val['user_type_id'];

            if ($user_type_id == '2') { //Agent

                $isAgentStatus = 2;
                $isBankStatus = 2;
                $work = UserWorkProfile::where('user_type_id', '=', '2')->where('status', '=', 1)->where('user_id', '=', $user_id)->first();
                //echo "<pre>"; print_r($work); exit;

                if (!empty($work)) {
                    $profile_id = $work->profile_id;

                    $agent = AgentUsers::where('status', 1)->where('id', $profile_id)->first();
                    if (!empty($agent)) {
                        $pan_card_url_status = $agent->pan_card_url_status;
                        $adhar_card_url_status = $agent->adhar_card_url_status;
                        $registration_document_url_status = $agent->registration_document_url_status;
                        $adhar_card_back_url_status = $agent->adhar_card_back_url_status;
                        $logo_status = $agent->logo_status;

                        if ($pan_card_url_status == 1 && $adhar_card_url_status == 1 && $adhar_card_back_url_status == 1) {
                            /* $userUpdate = User::find($user_id);
                            $userUpdate->is_approved = 1;
                            $userUpdate->save(); */
                            $isAgentStatus = 1;
                        } elseif ($pan_card_url_status == 0 || $adhar_card_url_status == 0 || $adhar_card_back_url_status == 0) {
                            /* $userUpdate = User::find($user_id);
                            $userUpdate->is_approved = 0;
                            $userUpdate->save(); */
                            $isAgentStatus = 0;
                        }
                    }
                }

                /* Check bank details */
                $bankdetails = UserBankMapping::where('user_id', $user_id)->first();
                if (!empty($bankdetails)) {
                    $bank_account_id = $bankdetails->bank_account_id;
                    $bankaccount = BankAccount::where('id', $bank_account_id)->first();
                    if (!empty($bankaccount)) {
                        $bank_document_url_status = $bankaccount->bank_document_url_status;
                        if ($bank_document_url_status == 1) {
                            $isBankStatus = 1;
                        } else if ($bank_document_url_status == 0) {
                            $isBankStatus = 0;
                        }
                    }
                }

                /* echo $isAgentStatus;
                echo "===";
                echo $isBankStatus;
                exit; */

                if ($isAgentStatus == 1 && $isBankStatus == 1) {
                    $userUpdate = User::find($user_id);
                    $userUpdate->is_approved = 1;
                    $userUpdate->save();
                } else if ($isAgentStatus == 0 || $isBankStatus == 0) {
                    $userUpdate = User::find($user_id);
                    $userUpdate->is_approved = 0;
                    $userUpdate->save();
                }
            } else if ($user_type_id == '3') { //Travel Agency

                $isAgentApproved = 2;
                $isTravelApproved = 2;
                $isBankStatus = 2;

                /* Owner details check in agent user table with 3 user type */
                $agentWork = UserWorkProfile::where('user_type_id', '=', '3')->where('status', 1)->where('user_id', '=', $user_id)->first();
                if (!empty($agentWork)) {
                    $profile_id = $agentWork->profile_id;

                    $agent = AgentUsers::where('status', 1)->where('id', $profile_id)->first();
                    if (!empty($agent)) {
                        $pan_card_url_status = $agent->pan_card_url_status;
                        $adhar_card_url_status = $agent->adhar_card_url_status;
                        $registration_document_url_status = $agent->registration_document_url_status;
                        $adhar_card_back_url_status = $agent->adhar_card_back_url_status;
                        $logo_status = $agent->logo_status;

                        if ($pan_card_url_status == 1 && $adhar_card_url_status == 1 && $adhar_card_back_url_status == 1) {
                            $isAgentApproved = 1;
                        } elseif ($pan_card_url_status == 0 || $adhar_card_url_status == 0 || $adhar_card_back_url_status == 0) {
                            $isAgentApproved = 0;
                        }
                    }
                }

                /* Check bank details */
                $bankdetails = UserBankMapping::where('user_id', $user_id)->first();
                if (!empty($bankdetails)) {
                    $bank_account_id = $bankdetails->bank_account_id;
                    $bankaccount = BankAccount::where('id', $bank_account_id)->first();
                    if (!empty($bankaccount)) {
                        $bank_document_url_status = $bankaccount->bank_document_url_status;
                        if ($bank_document_url_status == 1) {
                            $isBankStatus = 1;
                        } else if ($bank_document_url_status == 0) {
                            $isBankStatus = 0;
                        }
                    }
                }

                /* Vehicle details check user with user id */
                $vehicles = Vehicles::where('status', 1)->where('user_id', $user_id)->first();
                if (!empty($vehicles)) {
                    $vehicle_id = $vehicles->id;
                    $rc_front_url_status = $vehicles->rc_front_url_status;
                    $rc_back_url_status = $vehicles->rc_back_url_status;
                    $insurance_doc_url_status = $vehicles->insurance_doc_url_status;
                    $permit_doc_url_status = $vehicles->permit_doc_url_status;
                    $fitness_doc_url_status = $vehicles->fitness_doc_url_status;
                    $puc_doc_url_status = $vehicles->puc_doc_url_status;
                    $agreement_doc_url_status = $vehicles->agreement_doc_url_status;

                    if ($rc_front_url_status == 1 && $rc_back_url_status == 1 && $insurance_doc_url_status == 1) {

                        $vehiclePhoto = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', '1')->first();
                        if (!empty($vehiclePhoto)) {
                            $vehiclePhotoId = $vehiclePhoto['id'];

                            $update2 = VehiclePhotoMapping::find($vehiclePhotoId);
                            $image_url_status = $update2->image_url_status;
                            if ($image_url_status == 1) {
                                $isTravelApproved = 1;
                            } else if ($image_url_status == 0) {
                                $isTravelApproved = 0;
                            }
                        }
                    } else if ($rc_front_url_status == 0 || $rc_back_url_status == 0 || $insurance_doc_url_status == 0) {

                        $isTravelApproved = 0;
                        /* $vehiclePhoto = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', '1')->first();
                        if (!empty($vehiclePhoto)) {
                            $vehiclePhotoId = $vehiclePhoto['id'];

                            $update2 = VehiclePhotoMapping::find($vehiclePhotoId);
                            $image_url_status = $update2->image_url_status;
                            if ($image_url_status == 0) {
                                $isTravelApproved = 0;
                            }
                        } */
                    }
                }

                //echo $isAgentApproved."===".$isTravelApproved;

                if ($isAgentApproved == 1 && $isTravelApproved == 1 && $isBankStatus == 1) {
                    $userUpdate = User::find($user_id);
                    $userUpdate->is_approved = 1;
                    $userUpdate->save();
                } else if ($isAgentApproved == 0 || $isTravelApproved == 0 || $isBankStatus == 0) {
                    $userUpdate = User::find($user_id);
                    $userUpdate->is_approved = 0;
                    $userUpdate->save();
                }
            } else if ($user_type_id == '4') { //Driver

                $isDriverApproved = 2;
                $isVehicleApproved = 2;
                $work = UserWorkProfile::where('user_type_id', '=', '4')->where('status', '=', '1')->where('user_id', '=', $user_id)->first();
                if (!empty($work)) {
                    $profile_id = $work->profile_id;

                    $drivers = Drivers::where('status', 1)->where('id', $profile_id)->first();
                    if (!empty($drivers)) {
                        $dl_front_url_status = $drivers->dl_front_url_status;
                        $dl_back_url_status = $drivers->dl_back_url_status;
                        $police_verification_url_status = $drivers->police_verification_url_status;
                        $d_pan_card_url_status = $drivers->d_pan_card_url_status;
                        $d_adhar_card_url_status = $drivers->d_adhar_card_url_status;
                        $d_adhar_card_back_url_status = $drivers->d_adhar_card_back_url_status;

                        if ($dl_front_url_status == 1 && $dl_back_url_status == 1) {
                            $isDriverApproved = 1;
                        } elseif ($dl_front_url_status == 0 || $dl_back_url_status == 0) {
                            $isDriverApproved = 0;
                        }

                        /* $vehicleDrivingMapping = VehicleDrivingMapping::where('driver_id', $profile_id)->where('status','=','1')->first();
                        if(!empty($vehicleDrivingMapping)){
                            $vehicle_id = $vehicleDrivingMapping->vehicle_id;
                            
                            $vehicles = Vehicles::where('status',1)->where('id',$vehicle_id)->first();
                            if (!empty($vehicles)) {
                                $rc_front_url_status = $vehicles->rc_front_url_status;
                                $rc_back_url_status = $vehicles->rc_back_url_status;
                                $insurance_doc_url_status = $vehicles->insurance_doc_url_status;
                                $permit_doc_url_status = $vehicles->permit_doc_url_status;
                                $fitness_doc_url_status = $vehicles->fitness_doc_url_status;
                                $puc_doc_url_status = $vehicles->puc_doc_url_status;
                                $agreement_doc_url_status = $vehicles->agreement_doc_url_status;

                                if ($rc_front_url_status == 1 && $rc_back_url_status == 1 && $insurance_doc_url_status == 1) {
                                    $vehiclePhoto = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', '1')->first();
                                    if (!empty($vehiclePhoto)) {
                                        $vehiclePhotoId = $vehiclePhoto['id'];

                                        $update2 = VehiclePhotoMapping::find($vehiclePhotoId);
                                        $image_url_status = $update2->image_url_status;
                                        if ($image_url_status == 1) {
                                            $isVehicleApproved = 1;
                                        }
                                    }
                                } elseif ($rc_front_url_status == 1 && $rc_back_url_status == 1 && $insurance_doc_url_status == 1) {
                                    $isVehicleApproved = 0;
                                }
                            }
                        } */
                    }
                }

                if ($isDriverApproved == 1) { /* && $isVehicleApproved = 1 */
                    $userUpdate = User::find($user_id);
                    $userUpdate->is_approved = 1;
                    $userUpdate->save();
                } else if ($isDriverApproved == 0) { /*  && $isVehicleApproved = 0 */
                    $userUpdate = User::find($user_id);
                    $userUpdate->is_approved = 0;
                    $userUpdate->save();
                }
            } else if ($user_type_id == '5') { //Driver Cum Owner
                $isDriverApproved = 2;
                $isVehicleApproved = 2;
                $isBankStatus = 2;
                $work = UserWorkProfile::where('user_type_id', '=', '5')->where('status', '=', '1')->where('user_id', '=', $user_id)->first();
                if (!empty($work)) {
                    $profile_id = $work->profile_id;

                    $drivers = Drivers::where('status', 1)->where('id', $profile_id)->first();
                    if (!empty($drivers)) {
                        $dl_front_url_status = $drivers->dl_front_url_status;
                        $dl_back_url_status = $drivers->dl_back_url_status;
                        $police_verification_url_status = $drivers->police_verification_url_status;
                        $d_pan_card_url_status = $drivers->d_pan_card_url_status;
                        $d_adhar_card_url_status = $drivers->d_adhar_card_url_status;
                        $d_adhar_card_back_url_status = $drivers->d_adhar_card_back_url_status;

                        if ($dl_front_url_status == 1 && $dl_back_url_status == 1 && $d_pan_card_url_status == 1 && $d_adhar_card_url_status == 1 && $d_adhar_card_back_url_status == 1) {
                            $isDriverApproved = 1;
                        } elseif ($dl_front_url_status == 0 || $dl_back_url_status == 0 || $d_pan_card_url_status == 0 || $d_adhar_card_url_status == 0 || $d_adhar_card_back_url_status == 0) {
                            $isDriverApproved = 0;
                        }

                        $vehicles = Vehicles::where('status', 1)->where('user_id', $user_id)->first();
                        if (!empty($vehicles)) {
                            $vehicle_id = $vehicles->id;
                            $rc_front_url_status = $vehicles->rc_front_url_status;
                            $rc_back_url_status = $vehicles->rc_back_url_status;
                            $insurance_doc_url_status = $vehicles->insurance_doc_url_status;
                            $permit_doc_url_status = $vehicles->permit_doc_url_status;
                            $fitness_doc_url_status = $vehicles->fitness_doc_url_status;
                            $puc_doc_url_status = $vehicles->puc_doc_url_status;
                            $agreement_doc_url_status = $vehicles->agreement_doc_url_status;

                            if ($rc_front_url_status == 1 && $rc_back_url_status == 1 && $insurance_doc_url_status == 1) {
                                $vehiclePhoto = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', '1')->first();
                                if (!empty($vehiclePhoto)) {
                                    $vehiclePhotoId = $vehiclePhoto['id'];

                                    $update2 = VehiclePhotoMapping::find($vehiclePhotoId);
                                    $image_url_status = $update2->image_url_status;
                                    if ($image_url_status == 1) {
                                        $isVehicleApproved = 1;
                                    } else if ($image_url_status == 0) {
                                        $isVehicleApproved = 0;
                                    }
                                }
                            } elseif ($rc_front_url_status == 0 || $rc_back_url_status == 0 || $insurance_doc_url_status == 0) {
                                $isVehicleApproved = 0;
                                /* $vehiclePhoto = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', '1')->first();
                                if (!empty($vehiclePhoto)) {
                                    $vehiclePhotoId = $vehiclePhoto['id'];

                                    $update2 = VehiclePhotoMapping::find($vehiclePhotoId);
                                    $image_url_status = $update2->image_url_status;
                                    if ($image_url_status == 0) {
                                        $isVehicleApproved = 0;
                                    }
                                } */
                            }
                        }
                    }
                }

                /* Check bank details */
                $bankdetails = UserBankMapping::where('user_id', $user_id)->first();
                if (!empty($bankdetails)) {
                    $bank_account_id = $bankdetails->bank_account_id;
                    $bankaccount = BankAccount::where('id', $bank_account_id)->first();
                    if (!empty($bankaccount)) {
                        $bank_document_url_status = $bankaccount->bank_document_url_status;
                        if ($bank_document_url_status == 1) {
                            $isBankStatus = 1;
                        } else if ($bank_document_url_status == 0) {
                            $isBankStatus = 0;
                        }
                    }
                }

                if ($isDriverApproved == 1 && $isVehicleApproved == 1 && $isBankStatus == 1) {
                    $userUpdate = User::find($user_id);
                    $userUpdate->is_approved = 1;
                    $userUpdate->save();
                } else if ($isDriverApproved == 0 || $isVehicleApproved == 0 || $isBankStatus == 1) {
                    $userUpdate = User::find($user_id);
                    $userUpdate->is_approved = 0;
                    $userUpdate->save();
                }
            } else if ($user_type_id == '6') {
                $userUpdate = User::find($user_id);
                $userUpdate->is_approved = 1;
                $userUpdate->save();
            }
        }
    }

    public function checkDeletedStatusUsers()
    {
        /* $offset = '0';
        $limit = '5000'; */
        //$users = User::where('status',1)->where('type','user')->offset($offset)->limit($limit)->orderBy('id','ASC')->get();
        //echo $totalusers = User::where('status',1)->where('type','user')->where('user_type_id','4')->where('is_approved','1')->count();
        $users = User::where('status', 1)->where('type', 'user')->get();
        echo "<pre>";
        //print_r($users); 
        //exit;

        $i = 1;
        foreach ($users as $val) {
            $user_id = $val['id'];
            //echo "<br>";

            $work = UserWorkProfile::where('user_type_id', '=', '3')->where('status', '=', 1)->where('user_id', '=', $user_id)->first();
            //$work = Vehicles::where('status', '=', 0)->where('user_id', '=', $user_id)->first();
            //echo "<pre>"; 
            //print_r($work); 
            //exit;

            if (!empty($work)) {

                /* echo $i;
                echo "<br>";
                $i++; */

                $profile_id = $work->profile_id;

                $vehicleMappings = VehicleDrivingMapping::where('vehicle_id', $profile_id)->get();
                if (!empty($vehicleMappings)) {
                    foreach ($vehicleMappings as $r) {
                        $driver_id = $r['driver_id'];
                        $vehicle_id = $r['vehicle_id'];
                        $vehicle_details = Vehicles::where('all_document_verify', '=', 1)->where('id', '=', $vehicle_id)->first();

                        if (!empty($vehicle_details)) {

                            if ($vehicle_details->status == 0) {
                                echo $vehicle_id;
                                echo "<br>";
                            }

                            $drivers = Drivers::where('status', 0)->where('id', $profile_id)->first();
                            if (!empty($drivers)) {
                                /* echo $driver_id = $drivers->id;
                                echo "<br>"; */

                                /* $update = Drivers::find($driver_id);
                                $update->status = 1;
                                $update->save(); */
                            }
                        }
                    }
                }
                /* echo "<br>"; */

                //$drivers = Drivers::where('status', 0)->where('id', $profile_id)->first();
                //print_r($agent);
                /* if (!empty($drivers)) {
                    $driver_id = $drivers->id; */

                /* $update = Drivers::find($driver_id);
                    $update->status = 1;
                    $update->save(); */

                /* echo $i;
                    echo "<br>"; */
                //$i++;
                //}
            }
        }
    }
    
 
    
}