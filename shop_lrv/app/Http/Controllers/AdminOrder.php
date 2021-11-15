<?php

namespace App\Http\Controllers;

use App\Models\DetailOd;
use App\Models\inventory;
use App\Models\Orders;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
class AdminOrder extends Controller
{
    private $Orders; 
    private $Products;
    private $DetailOrder;
    private $inventory;
    public function __construct(inventory $inventory ,Orders $orders, product $product, DetailOd $detailOd)
    {
        $this->inventory = $inventory;
        $this->Orders = $orders;
        $this->Products = $product;
        $this->DetailOrder = $detailOd;
    }

    public function index(){
        $success = $this->Orders->where('Status','Đơn hàng thành công')->count();
        $delivery = $this->Orders->where('Status','Đơn hàng đang giao')->count();
        $handle = $this->Orders->where('Status','Đơn hàng đang xử lý')->count();
        $cancel = $this->Orders->where('Status','Đơn hàng bị hủy')->count();

        $Order = $this->Orders->orderBy('id','desc')->paginate(5);

        $total_success = $this->Orders->where('Status','Đơn hàng thành công')->get();
        $count_total_success = 0;
        foreach ($total_success as $item){
            $count_total_success += $item['total_price'];
        } 
        $total_delivery = $this->Orders->where('Status','Đơn hàng đang giao')->get();
        $count_total_delivery= 0;
        foreach ($total_delivery as $item){
            $count_total_delivery += $item['total_price'];
        } 
        $total_handle= $this->Orders->where('Status','Đơn hàng đang xử lý')->get();
        $count_total_handle = 0;
        foreach ($total_handle as $item){
            $count_total_handle += $item['total_price'];
        } 
        $total_cancel = $this->Orders->where('Status','Đơn hàng bị hủy')->get();
        $count_total_cancel = 0;
        foreach ($total_cancel as $item){
            $count_total_cancel += $item['total_price'];
        } 
        
        $success_total = $count_total_success;
        $delivery_total =  $count_total_delivery;
        $handle_total = $count_total_handle;
        $cancel_total =  $count_total_cancel;

        return view('home_admin', compact('Order','success','delivery','handle','cancel','success_total','delivery_total','handle_total','cancel_total'));
    }
    public function detail_order($id){
        if( $this->Orders->find($id) != null){
            $Order = $this->Orders->find($id);
            $DetailOd = $this->DetailOrder->where('Dt_order_id', $id)->get();
            $product =  $this->Products->get();
            return view('admin.Order.detail_order', compact('Order','DetailOd','product'));
        }
    }
    public function update($id, Request $request){
        if($this->Orders->find($id) != null ){
            if($request->Status == 'Đơn hàng bị hủy'){
                $Detail = $this->DetailOrder->where('Dt_order_id', $id)->get();
                foreach($Detail as $detail){
                    $inventory = $this->inventory->where('id_product', $detail->Dt_product_id)->first();
                    if($inventory != null &&  $inventory['quantity_now'] <= $inventory['quantity_ori']){
                        $this->inventory->where('id_product', $detail->Dt_product_id)->update([
                            'quantity_now' => $inventory['quantity_now'] + $detail->Dt_quantity,
                        ]);   
                    }
                }
            }
            $update = [
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'address' => $request->address,
                'total_price' => $request->total_price,
                'payment_method' => $request->payment_method,
                'Status' => $request->Status,
                'Dtime' => $request->Dtime,
            ];
            $this->Orders->find($id)->update($update);
            $slug = Str::slug($request->name);
            return redirect('admin/Orders/detail_order/'.$id.'/'.$slug);
        }
    }

