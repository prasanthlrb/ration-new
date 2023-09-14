<?php

namespace App\Helpers\Util;


use Illuminate\Support\Str;

class ImageUtil
{

    static function getImageOrNull(?string $data): ?string
    {

        if($data!=null && strlen($data)>100){
            return base64_decode($data);
        }else{
            return null;
        }
    }



}

