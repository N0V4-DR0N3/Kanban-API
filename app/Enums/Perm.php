<?php

namespace App\Enums;

use App\Enums\Concerns\AsPerm;
use App\Enums\Concerns\Coerceable;
use App\Enums\Contracts\Perm as PermContract;
use Illuminate\Support\Str;

enum Perm: string implements PermContract
{
    use AsPerm;
    use Coerceable;

    /*
     * This specific enum uses Pascal cases for better readability, since it
     * needs to map different domains of permissions.
     */
    case Logs_View = 'logs.view';

    case Roles_View = 'roles.view';
    case Roles_Create = 'roles.create';
    case Roles_Update = 'roles.update';
    case Roles_Delete = 'roles.delete';

    case Users_View = 'users.view';
    case Users_Create = 'users.create';
    case Users_Update = 'users.update';
    case Users_Delete = 'users.delete';

    public function getDomainTitle(): string
    {
        return match ($domain = $this->getDomain()) {
            'logs' => 'Histórico de ações',
            'roles' => 'Cargos',
            'users' => 'Usuários',

            default => '['.Str::title($domain).']',
        };
    }
}
