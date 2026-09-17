<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Model;

class ContactCsvData extends Model {
protected $table = 'qr_contact_csv_data';


     protected $fillable = ['csv_filename', 'csv_header', 'csv_data'];
}
