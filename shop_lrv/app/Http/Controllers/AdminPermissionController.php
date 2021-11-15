<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class AdminPermissionController extends Controller
{
    private $Permission;
    public function __construct(Permission $permission)
    {
        $this->Permission = $permission;
    }
    public function createPermission(){
        return view('admin.permission.add');
    }
    public function store(Request $request){
       $per = $this->Permission->create([
           'name' => $request->module_parent,
           'display_name' => $request->module_parent,
           'parent_id' => 0,
           'key_code' => $request->module_parent,
       ]);
       foreach( $request->module_children as $value){
        $this->Permission->create([
            'name' => $value,
            'display_name' => $value,
            'parent_id' => $per->id,
            'key_code' => $value.'_'.$request->module_parent,
        ]);
       }
       return view('admin.permission.add');
    }
}
