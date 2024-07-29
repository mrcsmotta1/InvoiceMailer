<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome_fantasia',
        'email',
        'cnpj',
        'codigo_barra',
        'valor',
        'data_vencimento',
        'juros'
    ];
}
