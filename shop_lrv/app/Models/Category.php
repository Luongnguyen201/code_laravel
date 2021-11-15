<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $guarded = [];
    public function categoryChildren(){
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function Products(){
        return $this->hasMany(product::class,'category');
    }
}
