<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactConversation extends Model
{
    use HasFactory;

    public function contype()
    {
        return $this->belongsTo(ContactType::class);
    }
}
