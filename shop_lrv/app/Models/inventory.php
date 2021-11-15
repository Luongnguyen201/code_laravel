<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventory extends Model
{
    use HasFactory;
    protected $table= 'inventory';
    protected $guarded = [];
    public function get_product(){
        return $this->belongsTo(product::class, 'id_product');
    }
}
