<?php

namespace App\Models\Passport;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Passport\PersonalAccessClient as BasePersonalAccessClient;

class PersonalAccessClient extends BasePersonalAccessClient
{
    use HasUuids;
}
