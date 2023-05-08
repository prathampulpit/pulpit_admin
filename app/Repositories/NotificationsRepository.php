<?php

namespace App\Repositories;

use App\Notifications;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DB;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationsRepository extends BaseRepository
{
    protected $notifications;

    public function __construct(Notifications $notifications)
    {
        parent::__construct($notifications);
        $this->notifications = $notifications;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $query = $this->notifications::whereRaw('1=1');
        $query->select('notifications.id', 'users.first_name', 'users.last_name', 'notifications.title', 'notifications.description', 'notifications.type', 'notifications.user_id', 'notifications.datetime');
        $query->leftJoin('users', 'notifications.user_id', '=', 'users.id');
        $query->where('notifications.type', 'like' , '%admin%');
        
        // conditions
        //$query->where('status', '!=', '2');
        if (isset($params['id'])) {
            $query->where('notifications.id', $params['id']);
        }

        if (isset($params['title'])) {
            $query->where('notifications.title', $params['title']);
        }

        if (isset($params['user_id'])) {
            $query->where('notifications.user_id', $params['user_id']);
        }
        
        if (isset($params['response_type']) && $params['response_type'] == "single") {
            $records = $query->first();
            return $records;
        }
        $query->groupBy('notifications.description');
        // order by
        if (isset($params['order_by']) && isset($params['order'])) {
            $query->orderBy($params['order_by'], $params['order']);
        }

        if (isset($params['count'])) {
            $records = $query->count();
            return $records;
        }

        // paginate
        if (isset($params['limit'])) {
            $records = $query->paginate($params['limit']);
        } else {
            $records = $query->get();
        }

        return $records;
    }

    public function getPanelUsers($request, $params)
    {
        if (request('per_page') == 'all') {
            $usersCount = [];
            $usersCount['count'] = true;
            $perPage = $this->getByParams($usersCount);
        } else {
            $perPage = request('per_page', config('custom.db.per_page'));
        }
        $orderBy = request('order_by', 'cab_post.id');
        $order = request('order', 'desc');
        
        $query = $this->notifications::whereRaw('1=1');
        $query->select('notifications.id', 'users.first_name', 'users.last_name', 'notifications.title', 'notifications.description', 'notifications.type', 'notifications.user_id', 'notifications.datetime','notifications.image_name');
        $query->leftJoin('users', 'notifications.user_id', '=', 'users.id');
        $query->where('notifications.type', 'like' , '%admin%');

        //$query->where('status', '!=', '2');
        if (isset($params['id'])) {
            $query->where('notifications.id', $params['id']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("notifications.title like " . "'" . $search . "' OR notifications.description like " . "'" . $search . "'");
            });
        }
        $query->groupBy('notifications.description');

        $query->orderBy($orderBy, $order);
        
        if (isset($params['limit'])) {
            $records = $query->paginate($params['limit']);
        } else {
            $records = $query->paginate($perPage);
        }
        return $records;
    }
    
    public function updateStatus($id)
    {
        $user = $this->notifications::where('id', $id)->delete();
    }

    public function upload($file, $uploadPath)
    {
        $name = $this->getName($file);
        $path = $uploadPath . '/' . $name;

        $disk = $this->getDisk();
        Storage::disk($disk)->put($path, file_get_contents($file));

        return $name;
    }

    private function getName($file)
    {
        return Str::slug(preg_replace('/\s+/', '_', time())) . '-' . time() . '.' . $file->getClientOriginalExtension();
    }

    private function getDisk()
    {
        return  config('custom.upload.disk', 'local');
    }

    public function sendPuchNotification($deviceType, $deviceToken, $notificationText,$totalNotifications='0',$pushMessageText="", $title="Pulpit", $imageName="") {
        $fields = "notificationId";
        $devicetoken[] = $deviceToken;
        $desc = $notificationText;

        $type = 'admin';
        if(!empty($pushMessageText)){
            $type = $pushMessageText;
        }

        // Set POST variables 
        // $url = 'https://fcm.googleapis.com/fcm/send';
        //$message = array("message" => $desc);
        $message = array("message" => $desc, 'title' => $title, 'click_action' => "FLUTTER_NOTIFICATION_CLICK", 'status' => 'done');
        //$message = $desc;
        
        //echo $totalNotifications;
        // $notificationArray["body"] =  ;
        // $notificationArray["title"] = ; 

        if ($deviceType == 'Iphone') {
            // $fieldsJson =  '{"to":"'.$deviceToken.'","content_available": true,"mutable_content":true,"priority":"high","data":{"body":"'.mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8").'","type":"'.$type.'","image":"'.$imageName.'","sound":true,"title":"'.$title.'","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"},"notification":{"body":"'.mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8").'","sound":true,"title":"'.$title.'","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"}}';
            // $fieldsJson = 
        }else{
            // // $fieldsJson =  '{"to":"'.$deviceToken.'","content_available": true,"mutable_content":true,"priority":"high","data":{"body":"'.mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8").'","sound":true,"title":"'.$title.'","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"},"notification":{"body":"'.mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8").'","sound":true,"title":"'.$title.'","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"}}';
            // $fieldsJson =  '{"to":"'.$deviceToken.'","content_available": true,"mutable_content":true,"priority":"high","data":{"body":"'.mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8").'","type":"'.$type.'","image":"'.$imageName.'","sound":true,"title":"'.$title.'","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"}}';
        }
        // $data["template"] = $notificationArray;
        // $data["data"] = $fieldsJson;
        $data = [
          "template" => [
                "title" => $title,
                "body" => $desc,
                "image" => $imageName,
             ], 
          "data" => [
                    "body" => "'.mb_convert_encoding($notificationText, 'HTML-ENTITIES', 'UTF-8').'",
                    "type" => "'.$type.'",
                    "sound" => true,
                    "title" => "'.$title.'",
                    "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                    "priority" => "high"
                ], 
          "deviceToken" => [
                      $deviceToken 
                   ] 
          ];
 
        // dd($data);
 
        $response = Http::post('https://customer.api.pulpitmobility.com/customer/notification_php', $data);
  
        $jsonData = $response->json();
        
        // dd($jsonData['status']);
        // $headers = array(
        //     'Authorization: key='.env('GOOGLE_API_KEY'),
        //     'Content-Type: application/json'
        // );

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsJson);
        // $result = curl_exec($ch);
        if ($jsonData['status'] != 200) {
            die('Curl failed: ' . $jsonData['status']);
        }
        // curl_close($ch);

        /* echo $fieldsJson; 
        echo "<br>";
        echo $result;
        exit; */
        /* DB::table('api_logs')->insert([
            'user_id' => '0',
            'api_name' => 'Notification Log',
            'request_data' => $fieldsJson,
            'response_data' => $result,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]); */
        
        return $jsonData;
    }
}
