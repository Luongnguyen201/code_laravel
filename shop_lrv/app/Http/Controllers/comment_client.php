<?php

namespace App\Http\Controllers;

use App\Models\Comment_client as ModelsComment_client;
use App\Models\product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Mime\Part\DataPart;
use Illuminate\Support\Str;

class comment_client extends Controller
{

    private $comment;
    private $Product;
    private $User;
    public function __construct(User $user ,ModelsComment_client $comment_client, product $product)
    {
        $this->comment = $comment_client;
        $this->Product = $product;
        $this->User = $user;
    }

    public function create_comment(Request $request){
        if( Auth::check() && $request->content != null ){
            $us = $this->User->find(Auth::id());
            $create_commnet = [
                'Cm_user_id' => Auth::id(),
                'Cm_product_id' => $request->Id_pr,
                'name' => $us['name'],
                'Content' => $request->content,
        ];
            $this->comment->create($create_commnet);
        }
        $ProductId = $this->Product->find($request->Id_pr);
        $slug = Str::slug($ProductId['name']);
        return redirect('detail_product/'.$request->Id_pr.'/'.$slug.'');
    }
}
