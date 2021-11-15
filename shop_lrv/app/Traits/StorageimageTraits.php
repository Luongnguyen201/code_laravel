<?php
namespace App\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
    trait StorageimageTrait{
        public function storageTraitUpload($request,$fielname, $foldername){
            if($request->hasFile($fielname)){
                $file = $request->$fielname;
                $fileNameOrigin = $file->getClientOriginalName();
                $fileNameHash = $file->getClientOriginalExtension();
                $path = $request->file($fielname)->storeAs('public/'.$foldername.'/'.auth()->id(),$fileNameOrigin);
                $dataUploadTrait = [
                    'file_name'=>$fileNameOrigin,
                    'file_path'=>$url = Storage::url($path),
                ];
                    return $dataUploadTrait ;
            }
            return null;
        }
    }

    trait StorageimageTraitUploadMutiple{
        public function storageTraitUpload($file, $foldername){
                $fileNameOrigin = $file->getClientOriginalName();
                $fileNameHash = $file->getClientOriginalExtension();
                $path = $file->storeAs('public/'.$foldername.'/'.auth()->id(),$fileNameOrigin);
                $dataUploadTrait = [
                    'file_name'=>$fileNameOrigin,
                    'file_path'=>$url = Storage::url($path),
                ];
                    return $dataUploadTrait ;
        }
    }
?>
<!-- -----------------Không gọi được trait--------------------------- -->