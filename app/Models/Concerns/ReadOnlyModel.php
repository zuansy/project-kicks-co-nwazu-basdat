<?php

namespace App\Models\Concerns;

use LogicException;

trait ReadOnlyModel
{
    public $timestamps = false;
    public $incrementing = false;

    public function save(array $options = [])
    {
        throw new LogicException(static::class.' bersifat read-only (view database).');
    }

    public function delete()
    {
        throw new LogicException(static::class.' bersifat read-only (view database).');
    }
}
