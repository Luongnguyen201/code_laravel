<?php

namespace App\Http\Controllers;

use App\Components\MenuRecusive;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Manus;
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class AdminContact extends Controller
{
    private $Contact;
    private $Menu;
    private $MenuRecusive;
    public function __construct(Contact $contact, Manus $menu, MenuRecusive $menuRecusive)
    {
        $this->Contact = $contact;
        $this->Menu = $menu;
        $this->MenuRecusive  = $menuRecusive;
    }
    public function index(){
        $ContactAll = $this->Contact->latest()->paginate(5);
        if($ContactAll != null){
            return view('admin.contact.index', compact('ContactAll'));
        }
    }
    public function create(){
        $Menu = $this->Menu->get();
        $MenuRecusive = $this->MenuRecusive->ManuRecusiveSelect($Menu->parent_id=0);
        return view('admin.contact.add', compact('MenuRecusive'));
    }
    public function store(ContactRequest $request){
        try{
            DB::beginTransaction();
            $insertData = [
                'name' => $request->name,
                'content' => $request->content,
                'id_menu' => $request->id_menu,
            ];
            $this->Contact->Create($insertData);
            DB::commit();
            return redirect('admin/Contact/index');
        }catch(Exception $exception){
            DB::rollBack();
            Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        }
    }
    public function edit($id){
        $message = "NOTE: Một page chỉ có một menu duy nhất và ngược lại !";
        $Contact = $this->Contact->find($id);
        $MenuRecusive = $this->MenuRecusive->ManuRecusiveSelect( $id=$Contact['id']);
        return view('admin.Contact.edit', compact('Contact','MenuRecusive','message'));
    }
    public function update($id, ContactRequest $request){
        $Contact_id = $this->Contact->where('id_menu',$request->id_menu)->count();            
        $Contact = $this->Contact->find($id);

        // dd($Contact);
        if( $Contact_id == 0 ){
            try{
                DB::beginTransaction();
                $updateData = [
                    'name' => $request->name,
                    'content' => $request->content,
                    'id_menu' => $request->id_menu,
                ];
                $this->Contact->find($id)->update($updateData);
                DB::commit();
                return redirect('admin/Contact/index');
    
            }catch(Exception $exception){
                DB::rollBack();
                Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
            }
        }elseif( $Contact_id == 1 && $Contact['id_menu'] == $request->id_menu ){
            try{
                DB::beginTransaction();
                $updateData = [
                    'name' => $request->name,
                    'content' => $request->content,
                    'id_menu' => $request->id_menu,
                ];
                $this->Contact->find($id)->update($updateData);
                DB::commit();
                return redirect('admin/Contact/index');
    
            }catch(Exception $exception){
                DB::rollBack();
                Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
            }
        }else{
            $message = "Menu đã tồn tại page. Mời bạn chọn lại !";
            $MenuRecusive = $this->MenuRecusive->ManuRecusiveSelect( $id=$Contact['id']);
            return view('admin.Contact.edit', compact('Contact','MenuRecusive','message'));
        }
        
        
    }
    public function delete($id){
       try{
        $this->Contact->find($id)->delete();
        return response()->json([
            'code' => 200,
            'message'=>'success',
        ],200);
    }catch(Exception $exception) {
        Log::error('Message:' .$exception->getMessage() . '-----Line:' . $exception->getLine());
        return response()->json([
            'code' => 500,
            'message' => 'fail',
        ],500);
    }
    }
}
