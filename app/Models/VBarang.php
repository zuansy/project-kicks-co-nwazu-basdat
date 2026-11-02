<?php

namespace App\Models;

use App\Models\Concerns\ReadOnlyModel;
use Illuminate\Database\Eloquent\Model;

class VBarang extends Model
{
    use ReadOnlyModel;

    protected $table = 'v_barang';
    protected $primaryKey = 'idbarang';
}
