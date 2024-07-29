<?php
namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use App\Repositories\InvoiceRepository;

class EloquentInvoiceRepository implements InvoiceRepository
{
    public function add(array $data)
    {
        return DB::transaction(function () use ($data) {
            Invoice::insert($data);
        });
    }
}
