<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public function coins(){
        return $this->hasOne(Wallet::class, 'company_id', 'id');
    }
    public function candidates(){
        return $this->belongsToMany(Candidate::class, 'company_contacts');
    }
}
