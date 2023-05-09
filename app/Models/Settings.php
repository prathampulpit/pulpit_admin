<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Settings extends Model {

    protected $guarded = ['id'];

    public static function getPincode($string) {
        $strings = explode(' ', $string);
        $pincode = '';
        foreach ($strings as $string) {
            $pincode = (Settings::extract_numbers($string));

            if (!empty($pincode[0])) {
                $pincode = $pincode[0];
                break;
            }
        }
        $getCityStateDetails = DB::table('pincodes')->where('Pincode', $pincode)->first();
        
        return $getCityStateDetails;
    }

    public static function extract_numbers($string) {
        return preg_match_all('/(?<!\d)\d{5,6}(?!\d)/', $string, $match) ? $match[0] : [];
    }

}
