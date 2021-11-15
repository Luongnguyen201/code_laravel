<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
class AdminRoleController extends Controller
{
    private $Role;
    private $Permission;
    public function __construct(Role $role, Permission $permission)
    {
        $this->Role = $role;
        $this->Permission = $permission;
    }
    public function index(){
        $role = $this->Role->paginate(10);
        return view('admin.role.index',compact('role'));
    }
    public function create(){
        $role_parent = $this->Permission->where('parent_id',0)->get();
        return view('admin.role.add', compact('role_parent'));
    }
    public function store(Request $request){
        $role = $this->Role->create([
            'name' => $request->name,
            'display_name' => $request->display_name,
        ]);
        $role->permissions()->attach($request->permission_id);
        return redirect('admin/roles/index');
    }
    public function edit($id){
        $role_parent = $this->Permission->where('parent_id',0)->get();
        $role = $this->Role->find($id);
        $permissionscheck = $role->permissions;
        return view('admin.role.edit',compact('role_parent','role','permissionscheck'));
    }
    public function update(Request $request,$id){
        $role = $this->Role->find($id);
        $this->Role->find($id)->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
        ]);
        $role->permissions()->sync($request->permission_id);
        return redirect('admin/roles/index');
    }
    public function delete($id){
        try{
            $this->Role->find($id)->delete();
            return response()->json([
                'code' => 200,
                'message' => 'success',
            ],200);
        }catch(Exception $exception){
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
            return response()->json([
                'code' => 500,
                'message' => 'fail'
            ],500);
        }
    }
    
}
