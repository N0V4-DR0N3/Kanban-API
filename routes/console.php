<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:prune-batches --cancelled=72 --unfinished=72')->daily();
