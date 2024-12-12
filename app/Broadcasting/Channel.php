<?php

namespace App\Broadcasting;

use Illuminate\Broadcasting\Channel as BaseChannel;

abstract class Channel extends BaseChannel
{
    abstract public static function route(): string;
}
