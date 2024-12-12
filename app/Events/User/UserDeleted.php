<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\SerializesModels;

#[WithoutRelations]
class UserDeleted implements ShouldDispatchAfterCommit
{
    use SerializesModels;

    public function __construct(
        public User $user
    ) {
    }
}
