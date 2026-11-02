<?php

namespace App\Models;

use App\Models\Concerns\ReadOnlyModel;
use Illuminate\Database\Eloquent\Model;

class VSatuan extends Model
{
    use ReadOnlyModel;

    protected $table = 'v_satuan';
    protected $primaryKey = 'idsatuan';
}
