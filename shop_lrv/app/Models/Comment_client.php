<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment_client extends Model
{
    use HasFactory;
    protected $table = "comment_clients";
    protected $guarded = [];
}
