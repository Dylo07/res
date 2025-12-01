<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettycashTrans extends Model
{
    use HasFactory;

    protected $fillable = ['TypeOfTrans', 'Employee', 'Description', 'Amount', 'trans_date'];

    public function TypeOfTrans()
{
    return str_replace("_"," ",$this->TypeOfTrans);
}
}
