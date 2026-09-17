<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    public function expensecat() {
        return $this->belongsTo(Expensecategory::class);
    }

    public function  expensefor() {
        return $this->belongsTo(Expensefor::class);
    }

    public function admin(){
        return $this->belongsTo(Admin::class);
    }


    public function scopeFilterExpenseFor($query, $expensefor_id) {
        return $query->when($expensefor_id, function ($query, $expensefor_id) {
            $query->whereIn('expensefor_id', (array) $expensefor_id);
        });
    }

    public function scopeFilterExpenseCat($query, $expensecat_id) {
        return $query->when($expensecat_id, function ($query, $expensecat_id) {
            $query->whereIn('expensecat_id', (array) $expensecat_id);
        });
    }

    public function scopeFilterPaymentFor($query, $payment_mode) {
        return $query->when($payment_mode, function ($query, $payment_mode) {
            $query->whereIn('payment_mode', (array) $payment_mode);
        });
    }

    public function scopeFilterCreateBy($query, $admin_id) {
        return $query->when($admin_id, function ($query, $admin_id) {
            $query->whereIn('admin_id', (array) $admin_id);
        });
    }

    public function scopeFilterDateRange($query, $column, $date_range)
    {
        return $query->when($date_range, function ($query) use ($column, $date_range) {
            [$start, $end] = array_map('trim', explode('-', $date_range));
            $query->whereBetween($column, [date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end))]);
        });
    }

    public function scopeFilterSearchText($query, $searchText){
        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
            $query->where(function ($q) use ($searchText) {
                $q->orWhere('id','like',$searchText)
                ->orWhere('name','like',$searchText)
                ->orWhere('paid_to','like',$searchText)
                ->orWhere('amount','like',$searchText)
                ->orWhere('expense_date','like',$searchText)
                ->orWhere('payment_mode','like',$searchText)
                ->orWhere('payment_from','like',$searchText)
                ->orWhere('tax','like',$searchText)
                ->orWhereHas('expensecat', fn($q) => $q->where('name','like',$searchText))
                ->orWhereHas('expensefor', fn($q) => $q->where('name','like',$searchText))
                ->orWhereHas('admin', fn($q) => $q->where('name','like',$searchText));
            });
        });
    }

}
