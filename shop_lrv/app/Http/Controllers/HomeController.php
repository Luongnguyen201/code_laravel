<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use App\Models\DetailOd;
use App\Models\Investors;
use App\Models\Orders;
use App\Models\product;
use App\Models\ProductAds;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\User;
use App\Models\ViewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{   
    private $Slider;
    private $Category;
    private $Product;
    private $Setting;
    private $ProductAds;
    private $Investors;
    private $Contact;
    private $User;
    private $Order;
    private $Detail;
    private $Views;
    public function __construct(ViewModel $viewModel , DetailOd $detailOd, Orders $orders,User $user,Contact $contact,Slider $slider, Category $category, product $product, Setting $setting, ProductAds $productAds,Investors $investors)
    {
        $this->Views = $viewModel;
        $this->Detail = $detailOd;
        $this->Order = $orders;
        $this->User = $user;
        $this->Slider = $slider;
        $this->Category = $category;
        $this->Product = $product;
        $this->Setting = $setting;
        $this->ProductAds = $productAds;
        $this->Investors = $investors;
        $this->Contact = $contact;
    }
    public function home(){
        $Slider = $this->Slider->latest()->get();
        $Category = $this->Category->where('parent_id',0)->get();
        
        $Views = $this->Views->get();
        $Featured_Product = $this->Product->orderby('promotion','desc')->paginate(4);
        $Latest_Product = $this->Views->orderby('views','desc')->paginate(12);

        $Setting = $this->Setting->latest()->get();
        $ProductAds = $this->ProductAds->latest()->get();
        $Logo = $this->Investors->latest()->get();
        $Contact = $this->Contact->get();
        return view('Frontend.home', compact('Views','Contact','Slider','Category','Featured_Product','Latest_Product','Setting','ProductAds','Logo'));
    }
    public function Page($id){
        $Contact = $this->Contact->get();
        $Setting = $this->Setting->latest()->get();
        $Content = $this->Contact->find($id);
        return view('Frontend.page', compact('Setting','Content','Contact'));
    }
    public function history(){
        if(Auth::check() != false){
            $id = Auth::id();
            $User = $this->User->find($id);
            $email = $User['email'];
            return redirect('return-success/'.$email);
        }else{
        $Slider = $this->Slider->latest()->get();
        $Setting = $this->Setting->latest()->get();
        $Contact = $this->Contact->get();
        $message = null;
        return view('Frontend.history', compact('message','Slider','Setting','Contact'));
        }
    }
    public function check_Email(Request $request){

        $check = $this->Order->where('email', $request->email)->count();
        if($check != 0 && $request != null){
            $emailclient = $request->email;
            $name = $emailclient;
            $code_check = Str::Random(6);
            Mail::send('Frontend.emails.sendmail_check_history_order', compact('name','code_check'), function($email) use($name,$emailclient){
                $email->subject('[PHONE-STORE] Mã xác nhận !');
                $email->to( $emailclient , $name);
            });
            $code = md5($code_check);
            return redirect('return-check/'.$emailclient.'/'.$code); 
        }else{
            $Slider = $this->Slider->latest()->get();
            $Setting = $this->Setting->latest()->get();
            $Contact = $this->Contact->get();
            $message = "Email này chưa mua sản phẩm nào";
            return view('Frontend.history', compact('message','Slider','Setting','Contact'));
        }
    }
    public function return_check($email, $code){
        $emailclient = $email;
        $code_check = $code;
        $Slider = $this->Slider->latest()->get();
        $Setting = $this->Setting->latest()->get();
        $Contact = $this->Contact->get();
        $message = null;
        return view('Frontend.emails.check_code_mail', compact('message','Slider','Setting','Contact','emailclient','code_check'));
    }
    public function success_check($email,$code, Request $request){
        $check = $this->Order->where('email', $email)->count();
        if($check != 0 && $code == md5($request->code) ){
            $Order = $this->Order->orderBy('id','desc')->where('email', $email)->paginate(5);
            $Detail = $this->Detail->get();
            $Product = $this->Product->get();
            $Contact = $this->Contact->get();
            $Setting = $this->Setting->latest()->get();
            $Slider = $this->Slider->latest()->get();
            $i = 0;
            return view('Frontend.history_return',compact('email','i','Order','Detail','Product','Contact','Setting','Slider'));
        }else{
            $code_check = $code;
            $emailclient = $email;
            $Slider = $this->Slider->latest()->get();
            $Setting = $this->Setting->latest()->get();
            $Contact = $this->Contact->get();
            $message = "Mã code không đúng, vui lòng nhập lại";
            return view('Frontend.emails.check_code_mail', compact('message','Slider','Setting','Contact','emailclient','code_check'));
        }
    }
    public function success($email){
        if(Auth::check()){
            $Order = $this->Order->orderBy('id','desc')->where('email', $email)->paginate(5);
            $Detail = $this->Detail->get();
            $Product = $this->Product->get();
            $Contact = $this->Contact->get();
            $Setting = $this->Setting->latest()->get();
            $Slider = $this->Slider->latest()->get();
            $i = 0;
            return view('Frontend.history_return',compact('email','i','Order','Detail','Product','Contact','Setting','Slider'));
        }else{
            return redirect('history');
        }
    }
}
