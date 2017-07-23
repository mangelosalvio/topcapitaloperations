<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckVoucher extends Model
{
    protected $fillable = [
      'customer_name',
      'date',
      'amount',
      'bank_id',
      'check_no',
      'explanation'
    ];

    public function details(){
        return $this->hasMany(CheckVoucherDetail::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
