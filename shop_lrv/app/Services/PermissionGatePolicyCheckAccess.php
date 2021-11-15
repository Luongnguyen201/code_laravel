<?php
    namespace App\Services;


    use Illuminate\Support\Facades\Gate;

class PermissionGatePolicyCheckAccess{
    public function setGateAndPolicyAccess(){   

        $this->defineGateCategory();
        $this->defineGateProduct();
        $this->defineGateMenu();
        $this->defineSlider();
        $this->defineSetting();
        $this->defineUser();
        $this->defineRole();
        $this->defineProductAds();
        $this->defineInvestors();
        $this->defineGateOrder();
        $this->defineGatePages();
            
    }
        public function defineGatePages(){
            Gate::define('Contact-list','App\Policies\ContactPolicy@view');
            Gate::define('Contact-create','App\Policies\ContactPolicy@create');
            Gate::define('Contact-update','App\Policies\ContactPolicy@update');
            Gate::define('Contact-delete','App\Policies\ContactPolicy@delete');
        }
        public function defineGateOrder(){
            Gate::define('order-list','App\Policies\OrderPolicy@view');
            Gate::define('detail_order-list','App\Policies\OrderPolicy@detail_order');
            Gate::define('order-update','App\Policies\OrderPolicy@update');
            Gate::define('order-delete','App\Policies\OrderPolicy@delete');
        }
        public function defineGateCategory(){
            Gate::define('category-list','App\Policies\CategoryPolicy@view');
            Gate::define('category-add','App\Policies\CategoryPolicy@create');
            Gate::define('category-edit','App\Policies\CategoryPolicy@update');
            Gate::define('category-delete','App\Policies\CategoryPolicy@delete');
        }
        public function defineGateProduct(){
            Gate::define('product-list','App\Policies\ProductPolicy@view');
            Gate::define('product-add','App\Policies\ProductPolicy@create');
            Gate::define('product-edit','App\Policies\ProductPolicy@update');
            Gate::define('product-delete','App\Policies\ProductPolicy@delete');
        }
        public function defineGateMenu(){
            Gate::define('menu-list','App\Policies\MenuPolicy@view');
            Gate::define('menu-add','App\Policies\MenuPolicy@create');
            Gate::define('menu-edit','App\Policies\MenuPolicy@update');
            Gate::define('menu-delete','App\Policies\MenuPolicy@delete');
        }
        public function defineSlider(){
            Gate::define('slider-list','App\Policies\SliderPolicy@view');
            Gate::define('slider-add','App\Policies\SliderPolicy@create');
            Gate::define('slider-edit','App\Policies\SliderPolicy@update');
            Gate::define('slider-delete','App\Policies\SliderPolicy@delete');
        }
        public function defineSetting(){
            Gate::define('setting-list','App\Policies\SettingPolicy@view');
            Gate::define('setting-add','App\Policies\SettingPolicy@create');
            Gate::define('setting-edit','App\Policies\SettingPolicy@update');
            Gate::define('setting-delete','App\Policies\SettingPolicy@delete');
        }
        public function defineUser(){
            Gate::define('user-list','App\Policies\UserPolicy@view');
            Gate::define('user-add','App\Policies\UserPolicy@create');
            Gate::define('user-edit','App\Policies\UserPolicy@update');
            Gate::define('user-delete','App\Policies\UserPolicy@delete');
        }
        public function defineRole(){
            Gate::define('role-list','App\Policies\RolePolicy@view');
            Gate::define('role-add','App\Policies\RolePolicy@create');
            Gate::define('role-edit','App\Policies\RolePolicy@update');
            Gate::define('role-delete','App\Policies\RolePolicy@delete');
        }
        public function defineProductAds(){
            Gate::define('productads-list','App\Policies\ProductAdsPolicy@view');
            Gate::define('productads-add','App\Policies\ProductAdsPolicy@create');
            Gate::define('productads-edit','App\Policies\ProductAdsPolicy@update');
            Gate::define('productads-delete','App\Policies\ProductAdsPolicy@delete');
        }
        public function defineInvestors(){
            Gate::define('investors-list','App\Policies\InvestorsPolicy@view');
            Gate::define('investors-add','App\Policies\InvestorsPolicy@create');
            Gate::define('investors-edit','App\Policies\InvestorsPolicy@update');
            Gate::define('investors-delete','App\Policies\InvestorsPolicy@delete');
        }
    }
?>