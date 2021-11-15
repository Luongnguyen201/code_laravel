<?php

namespace App\Http\Controllers;

use App\Components\Cart;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Contact;
use App\Models\DetailOd;
use App\Models\inventory;
use App\Models\Investors;
use App\Models\Orders;
use App\Models\Payments;
use App\Models\product;
use App\Models\ProductAds;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private $inventory;
    private $Slider;
    private $Category;
    private $Product;
    private $Setting;
    private $ProductAds;
    private $Investors;
    private $Payments;
    private $Orders;
    private $DetailOd;
    private $Contact;
    private $User;
    public function __construct(inventory $inventory ,User $user ,Contact $contact,Payments $payments,DetailOd $detailOd, Orders $orders ,Slider $slider, Category $category, product $product, Setting $setting, ProductAds $productAds,Investors $investors)
    {
        $this->inventory = $inventory;
        $this->User = $user;
        $this->Slider = $slider;
        $this->Category = $category;
        $this->Product = $product;
        $this->Setting = $setting;
        $this->ProductAds = $productAds; 
        $this->Investors = $investors;
        $this->Orders = $orders;
        $this->Payments = $payments;
        $this->DetailOd = $detailOd;
        $this->Contact = $contact;
    }

    public function Cart(){
        $Contact =$this->Contact->get();
        $Setting = $this->Setting->latest()->get();
        return View('Frontend.Cart',compact('Setting','Contact'));
    }
    public function AddCart(Request $request, $id){
        $Product = $this->Product->find($id);
        if($Product != null){
            $oldCart = Session('Cart') ? Session('Cart') : null ;
            $newCart = new Cart($oldCart);
            $newCart->Add_Cart($Product,$id,$request->quantity,$request->color_product);

            $request->session()->put('Cart',$newCart);
        }
        // dd($newCart);
        return redirect('Cart');
    }

    public function increaseQuantity(Request $request, $id){
        $Product = $this->Product->find($id);
        $quantity = 1;
        if($Product != null){
            $oldCart = Session('Cart') ? Session('Cart') : null ;
            $newCart = new Cart($oldCart);
            $newCart->increaseQuantity($id,$quantity,$Product);
            $request->session()->put('Cart',$newCart);
        }
        return redirect('Cart');
    }

    public function decreaseQuantity(Request $request, $id){
        $Product = $this->Product->find($id);
        $quantity = 1;
        if($Product != null){
            $oldCart = Session('Cart') ? Session('Cart') : null ;
            $newCart = new Cart($oldCart);
            $newCart->decreaseQuantity($id,$quantity,$Product);
            $request->session()->put('Cart',$newCart);
        }
        return redirect('Cart');
    }

    public function delete(Request $request, $id){
            $oldCart = Session('Cart') ? Session('Cart') : null ;
            $newCart = new Cart($oldCart);
            $newCart->DeleteItemCart($id);
            if( ($newCart->Products) > 0 ){
                $request->session()->put('Cart', $newCart);
            }else{
                $request->session()->forget('Cart');
            }
        return redirect('Cart');
    }

    public function DeleteCart(Request $request){
        $oldCart = Session('Cart') ? Session('Cart') : null ;
        $newCart = new Cart($oldCart);
        $request->session()->forget('Cart');
        return redirect('Cart');
    }

    public function Order(){
        $Setting = $this->Setting->latest()->get();
        $Contact =$this->Contact->get();
        if(Auth::check()){
            $id = Auth::id();
            $User = $this->User->find($id);
            if(Session::has('Cart') != null && Session::get('Cart')->TotalQuantity > 0 ){
                return view('Frontend.Order', compact('Setting','Contact','User'));  
            }
        }else{
            $User = null;
            if(Session::has('Cart') != null && Session::get('Cart')->TotalQuantity > 0 ){
                return view('Frontend.Order', compact('Setting','Contact','User'));  
            }
        }
        
        
    }
    public function postPay(Request $request){
    //   $token = csrf_token();        
        if($request->payment == 2){
            $total = Session::get('Cart')->TotalPrice;

            $data = [
                'name' => $request->name,
                'phone_number' => $request->Number_phone,
                'email' => $request->email,
                'address' => $request->address,
                'Other_re' => $request->other_re,
                'total_price' => $total,
                'payment_method' => 'Thanh toán online',
                'Status' => 'Đơn hàng đang xử lý',
                'Dtime' => Carbon::now('Asia/Ho_Chi_Minh'),


            ];
            $Setting = $this->Setting->latest()->get();
            session(['info_customer' => $data ]);
            $Contact =$this->Contact->get();
            return view('Frontend.Vnpay.index', compact('Setting','total','Contact'));
        }else{
            try{
                DB::beginTransaction();
                $total = Session::get('Cart')->TotalPrice;
                $data = [
                    'name' => $request->name,
                    'phone_number' => $request->Number_phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'Other_re' => $request->other_re,
                    'total_price' => $total,
                    'payment_method' => 'Thanh toán khi nhận hàng',
                    'Status' => 'Đơn hàng đang xử lý',
                    'Dtime' => Carbon::now('Asia/Ho_Chi_Minh'),

                ];
                session(['info_customer' => $data ]);
                if( $request->all() != null && Session::get('info_customer') != null && Session::get('Cart') != null ){
                    
                    $data = Session::get('info_customer');
                    $id_order =  $this->Orders->insertGetId($data); 
                    $oldCart = Session('Cart') ? Session('Cart') : null ;
                    $dataCart = new Cart($oldCart);
                    foreach($dataCart->Products as $item){
                        $dataDetail = [
                            'Dt_order_id' => $id_order,
                            'Dt_product_id' =>  $item['productInfo']->id,
                            'Dt_quantity' => $item['quantity'],
                            'Dt_color' => $item['color'],
                            'Dt_price' => $item['price'],
                            'promotion' => $item['promotion']
                        ];
                        $inventory = $this->inventory->where('id_product',$dataDetail['Dt_product_id'])->first();
                        if( $inventory != null ){
                            if($inventory['quantity_now'] != 0 ){
                                $this->inventory->where('id_product',$dataDetail['Dt_product_id'])->update([
                                    'quantity_now' => $inventory['quantity_now'] -  $dataDetail['Dt_quantity'],
                                ]);
                            }else{
                                $this->inventory->where('id_product', $dataDetail['Dt_product_id'])->update([
                                    'quantity_now' => $inventory['quantity_ori'] - $dataDetail['Dt_quantity'], 
                                ]);
                            }        
                        }
                        $this->DetailOd->create($dataDetail);
                    }

                    $Setting = $this->Setting->latest()->get();
                    $name =  $data['name'];
                    $emailclient = $data['email'];
                    $pt_order = $data['payment_method'];
                    $vnp_bank = $request->vnp_BankCode;
                    Mail::send('Frontend.emails.sendmail_order', compact('name','pt_order','vnp_bank'), function($email) use($name,$emailclient){
                        $email->subject('[PHONE-STORE] Đơn hàng mới');
                        $email->to( $emailclient , $name);
                    });
                    DB::commit();
                    Session::forget('Cart');
                    Session::forget('info_customer');
                    $Return_order_online = null;
                    $Return_order_offline = $this->Orders->find($id_order);
                    $Contact =$this->Contact->get();
                    return view('Frontend.Vnpay.Order_Success', compact('Return_order_online','Return_order_offline','Setting','Contact'));
                }else{
                    DB::commit();
                    return view('errors.404');
                }
                
            }catch(Exception $exception){
                DB::rollBack();
                Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
            }
        }
    }

    public function createPayment(Request $request){
        $vnp_TmnCode = "A5JLV2RD";
        $vnp_HashSecret = "RKPBXRDAQAIRUVSRSXAYOPWGCGPNUAJS";
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost:8080/shop_lrv/return_vnp";
        $vnp_TxnRef = Str::random(15); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = $_POST['order_desc']; //nội dung thanh toán
        $vnp_OrderType = $_POST['order_type']; //loại thanh toán
        $vnp_Amount = $_POST['amount'] * 100; // Số tiền thanh toán
        $vnp_Locale = $_POST['language']; //ngôn ngữ
        $vnp_BankCode = $_POST['bank_code']; //ngân hàng
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request){
    
        try{
            DB::beginTransaction();
            if( $request->all() != null && Session::get('info_customer') != null && Session::get('Cart') != null ){
                $data = Session::get('info_customer');
                $id_order =  $this->Orders->insertGetId($data); 
                $oldCart = Session('Cart') ? Session('Cart') : null ;
                $dataCart = new Cart($oldCart);
                foreach($dataCart->Products as $item){
                    $dataDetail = [
                        'Dt_order_id' => $id_order,
                        'Dt_product_id' =>  $item['productInfo']->id,
                        'Dt_quantity' => $item['quantity'],
                        'Dt_color' => $item['color'],
                        'Dt_price' => $item['price'],
                        'promotion' => $item['promotion'],
                    ];
                    $inventory = $this->inventory->where('id_product',$dataDetail['Dt_product_id'])->first();
                        if( $inventory != null ){
                            if($inventory['quantity_now'] != 0 ){
                                $this->inventory->where('id_product',$dataDetail['Dt_product_id'])->update([
                                    'quantity_now' => $inventory['quantity_now'] -  $dataDetail['Dt_quantity'],
                                ]);
                            }else{
                                $this->inventory->where('id_product', $dataDetail['Dt_product_id'])->update([
                                    'quantity_now' => $inventory['quantity_ori'] - $dataDetail['Dt_quantity'], 
                                ]);
                            }        
                        }
                    $this->DetailOd->create($dataDetail);
                }
                $vnpayData = [
                    'p_order_id' => $id_order,
                    'p_money' => $request->vnp_Amount/100,
                    'p_transation_code' => $request->vnp_TxnRef,
                    'p_note' => $request->vnp_OrderInfo,
                    'p_vnp_response_code' => $request->vnp_TransactionStatus,
                    'p_code_vnpay' => $request->vnp_TransactionNo,
                    'p_code_bank' => $request->vnp_BankCode,
                    'p_time' => $request->vnp_PayDate,
                ];
                $Success_id = $this->Payments->insertGetId($vnpayData);
                $Setting = $this->Setting->latest()->get();
                $name =  $data['name'];
                $emailclient = $data['email'];
                $pt_order =  $data['payment_method'];
                $vnp_bank = $request->vnp_BankCode;
                Mail::send('Frontend.emails.sendmail_order', compact('name','pt_order','vnp_bank'), function($email) use($name,$emailclient){
                    $email->subject('[PHONE-STORE] Đơn hàng mới');
                    $email->to( $emailclient , $name);
                });
                DB::commit();
                Session::forget('Cart');
                Session::forget('info_customer');
                $Return_order_offline = null;
                $Return_order_online = $this->Payments->find($Success_id);
                $Contact =$this->Contact->get();
                return view('Frontend.Vnpay.Order_Success', compact('Return_order_online','Return_order_offline','Setting','Contact'));
            }else{
                DB::commit();
                return view('errors.bug_payment');
            }
            
        }catch(Exception $exception){
            DB::rollBack();
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        }

    }
}
