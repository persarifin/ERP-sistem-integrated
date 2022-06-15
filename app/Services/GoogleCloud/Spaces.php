<?php

namespace App\Services\GoogleCloud;
use Illuminate\Support\Facades\Storage;

class Spaces { 

    function upload($folder = '', $file_name = '', $file = null)
    {
        if(!$file){
          return false;
        }
        try{
          $folder = $folder ? $folder.'/' : '';
          $file_name = $file_name ? $file_name : time();
          $result = Storage::disk('gcs')->put($folder.''.$file_name, file_get_contents($file), 'public');
          
        }catch(\Exception $e){
          return $e->getMessage();
        }
        return [
          "file_url" => config('filesystems.disks.gcs.storage_api_uri').$folder.$file_name,
          "folder_url" => config('filesystems.disks.gcs.storage_api_uri').$folder,
          "file_name" => $file_name
        ];
    }
}
