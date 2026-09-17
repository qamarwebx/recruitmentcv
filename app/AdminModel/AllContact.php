<?php

namespace App\AdminModel;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllContact extends Model
{
    protected $table = 'qr_all_contacts_tbl';

    protected $primaryKey = 'id';
    
    use HasFactory;

    public function regardings()
    {
        return $this->belongsToMany(Regarding::class,'qr_contact_regarding_tbl','cont_id','regarding_id');
    }

    public function tradesites()
    {
        return $this->belongsToMany(Tradeitecenter::class,'qr_contact_tradesitecenter_tbl','cont_id','tradesc_id');
    }

    public function lcs()
    {
        return $this->belongsTo(LifeCycleStage::class);
    }

    public function ls()
    {
        return $this->belongsTo(Lstage::class);
    }

    public function indust(){
        return $this->belongsTo(Industry::class);
    }

    public function expectct()
    {
        return $this->belongsTo(Country::class,'country_id','expected_cid');
    }

    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    
    

}
