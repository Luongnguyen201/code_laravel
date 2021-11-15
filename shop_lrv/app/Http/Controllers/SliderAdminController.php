<?php

namespace App\Http\Controllers;

use App\Http\Requests\SliderAddRequest;
use App\Models\Slider;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
class SliderAdminController extends Controller
{
    private $Slider;
    public function __construct(Slider $slider)
    {
        $this->Slider = $slider;
    }

    public function index(){
        $sliders = $this->Slider->latest()->paginate(5);
        return view('admin.slider.index', compact('sliders'));
    }
    public function create(){
        return view('admin.slider.add');
    }
    public function store(SliderAddRequest $request){
        try{
            DB::beginTransaction();
            $dataInsert = [
                'name'=> $request->name,
                'description' => $request->description,
            ];
    
            if( $request->hasFile('image_path') ){
                $file = $request->image_path;
                $fileNameOrigin = $file->getClientOriginalName();
                $fileNameHash = $file->getClientOriginalExtension();
                $path = $request->file('image_path')->storeAs('public/Slider/'.auth()->id(),$fileNameOrigin);
                $dataUploadTrait = [
                    'file_name'=>$fileNameOrigin,
                    'file_path'=>$url = Storage::url($path),
                ];
                
                if(!empty($dataUploadTrait)){
                    $dataInsert['image_path'] = $dataUploadTrait['file_path'];
                    $dataInsert['image_name'] = $dataUploadTrait['file_name'];
                }
            }
            $this->Slider->create($dataInsert);
            DB::commit();
            return redirect('admin/slider/index');

        }catch(Exception $exception){
            DB::rollBack();
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        }
    }
    public function edit($id){
        $Slider_Edit = $this->Slider->find($id);
        return view('admin.slider.edit', compact('Slider_Edit'));
    }
    public function update(Request $request,$id){
        try{
            DB::beginTransaction();
            $dataUpdate = [
                'name' => $request->name,
                'description' => $request->description,
            ];
            if($request->hasFile('image_path')){
                $file = $request->image_path;
                $fileNameOrigin = $file->getClientOriginalName();
                $fileNameHash = $file->getClientOriginalExtension();
                $path = $request->file('image_path')->storeAs('public/Slider/'.auth()->id(),$fileNameOrigin);
                $dataUploadTrait = [
                    'file_name'=>$fileNameOrigin,
                    'file_path'=>$url = Storage::url($path),
                ];
                if(!empty($dataUpdate)){
                    $dataUpdate['image_path'] = $dataUploadTrait['file_path'];
                    $dataUpdate['image_name'] = $dataUploadTrait['file_name'];
                }
            }
            $this->Slider->find($id)->update($dataUpdate);
            DB::commit();
            return redirect('admin/slider/index');
        }catch(Exception $exception){
            DB::rollBack();
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        }
    }
    public function delete($id){
        try{
            $this->Slider->find($id)->delete();
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
