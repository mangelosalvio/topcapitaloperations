<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    protected $fillable = [
      'account_type_desc',
      'account_type_code'
    ];

    public function accounts(){
        return $this->hasMany(ChartOfAccount::class);
    }
}
