<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextMessageDumb extends Model
{
    use HasFactory;

    protected $table = 'text_message_dumb';
    protected $primarykey = 'id';
    protected $gaurded = [];

}
