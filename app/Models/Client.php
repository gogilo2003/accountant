<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'ids', 'client_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
