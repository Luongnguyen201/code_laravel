<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Log;
use Exception;
class AdminUserController extends Controller
{
    private $User;
    private $Role;
    public function __construct(User $user, Role $role)
    {
        $this->User = $user;
        $this->Role = $role;
    }
    public function index(){
        $users = $this->User->paginate(10);
        return view('admin.user.index', compact('users'));
    }
    public function create(){
        $roles = $this->Role->all();
        return view('admin.user.add',compact('roles'));
    }
    public function store(UserRequest $request){
        try{
            DB::beginTransaction();
            $User = $this->User->create([
                'name'=> $request->name,
                'email'=> $request->email,
                'password'=> Hash::make($request->password)
            ]);
            $User->roles()->attach($request->role_id);
            DB::commit();
            return redirect('admin/users/index');
        }catch(Exception $exception){
            DB::rollBack();
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        }
    }
    public function edit($id){
        $User = $this->User->find($id);
        $roles = $this->Role->all();
        $roles_user = $User->roles;
        return view('admin.user.edit', compact('User','roles','roles_user'));
    }
    public function update(Request $request, $id){
        try{
            DB::beginTransaction();
            $this->User->find($id)->update([
                'name'=> $request->name,
                'email'=> $request->email,
                // 'password'=> Hash::make($request->password)
            ]);
            $user = $this->User->find($id);
            $user->roles()->sync($request->role_id);
            DB::commit();
            return redirect('admin/users/index');
        }catch(Exception $exception){
            DB::rollBack();
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        }
    }
    
    public function delete($id){
        try{
            $this->User->find($id)->delete();
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
