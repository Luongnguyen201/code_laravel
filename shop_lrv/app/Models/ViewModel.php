<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewModel extends Model
{
    use HasFactory;
    protected $table = 'views';
    protected $guarded = [];
    public function get_product_views(){
        return $this->belongsTo(product::class, 'id_product');
    }
}
