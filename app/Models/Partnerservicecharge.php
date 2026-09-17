<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partnerservicecharge extends Model
{
    use HasFactory;

    /**
     * Get the givenby that owns the Partnerservicecharge
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function givenby()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the profession that owns the Partnerservicecharge
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    /**
     * Get the createby that owns the Partnerservicecharge
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createby()
    {
        return $this->belongsTo(Admin::class);
    }


}
