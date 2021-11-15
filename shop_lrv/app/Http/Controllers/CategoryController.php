<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use App\Components\Recusive;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CotegoryRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class CategoryController extends Controller
{
    private $htmlseclect;
    private $category;

    public function __construct(Category $category)
    {
        $this->category=$category;
    }
    public function add_cotegory_product($parent_id=''){
            $htmloption=$this->getCotegory($parent_id='');
            return View('admin.category.add',compact('htmloption'));
    }
    public function saved_category(CotegoryRequest $request){
        $dataCotegory = ([
            'name'=>$request->name,
            'parent_id'=>$request->parent_id,
            'slug'=> $request->name,
        ]);
        if($request->hasFile('image_path')){
            $file = $request->image_path;
            $fileNameOrigin = $file->getClientOriginalName();
            $fileNameHash = $file->getClientOriginalExtension();
            $path = $request->file('image_path')->storeAs('public/category/'.auth()->id(),$fileNameOrigin);
            $dataUploadTrait = [
                'file_name'=>$fileNameOrigin,
                'file_path'=>$url = Storage::url($path),
            ];
        }
        if(!empty($dataUploadTrait)){
            $dataCotegory['image_name'] = $dataUploadTrait['file_name'];
            $dataCotegory['image_path'] = $dataUploadTrait['file_path'];
        }
        $this->category->create($dataCotegory);
        return redirect('admin/category/index');
    }

    public function index(){
        $category = $this->category->latest()->paginate(5);
        return View('admin.category.index', compact('category'));
    }
    public function getCotegory($parent_id){
        $data = $this->category->all();
        $recusive = new Recusive($data);
        $htmloption=$recusive->cateCategoryRecusive($parent_id);
        return $htmloption;
    }
    public function delete($id){
        try{
            $this->category->find($id)->delete();
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
    public function edit($id){
        $category= $this->category->find($id);
        $htmloption=$this->getCotegory($category->parent_id);
        return View('admin.category.edit',compact('category','htmloption'));
    }
    public function update($id,Request $request){
        try{
            DB::beginTransaction();
            $UpdateCategory = ([
                'name'=>$request->name,
                'parent_id'=>$request->parent_id,
                'slug'=> $request->name,
            ]);
            if($request->hasFile('image_path')){
                $file = $request->image_path;
                $fileNameOrigin = $file->getClientOriginalName();
                $fileNameHash = $file->getClientOriginalExtension();
                $path = $request->file('image_path')->storeAs('public/category/'.auth()->id(),$fileNameOrigin);
                $dataUploadTrait = [
                    'file_name'=>$fileNameOrigin,
                    'file_path'=>$url = Storage::url($path),
                ];
            }
            if(!empty($dataUploadTrait)){
                $UpdateCategory['image_name'] = $dataUploadTrait['file_name'];
                $UpdateCategory['image_path'] = $dataUploadTrait['file_path'];
            }
            $this->category->find($id)->update($UpdateCategory);
            DB::commit();
            return redirect('admin/category/index');
        }catch(Exception $exception){
            DB::rollBack();
            Log::error('message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        }
    }
}
