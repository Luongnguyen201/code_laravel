<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginCheckRequest;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
class AdminController extends Controller
{
    private $Setting;
    private $Slider;
    private $User;
    private $Contact;
    public function __construct(Contact $contact,Setting $setting, Slider $slider, User $user)
    {
        $this->Setting = $setting;
        $this->Slider = $slider;
        $this->User = $user;
        $this->Contact = $contact;
    }
    public function login(){
        if( Auth::check() ){
            return redirect('admin/Orders/index');
        }else{
            $check = null;
            $Slider = $this->Slider->latest()->get();
            $Setting = $this->Setting->latest()->get();
            $Contact = $this->Contact->get();
            return view('login_admin',compact('Setting','Slider','check','Contact'));
        }
    }
    public function postLoginAdmin(LoginCheckRequest $request){
        $remember= $request->has('remember') ? true : false;
        if(Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ],$remember)){
            return redirect('admin/Orders/index');
        }else{
            $check = 'Tài khoản hoặc mật khẩu không chính xác !';
            $Slider = $this->Slider->latest()->get();
            $Setting = $this->Setting->latest()->get();
            $Contact = $this->Contact->get();
            return view('login_admin',compact('Setting','Slider','check','Contact'));
        }
    }

    public function LoginClient(){
        $check = '';
        $Contact = $this->Contact->get();
        $Slider = $this->Slider->latest()->get();
        $Setting = $this->Setting->latest()->get();
        return view('Login_client',compact('Setting','Slider','check','Contact'));
    }
    public function LoginTo(LoginCheckRequest $request, $remember=true){
        if(Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ],$remember)){
            return redirect('/');
        }
        else{
            $check = 'Tài khoản hoặc mật khẩu không chính xác !';
            $Slider = $this->Slider->latest()->get();
            $Setting = $this->Setting->latest()->get();
            $Contact = $this->Contact->get();
            return view('Login_client',compact('Setting','Slider','check','Contact'));
        }
    }
    public function LogoutClient(){
        Auth::logout();
        return redirect('Login');
    }
    public function SignUpClient(LoginRequest $request){
        $userId = $this->User->where('email', $request->email)->count();
        if($userId == 0){ 
            $CreateUser = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];
            $this->User->create($CreateUser);
            $name =  $CreateUser['name'];
            $EmailClient = $CreateUser['email'];
            $password = $request->password;
            $vnp_bank = $request->vnp_BankCode;
            Mail::send('Frontend.emails.sendmailSingUp', compact('name','password'), function($email) use($name,$EmailClient){
                $email->subject('[PHONE-STORE] Đăng ký tài khoản thành công');
                $email->to( $EmailClient , $name);
            });
            if(Auth::attempt([
                'email' => $request->email,
                'password' => $request->password,
            ],$remember = true)){
                return redirect('/');
            }
        }else{
            $check = "Email này đã tồn tại !";
            $Slider = $this->Slider->latest()->get();
            $Setting = $this->Setting->latest()->get();
            $Contact = $this->Contact->get();
            return view('Login_client',compact('Setting','Slider','check','Contact'));
        }
    }

}
