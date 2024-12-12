<?php

namespace App\Http\Resources\Task;

use App\Models\TaskResponsible;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TaskResponsible
 * @property-read TaskResponsible $resource
 */
class TaskUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->user->name,
            'email' => $this->user->email,
        ];
    }
}
