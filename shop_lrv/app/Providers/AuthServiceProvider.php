<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\product;
use App\Services\PermissionGatePolicyCheckAccess;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    // private $Product;
    // public function __construct(product $product)
    // {
    //     $this->Product = $product;
    // }
    
    public function boot()
    {
        $this->registerPolicies();
        $PermissionGateAndPolicy = New PermissionGatePolicyCheckAccess();
        $PermissionGateAndPolicy->setGateAndPolicyAccess();
    }
}
