<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\product;
use App\Models\ProductImage;
use Facade\FlareClient\View;
use App\Components\Recusive;
use App\Http\Requests\ProductRequest;
use App\Models\Color;
use App\Models\inventory;
use App\Models\ProductTag;
use App\Models\tag;
use App\Models\ViewModel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminProductController extends Controller
{
    private $category;
    private $product;
    private $productImage;
    private $tagProduct;
    private $tags;
    private $Color;
    private $inventory;
    private $views;
    public function __construct(ViewModel $viewModel ,inventory $inventory,Category $category, product $Product, ProductImage $productimg, tag $tags, ProductTag $productTag, Color $color)
    {
        $this->views = $viewModel;
        $this->inventory  = $inventory;
        $this->category = $category;
        $this->product = $Product;
        $this->productImage = $productimg;
        $this->tagProduct = $productTag;
        $this->tags = $tags;
        $this->Color = $color;
    }
    public function index(){
        $dataProducts = $this->product->latest()->paginate(5);
        return view('admin.product.index', compact('dataProducts'));
    }
    public function add(){
        $htmloption=$this->getCotegory($parent_id='');
        return view('admin.product.add',compact('htmloption'));
    }
    public function getCotegory($parent_id){
        $data = $this->category->all();
        $recusive = new Recusive($data);
        $htmloption = $recusive->cateCategoryRecusive($parent_id);
        return $htmloption;
    }
    public function saved(ProductRequest $request){
        try{
            DB::beginTransaction();
            $dataProductCreate = [
                'name' => $request->name,
                'import_price' => $request->import_price,
                'price' =>  $request->price,
                'promotion' => $request->promotion,
                'content' => $request->content,
                'content_detail' => $request->content_detail,
                'user' => auth()->id(),
                'category' => $request->parent_id,
            ];
            if($request->hasFile('feature_image_path')){
                $file = $request->feature_image_path;
                $fileNameOrigin = $file->getClientOriginalName();
                $fileNameHash = $file->getClientOriginalExtension();
                $path = $request->file('feature_image_path')->storeAs('public/product/'.auth()->id(),$fileNameOrigin);
                $dataUploadTrait = [
                    'file_name'=>$fileNameOrigin,
                    'file_path'=>$url = Storage::url($path),
                ];
            }
            if(!empty($dataUploadTrait)){
                $dataProductCreate['feature_image_name'] = $dataUploadTrait['file_name'];
                $dataProductCreate['feature_image_path'] = $dataUploadTrait['file_path'];
            }
            $products = $this->product->create($dataProductCreate);
            //insert data to product images
            if( $request->hasFile('image_path') ){
                foreach( $request->image_path as $fileItem ){
                    $fileNameOrigin = $fileItem->getClientOriginalName();
                    $fileNameHash = $fileItem->getClientOriginalExtension();
                    $path = $fileItem->storeAs('public/product/'. auth()->id(), $fileNameOrigin);
                    $dataUploadTrait = [
                        'file_name' => $fileNameOrigin,
                        'file_path' => $url = Storage::url($path),
                    ];
                    $products->images()->create([
                        'image_path' => $dataUploadTrait['file_path'],
                        'image_name' => $dataUploadTrait['file_name'],
                    ]);
                } 
            }
            //insert tags for product 
            if(!empty($request->tags)){
                foreach( $request->tags as $tagItem){
                    //insert to tags
                    $tagInstance = $this->tags->firstOrCreate([ 'name' => $tagItem ]);
                    $tagIds[] = $tagInstance->id;
                }
            }
            $products->tags()->attach($tagIds);

            if(!empty($request->color)){
                foreach($request->color as $Item){
                    $Color_Instance = $this->Color->firstOrCreate([ 'name' => $Item ]);
                    $colorID[] = $Color_Instance->id;
                }
            }
            $products->product_color()->attach($colorID);
            $this->inventory->create([
                'quantity_ori' => $request->quantity,
                'id_product' => $products->id,
            ]);

            $this->views->create([
                'id_product' => $products->id,
            ]);

            DB::commit();
            return redirect('admin/product/index');
        }catch(Exception $exception){
            DB::rollBack();
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        }
            
       
    }
    public function edit($id){
        $id_product = $id;
        $product = $this->product->find($id);
        $htmloption = $this->getCotegory($product->category);
        $check_quantity = $this->inventory->where('id_product',$id)->first();
        if($check_quantity != null){
            $quantity = $this->inventory->where('id_product',$id)->first();
        }else{
            $quantity = null;
        }
        return view('admin.product.edit', compact('htmloption','product','id_product','quantity'));
    }
    public function update(Request $request,$id){
        try{
            DB::beginTransaction();
            $dataProductUpdate = [
                'name' => $request->name,
                'import_price' => $request->import_price,
                'price' =>  $request->price,
                'promotion' => $request->promotion,
                'content' => $request->content,
                'content_detail' => $request->content_detail,
                'user' => auth()->id(),
                'category' => $request->parent_id,
            ];
            if($request->hasFile('feature_image_path')){
                $file = $request->feature_image_path;
                $fileNameOrigin = $file->getClientOriginalName();
                $fileNameHash = $file->getClientOriginalExtension();
                $path = $request->file('feature_image_path')->storeAs('public/product/'.auth()->id(),$fileNameOrigin);
                $dataUploadTrait = [
                    'file_name'=>$fileNameOrigin,
                    'file_path'=>$url = Storage::url($path),
                ];
            }
            if(!empty($dataUploadTrait)){
                $dataProductUpdate['feature_image_name'] = $dataUploadTrait['file_name'];
                $dataProductUpdate['feature_image_path'] = $dataUploadTrait['file_path'];
            }
            $products = $this->product->find($id)->update($dataProductUpdate);
            $products = $this->product->find($id);
    
            //insert data to product image
            if( $request->hasFile('image_path') ){
                $this->productImage->where('product_id', $id)->delete();
                foreach( $request->image_path as $fileItem ){
                    $fileNameOrigin = $fileItem->getClientOriginalName();
                    $fileNameHash = $fileItem->getClientOriginalExtension();
                    $path = $fileItem->storeAs('public/product/'. auth()->id(), $fileNameOrigin);
                    $dataUploadTrait = [
                        'file_name' => $fileNameOrigin,
                        'file_path' => $url = Storage::url($path),
                    ];
                    $products->images()->create([
                        'image_path' => $dataUploadTrait['file_path'],
                        'image_name' => $dataUploadTrait['file_name'],
                    ]);
                } 
            }
            //insert tags for product 
            if(!empty($request->tags)){
                foreach( $request->tags as $tagItem){
                    //insert to tags
                    $tagInstance = $this->tags->firstOrCreate([ 'name' => $tagItem ]);
                    $tagIds[] = $tagInstance->id;
                }
            }
                //insert to productTag
            $products->tags()->sync($tagIds);

            if(!empty($request->color)){
                foreach($request->color as $Item){
                    $Color_Instance = $this->Color->firstOrCreate(['name' => $Item]);
                    $colorID[] = $Color_Instance->id;
                }
            }
            $products->product_color()->sync($colorID);
            if($request->quantity != null){
                $inventory = $this->inventory->where('id_product',$id)->first();
                if( $inventory == null ){
                    $this->inventory->create([
                        'quantity_ori' => $request->quantity,
                        'id_product' => $id,
                    ]);
                }else{
                    if($inventory->quantity_ori != null ){
                        $this->inventory->where('id_product',$id)->update([
                            'quantity_ori' => $inventory['quantity_ori']+$request->quantity,
                        ]);
                    }else{
                        $this->inventory->where('id_product',$id)->update([
                            'quantity_ori' => $request->quantity,
                        ]);
                    }
                }       
            }
            DB::commit();
            return redirect('admin/product/index');
        }catch(Exception $exception){
            DB::rollBack();
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        }
        
    }
    public function delete($id){
        try{
            $this->product->find($id)->delete();
            $this->views->where('id_product', $id)->delete();
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

