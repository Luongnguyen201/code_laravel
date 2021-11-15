<?php

namespace App\Http\Controllers;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use App\Components\MenuRecusive;
use App\Models\Manus;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\MenuRequest;
use Illuminate\Support\Facades\DB;
class MenuController extends Controller
{
    private $data;
    private $manus;
    public function __construct(MenuRecusive $manuRecusive, Manus $manu)
    {
        $this->data = $manu;
        $this->manus = $manuRecusive;
    }
    public function index(){
        $manu = $this->data->paginate('10');
        return View('admin.manus.index',compact('manu'));
    }
    public function add_menus(){
        $optionselect = $this->manus->ManuRecusiveAdd();
        return View('admin.manus.add',compact('optionselect'));
    }
    public function saved_manus(MenuRequest $request){
        $this->data->create([
            'name'=> $request->name,
            'parent_id'=> $request->parent_id,
            'slug'=> $request->name,
        ]);
        return redirect('admin/menus/index-menu');
    }
    public function edit($id){
        $menu= $this->data->find($id);
        $optionselect = $this->manus->ManuRecusiveSelect($menu->parent_id);
        return View('admin.manus.edit',compact('optionselect','menu'));
    }
    public function update($id,Request $request){
        try{
            DB::beginTransaction();
                $this->data->find($id)->update([
                    'name'=>$request->name,
                    'parent_id'=>$request->parent_id,
                ]);
            DB::commit();
            return redirect('admin/menus/index-menu');
        }catch(Exception $exception){
            DB::rollBack();
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        }            
    }
    public function delete($id){
        try{
            $this->data->find($id)->delete();
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
