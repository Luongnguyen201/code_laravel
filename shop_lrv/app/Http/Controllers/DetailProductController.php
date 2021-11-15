<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Comment_client;
use App\Models\Contact;
use App\Models\inventory;
use App\Models\Investors;
use App\Models\product;
use App\Models\ProductAds;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\ViewModel;

class DetailProductController extends Controller
{
    private $Slider;
    private $Category;
    private $Product;
    private $Setting;
    private $ProductAds;
    private $Investors;
    private $Comment;
    private $Contact;
    private $inventory;
    private $Views;
    public function __construct(ViewModel $viewModel,inventory $inventory,Contact $contact,Comment_client $comment_client ,Slider $slider, Category $category, product $product, Setting $setting, ProductAds $productAds,Investors $investors)
    {
        $this->Views = $viewModel;
        $this->inventory = $inventory;
        $this->Contact =$contact;
        $this->Comment = $comment_client;
        $this->Slider = $slider;
        $this->Category = $category;
        $this->Product = $product;
        $this->Setting = $setting;
        $this->ProductAds = $productAds;
        $this->Investors = $investors;
    }
    public function DetailProduct($id){
        $data = $this->Views->where('id_product',$id)->first();
        if($data['views'] != null){
            $this->Views->where('id_product', $id)->update([
                'views' => $data['views'] + 1 ,
            ]);
        }else{
            $this->Views->where('id_product', $id)->update([
                'views' => 1,
            ]);
        }
        $views = $this->Views->get();
        $Inventory = $this->inventory->where('id_product', $id)->first();
        $Contact = $this->Contact->get();
        $Setting = $this->Setting->latest()->get();
        $Product = $this->Product->find($id);
        $Commentall_id =  $this->Comment->where('Cm_product_id',$id)->paginate(8);
        $ListProductAs = $this->Product->where('category',$Product->category)->paginate(4);
        return view('Frontend.DetaiProduct',compact('views','Inventory','Contact','Setting','Product','ListProductAs','Commentall_id'));
    }
}