    public function search(){
        $success = $this->Orders->where('Status','Đơn hàng thành công')->count();
        $delivery = $this->Orders->where('Status','Đơn hàng đang giao')->count();
        $handle = $this->Orders->where('Status','Đơn hàng đang xử lý')->count();
        $cancel = $this->Orders->where('Status','Đơn hàng bị hủy')->count();

        $search_text = $_GET['search'];
        if( $search_text != null ){
            if( $this->Orders->where('name', 'LIKE', '%'.$search_text.'%')->get() != null  and $this->Orders->where('name', 'LIKE', '%'.$search_text.'%')->count() > 0  ){
                $Order = $this->Orders->orderBy('id','desc')->where('name', 'LIKE', '%'.$search_text.'%')->paginate(5);
            }elseif($this->Orders->orderBy('id','desc')->where('id', 'LIKE', '%'.$search_text.'%')->get() != null and $this->Orders->where('id', 'LIKE', '%'.$search_text.'%')->count() > 0){
                $Order = $this->Orders->where('id', 'LIKE', '%'.$search_text.'%')->paginate(5);
            }elseif($this->Orders->where('phone_number', 'LIKE', '%'.$search_text.'%')->get() != null and $this->Orders->where('phone_number', 'LIKE', '%'.$search_text.'%')->count() > 0){
                $Order = $this->Orders->orderBy('id','desc')->where('phone_number', 'LIKE', '%'.$search_text.'%')->paginate(5);
            }elseif($this->Orders->where('email', 'LIKE', '%'.$search_text.'%')->get() != null and $this->Orders->where('email', 'LIKE', '%'.$search_text.'%')->count() > 0){
                $Order = $this->Orders->orderBy('id','desc')->where('email', 'LIKE', '%'.$search_text.'%')->paginate(5);
            }elseif($this->Orders->where('address', 'LIKE', '%'.$search_text.'%')->get() != null and $this->Orders->where('address', 'LIKE', '%'.$search_text.'%')->count() > 0){
                $Order = $this->Orders->orderBy('id','desc')->where('address', 'LIKE', '%'.$search_text.'%')->paginate(5);
            }elseif($this->Orders->where('total_price', 'LIKE', '%'.$search_text.'%')->get() != null and $this->Orders->where('total_price', 'LIKE', '%'.$search_text.'%')->count() > 0){
                $Order = $this->Orders->orderBy('id','desc')->where('total_price', 'LIKE', '%'.$search_text.'%')->paginate(5);
            }elseif($this->Orders->where('payment_method', 'LIKE', '%'.$search_text.'%')->get() != null and $this->Orders->where('payment_method', 'LIKE', '%'.$search_text.'%')->count() > 0){
                $Order = $this->Orders->orderBy('id','desc')->where('payment_method', 'LIKE', '%'.$search_text.'%')->paginate(5);
            }elseif($this->Orders->where('Status', 'LIKE', '%'.$search_text.'%')->get() != null and $this->Orders->where('Status', 'LIKE', '%'.$search_text.'%')->count() > 0){
                $Order = $this->Orders->orderBy('id','desc')->where('Status', 'LIKE', '%'.$search_text.'%')->paginate(5);
            }elseif($this->Orders->where('Dtime', 'LIKE', '%'.$search_text.'%')->get() != null and $this->Orders->where('Dtime', 'LIKE', '%'.$search_text.'%')->count() > 0){
                $Order = $this->Orders->orderBy('id','desc')->where('Dtime', 'LIKE', '%'.$search_text.'%')->paginate(5);
            }else{
                $Order = $this->Orders->orderBy('id','desc')->paginate(5);
            }
        }else{
            $Order = $this->Orders->orderBy('id','desc')->paginate(5);
        }
        $total_success = $this->Orders->where('Status','Đơn hàng thành công')->get();
        $count_total_success = 0;
        foreach ($total_success as $item){
            $count_total_success += $item['total_price'];
        } 
        $total_delivery = $this->Orders->where('Status','Đơn hàng đang giao')->get();
        $count_total_delivery= 0;
        foreach ($total_delivery as $item){
            $count_total_delivery += $item['total_price'];
        } 
        $total_handle= $this->Orders->where('Status','Đơn hàng đang xử lý')->get();
        $count_total_handle = 0;
        foreach ($total_handle as $item){
            $count_total_handle += $item['total_price'];
        } 
        $total_cancel = $this->Orders->where('Status','Đơn hàng bị hủy')->get();
        $count_total_cancel = 0;
        foreach ($total_cancel as $item){
            $count_total_cancel += $item['total_price'];
        } 
        
        $success_total = $count_total_success;
        $delivery_total =  $count_total_delivery;
        $handle_total = $count_total_handle;
        $cancel_total =  $count_total_cancel;

        return view('home_admin', compact('Order','success','delivery','handle','cancel','success_total','delivery_total','handle_total','cancel_total'));
    }
    public function success(){
        $success = $this->Orders->where('Status','Đơn hàng thành công')->count();
        $delivery = $this->Orders->where('Status','Đơn hàng đang giao')->count();
        $handle = $this->Orders->where('Status','Đơn hàng đang xử lý')->count();
        $cancel = $this->Orders->where('Status','Đơn hàng bị hủy')->count();

        $Order = $this->Orders->orderBy('id','desc')->where('Status','Đơn hàng thành công')->paginate(5);
        $total_success = $this->Orders->where('Status','Đơn hàng thành công')->get();
        $count_total_success = 0;
        foreach ($total_success as $item){
            $count_total_success += $item['total_price'];
        } 
        $total_delivery = $this->Orders->where('Status','Đơn hàng đang giao')->get();
        $count_total_delivery= 0;
        foreach ($total_delivery as $item){
            $count_total_delivery += $item['total_price'];
        } 
        $total_handle= $this->Orders->where('Status','Đơn hàng đang xử lý')->get();
        $count_total_handle = 0;
        foreach ($total_handle as $item){
            $count_total_handle += $item['total_price'];
        } 
        $total_cancel = $this->Orders->where('Status','Đơn hàng bị hủy')->get();
        $count_total_cancel = 0;
        foreach ($total_cancel as $item){
            $count_total_cancel += $item['total_price'];
        } 
        
        $success_total = $count_total_success;
        $delivery_total =  $count_total_delivery;
        $handle_total = $count_total_handle;
        $cancel_total =  $count_total_cancel;

        return view('home_admin', compact('Order','success','delivery','handle','cancel','success_total','delivery_total','handle_total','cancel_total'));
    }
    public function delivery(){
        $success = $this->Orders->where('Status','Đơn hàng thành công')->count();
        $delivery = $this->Orders->where('Status','Đơn hàng đang giao')->count();
        $handle = $this->Orders->where('Status','Đơn hàng đang xử lý')->count();
        $cancel = $this->Orders->where('Status','Đơn hàng bị hủy')->count();

        $Order = $this->Orders->orderBy('id','desc')->where('Status','Đơn hàng đang giao')->paginate(5);
        $total_success = $this->Orders->where('Status','Đơn hàng thành công')->get();
        $count_total_success = 0;
        foreach ($total_success as $item){
            $count_total_success += $item['total_price'];
        } 
        $total_delivery = $this->Orders->where('Status','Đơn hàng đang giao')->get();
        $count_total_delivery= 0;
        foreach ($total_delivery as $item){
            $count_total_delivery += $item['total_price'];
        } 
        $total_handle= $this->Orders->where('Status','Đơn hàng đang xử lý')->get();
        $count_total_handle = 0;
        foreach ($total_handle as $item){
            $count_total_handle += $item['total_price'];
        } 
        $total_cancel = $this->Orders->where('Status','Đơn hàng bị hủy')->get();
        $count_total_cancel = 0;
        foreach ($total_cancel as $item){
            $count_total_cancel += $item['total_price'];
        } 
        
        $success_total = $count_total_success;
        $delivery_total =  $count_total_delivery;
        $handle_total = $count_total_handle;
        $cancel_total =  $count_total_cancel;

        return view('home_admin', compact('Order','success','delivery','handle','cancel','success_total','delivery_total','handle_total','cancel_total'));
    }
    public function handle(){
        $success = $this->Orders->where('Status','Đơn hàng thành công')->count();
        $delivery = $this->Orders->where('Status','Đơn hàng đang giao')->count();
        $handle = $this->Orders->where('Status','Đơn hàng đang xử lý')->count();
        $cancel = $this->Orders->where('Status','Đơn hàng bị hủy')->count();

        $Order = $this->Orders->orderBy('id','desc')->where('Status','Đơn hàng đang xử lý')->paginate(5);
        $total_success = $this->Orders->where('Status','Đơn hàng thành công')->get();
        $count_total_success = 0;
        foreach ($total_success as $item){
            $count_total_success += $item['total_price'];
        } 
        $total_delivery = $this->Orders->where('Status','Đơn hàng đang giao')->get();
        $count_total_delivery= 0;
        foreach ($total_delivery as $item){
            $count_total_delivery += $item['total_price'];
        } 
        $total_handle= $this->Orders->where('Status','Đơn hàng đang xử lý')->get();
        $count_total_handle = 0;
        foreach ($total_handle as $item){
            $count_total_handle += $item['total_price'];
        } 
        $total_cancel = $this->Orders->where('Status','Đơn hàng bị hủy')->get();
        $count_total_cancel = 0;
        foreach ($total_cancel as $item){
            $count_total_cancel += $item['total_price'];
        } 
        
        $success_total = $count_total_success;
        $delivery_total =  $count_total_delivery;
        $handle_total = $count_total_handle;
        $cancel_total =  $count_total_cancel;

        return view('home_admin', compact('Order','success','delivery','handle','cancel','success_total','delivery_total','handle_total','cancel_total'));
    }
    public function cancel(){
        $success = $this->Orders->where('Status','Đơn hàng thành công')->count();
        $delivery = $this->Orders->where('Status','Đơn hàng đang giao')->count();
        $handle = $this->Orders->where('Status','Đơn hàng đang xử lý')->count();
        $cancel = $this->Orders->where('Status','Đơn hàng bị hủy')->count();

        $Order = $this->Orders->orderBy('id','desc')->where('Status','Đơn hàng bị hủy')->paginate(5);
        $total_success = $this->Orders->where('Status','Đơn hàng thành công')->get();
        $count_total_success = 0;
        foreach ($total_success as $item){
            $count_total_success += $item['total_price'];
        } 
        $total_delivery = $this->Orders->where('Status','Đơn hàng đang giao')->get();
        $count_total_delivery= 0;
        foreach ($total_delivery as $item){
            $count_total_delivery += $item['total_price'];
        } 
        $total_handle= $this->Orders->where('Status','Đơn hàng đang xử lý')->get();
        $count_total_handle = 0;
        foreach ($total_handle as $item){
            $count_total_handle += $item['total_price'];
        } 
        $total_cancel = $this->Orders->where('Status','Đơn hàng bị hủy')->get();
        $count_total_cancel = 0;
        foreach ($total_cancel as $item){
            $count_total_cancel += $item['total_price'];
        } 
        
        $success_total = $count_total_success;
        $delivery_total =  $count_total_delivery;
        $handle_total = $count_total_handle;
        $cancel_total =  $count_total_cancel;

        return view('home_admin', compact('Order','success','delivery','handle','cancel','success_total','delivery_total','handle_total','cancel_total'));
    }

    public function delete($id){
        return redirect('admin/Orders/index');
    }
}
