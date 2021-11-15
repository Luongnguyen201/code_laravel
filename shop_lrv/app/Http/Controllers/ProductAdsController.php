<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductAds;
use App\Http\Requests\ProductAdsRequest;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class ProductAdsController extends Controller
{
    private $ProductAds;
    public function __construct(ProductAds $productAds)
    {
        $this->ProductAds = $productAds;
    }
    public function index(){
        $SliderProductAds = $this->ProductAds->paginate(5);
        return view('admin.product_Ads.index', compact('SliderProductAds'));
    }
    public function create(){
        return view('admin.product_Ads.add');
    }
    public function store(ProductAdsRequest $request){
        $dataCreate = [
            'text' => $request->text,
            'name' => $request->name,
            'textarea' => $request->textarea,
        ];
        if($request->hasFile('image_path')){
            $file = $request->image_path;
            $fileNameOrigin = $file->getClientOriginalName();
            $fileNameHash = $file->getClientOriginalExtension();
            $path = $request->file('image_path')->storeAs('public/productAds/'.auth()->id(),$fileNameOrigin);
            $dataUploadTrait = [
                'file_name'=>$fileNameOrigin,
                'file_path'=>$url = Storage::url($path),
            ];
        }
        if(!empty($dataUploadTrait)){
            $dataCreate['image_name'] = $dataUploadTrait['file_name'];
            $dataCreate['image_path'] = $dataUploadTrait['file_path'];
        }
        $this->ProductAds->create($dataCreate);
        return redirect('admin/productads/index');
    }
    public function edit($id){
        $ProductAds = $this->ProductAds->find($id);
        return view('admin.product_Ads.edit', compact('ProductAds'));
    }
    public function update($id, Request $request){
        try{
            DB::beginTransaction();
            $dataUpdate = [
                'text' => $request->text,
                'name' => $request->name,
                'textarea' => $request->textarea,
            ];
            if($request->hasFile('image_path')){
                $file = $request->image_path;
                $fileNameOrigin = $file->getClientOriginalName();
                $fileNameHash = $file->getClientOriginalExtension();
                $path = $request->file('image_path')->storeAs('public/productAds/'.auth()->id(),$fileNameOrigin);
                $dataUploadTrait = [
                    'file_name'=>$fileNameOrigin,
                    'file_path'=>$url = Storage::url($path),
                ];
            }
            if(!empty($dataUploadTrait)){
                $dataUpdate['image_name'] = $dataUploadTrait['file_name'];
                $dataUpdate['image_path'] = $dataUploadTrait['file_path'];
            }
            $this->ProductAds->find($id)->update($dataUpdate);
            DB::commit();
            return redirect('admin/productads/index');
        }catch(Exception $exception){
            DB::rollBack();
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        }
    }
    public function delete($id){
        try{
            $this->ProductAds->find($id)->delete();
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
