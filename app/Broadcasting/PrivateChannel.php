<?php

namespace App\Broadcasting;

use Illuminate\Broadcasting\PrivateChannel as BaseChannel;

abstract class PrivateChannel extends BaseChannel
{
    abstract public static function route(): string;
}
