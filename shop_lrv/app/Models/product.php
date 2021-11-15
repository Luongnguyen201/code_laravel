<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class product extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $guarded = [];
    public function images(){
        return $this->hasMany(ProductImage::class, 'product_Id');// 1 - nhieu
    }
    public function tags(){
        return $this->belongsToMany(tag::class, 'product_tags','product_Id','tag_id')->withTimestamps(); //nhieu-nhieu
    }
    public function catagory(){
        return $this->belongsTo(Category::class, 'category');//1-1
    }
    public function product_img(){
        return $this->hasMany(ProductImage::class,'product_Id');
    }
    public function product_color(){
        return $this->belongsToMany(Color::class,'product_color','product_id','color_id')->withTimestamps();;
    }   
    public function scopeSearch($query){
        if(request('key')){
            $key = request('key');
            $query = $query->where('name','like','%'.$key.'%');
        }
        if(request('category_id')){
            $query = $query->where('category',request('category_id'));
        }
        return $query;
    }
}
//product voi product_image la moi quan he 1 - nhieu
