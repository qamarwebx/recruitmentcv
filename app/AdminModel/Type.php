<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class Type extends Model {
    protected $table = 'qr_type';
    protected $gaurded = [];


public function label()
    {
        return $this->hasMany(Label::class);
    }
}
