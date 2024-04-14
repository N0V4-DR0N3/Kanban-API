<?php

namespace App\Models;

use App\Models\Concerns\MockableChanges;
use App\Models\Concerns\MockableSave;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * @mixin MockableChanges
 * @mixin MockableSave
 */
abstract class Model extends BaseModel
{
    use MockableChanges;
    use MockableSave;
}
