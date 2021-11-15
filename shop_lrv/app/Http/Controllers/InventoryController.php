<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DetailOd;
use App\Models\inventory;
use App\Models\Orders;
use Illuminate\Http\Request;
use App\Models\product;
use Exception;
use Illuminate\Support\Facades\Log;
class InventoryController extends Controller
{
    private $Category;
    private $Product;
    private $Inventory;
    private $Order;
    private $DetailOd;
    public function __construct(DetailOd $detailOd, Orders $orders,Category $category ,product $product, inventory $inventory)
    {
        $this->DetailOd = $detailOd;
        $this->Order = $orders;
        $this->Category = $category;
        $this->Product  = $product;
        $this->Inventory = $inventory;

    }
    public function index(){
        $Category = $this->Category->get();
        $Inventory = $this->Inventory->latest()->paginate(10);
        return view('admin.inventory.index', compact('Inventory','Category'));
    }
    public function detail_order($id){
        $Detail = $this->DetailOd->where('Dt_product_id', $id)->get();
        $Detailcount = $this->DetailOd->where('Dt_product_id', $id)->count();
        if($Detailcount != 0 ){
            foreach($Detail as $item){
                $arr[] =['id' =>  $item->Dt_order_id, ]; 
            }
            foreach($arr as $item){ 
            $i = $this->Order->find($item['id']);
            $Order[] = [
                'id' => $i->id,
                'name' => $i->name,
                'phone_number' => $i->phone_number,
                'email' => $i->email,
                'address' => $i->address,
                'total_price' => $i->total_price,
                'payment_method' => $i->payment_method,
                'status' => $i->Status,
                'time' => $i->Dtime,
            ];
            }
            return view('admin.inventory.detail_order', compact('Order'));
        }else{
            return redirect('admin/inventory/index');
        }
    }
    public function delete($id){
        try{
            $this->Inventory->find($id)->delete();
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
