<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Investors;
use App\Models\product;
use App\Models\ProductAds;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\ViewModel;
use Illuminate\Support\Facades\DB;
class ListProductController extends Controller
{
    private $Slider;
    private $Category;
    private $Product;
    private $Setting;
    private $ProductAds;
    private $Investors;
    private $Contact;
    private $Views;

    public function __construct( ViewModel $viewModel,Contact $contact,Slider $slider, Category $category, product $product, Setting $setting, ProductAds $productAds,Investors $investors)
    {
        $this->Views = $viewModel;
        $this->Slider = $slider;
        $this->Category = $category;
        $this->Product = $product;
        $this->Setting = $setting;
        $this->ProductAds = $productAds;
        $this->Investors = $investors;
        $this->Contact = $contact;
    }
    public function index(){
        $Contact = $this->Contact->get();
        $Setting = $this->Setting->latest()->get();
        $Product = $this->Views->orderBy('views','desc')->paginate(12);
        $Category = $this->Category->get();
        return view('Frontend.List_product', compact('Contact','Setting','Product','Category'));
    }
    public function List_product_category($id){
        $views = $this->Views->get();
        $Setting = $this->Setting->latest()->get();
        $Product = $this->Product->latest()->where('category',$id)->paginate(12);
        $Category = $this->Category->get();
        $Contact = $this->Contact->get();
        return view('Frontend.List_product_category', compact('Contact','Setting','Product','Category','views'));
    }
    public function search_ajax(){
        $data = $this->Product->search()->paginate(10);
        return view('component.search_product_at_header', compact('data'));
    }
}
