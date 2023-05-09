<?php

namespace App\Helper;

class Helper {

    public function getFileExt($file) {
        $Infos = pathinfo($file);
        return $extension = $Infos['extension'];
    }

   

}
