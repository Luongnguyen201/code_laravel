<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Investors;
use App\Http\Requests\InvestorsRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;
class InvestorsController extends Controller
{
    private $Investors;
    public function __construct(Investors $investors)
    {
        $this->Investors = $investors;
    }
    public function index(){
        $Investors = $this->Investors->paginate(5);
        return view('admin.investors.index',compact('Investors'));
    }
    public function create(){
        return view('admin.investors.add');
    }
    public function store(InvestorsRequest $request){
        $dataCreate = [
            'name'=> $request->name,
        ];
        if($request->hasFile('image_path')){
            $file = $request->image_path;
            $fileNameOrigin = $file->getClientOriginalName();
            $fileNameHash = $file->getClientOriginalExtension();
            $path = $request->file('image_path')->storeAs('public/Investors/'.auth()->id(),$fileNameOrigin);
            $dataUploadTrait = [
                'file_name'=>$fileNameOrigin,
                'file_path'=>$url = Storage::url($path),
            ];
        }
        if(!empty($dataUploadTrait)){
            $dataCreate['image_name'] = $dataUploadTrait['file_name'];
            $dataCreate['image_path'] = $dataUploadTrait['file_path'];
        }
        $this->Investors->create($dataCreate);
        return redirect('admin/investors/index');
    }
    public function edit($id){
        $Investors = $this->Investors->find($id);
        return view('admin.investors.edit', compact('Investors'));
    }
    public function update($id, Request $request){
        try{
            DB::beginTransaction();
            $dataUpdate = [
                'name' => $request->name,
            ];
            if($request->hasFile('image_path')){
                $file = $request->image_path;
                $fileNameOrigin = $file->getClientOriginalName();
                $fileNameHash = $file->getClientOriginalExtension();
                $path = $request->file('image_path')->storeAs('public/Investors/'.auth()->id(),$fileNameOrigin);
                $dataUploadTrait = [
                    'file_name'=>$fileNameOrigin,
                    'file_path'=>$url = Storage::url($path),
                ];
            }
            if(!empty($dataUploadTrait)){
                $dataUpdate['image_name'] = $dataUploadTrait['file_name'];
                $dataUpdate['image_path'] = $dataUploadTrait['file_path'];
            }
            $this->Investors->find($id)->update($dataUpdate);
            DB::commit();
            return redirect('admin/investors/index');
        }catch(Exception $exception){
            DB::rollBack();
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        }
        
    }
    public function delete($id){
        try{
            $this->Investors->find($id)->delete();
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
