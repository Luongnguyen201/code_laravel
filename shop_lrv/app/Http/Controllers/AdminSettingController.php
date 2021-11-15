<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class AdminSettingController extends Controller
{
    private $Setting;
    public function __construct(Setting $settings)
    {
        $this->Setting = $settings;
    }
    public function index(){
        $list = $this->Setting->latest()->paginate(5);
        return View('admin.setting.index', compact('list'));
    }
    public function create(){
        return View('admin.setting.add');
    }
    public function store(SettingRequest $request){
        $dataInsert = [
            'config_key' => $request->config_key,
            'config_value' => $request->config_value,
            'type' => $request->type,
        ];
        if($request->hasFile('image_path')){
            $file = $request->image_path;
            $fileNameOrigin = $file->getClientOriginalName();
            $fileNameHash = $file->getClientOriginalExtension();
            $path = $request->file('image_path')->storeAs('public/setting/'.auth()->id(),$fileNameOrigin);
            $dataUploadTrait = [
                'file_name'=>$fileNameOrigin,
                'file_path'=>$url = Storage::url($path),
            ];
        }
        if(!empty($dataUploadTrait)){
            $dataInsert['image_path'] = $dataUploadTrait['file_path'];
        }
        $this->Setting->create($dataInsert);
        return redirect('admin/setting/index');
    }
    public function edit($id,$type){
        $SettingUpdate= $this->Setting->find($id);
        return View('admin.setting.edit',compact('SettingUpdate'));
    }
    public function update(Request $request,$id){
        $SettingUpdate = [
            'config_key' => $request->config_key,
            'config_value' => $request->config_value,
        ];
        if($request->hasFile('image_path')){
            $file = $request->image_path;
            $fileNameOrigin = $file->getClientOriginalName();
            $fileNameHash = $file->getClientOriginalExtension();
            $path = $request->file('image_path')->storeAs('public/setting/'.auth()->id(),$fileNameOrigin);
            $dataUploadTrait = [
                'file_name'=>$fileNameOrigin,
                'file_path'=>$url = Storage::url($path),
            ];
        }
        if(!empty($dataUploadTrait)){
            $SettingUpdate['image_path'] = $dataUploadTrait['file_path'];
        }
        $this->Setting->find($id)->update($SettingUpdate);
        return redirect('admin/setting/index');
    }
    public function delete($id){
        try{
            $this->Setting->find($id)->delete();
            return response()->json([
                'code' => 200,
                'message'=>'success',
            ],200);
        }catch(Exception $exception) {
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
            return response()->json([
                'code' => 500,
                'message' => 'fail',
            ],500);
        }
    }
}
