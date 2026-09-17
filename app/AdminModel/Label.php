<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class Label extends Model {
    protected $table = 'qr_label';
    protected $gaurded = [];

      public function type()
    {
        return $this->belongsTo(Type::class);
    }

}
