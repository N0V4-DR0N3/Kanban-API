<?php

namespace App\Enums\Concerns;

use Illuminate\Support\Str;

trait AsPerm
{
    public function name(): string
    {
        return $this->name;
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return array{string, string}
     * @phpstan-return array{string, literal-string}
     */
    protected function parts(): array
    {
        return explode('.', $this->value);
    }

    /**
     * @return string
     * @phpstan-return literal-string
     */
    final public function getDomain(): string
    {
        return $this->parts()[0];
    }

    abstract public function getDomainTitle(): string;

    /**
     * @return string
     * @phpstan-return literal-string
     */
    final public function getAction(): string
    {
        return $this->parts()[1];
    }

    public function getActionTitle(): string
    {
        return match ($action = $this->getAction()) {
            'view' => 'Visualizar',
            'create' => 'Criar',
            'update' => 'Editar',
            'delete' => 'Excluir',

            'cancel' => 'Cancelar',
            'destroy' => 'Destruir',
            'manage' => 'Gerenciar',
            'remove' => 'Remover',
            'review' => 'Revisar',
            'upload' => 'Adicionar',
            'upsert' => 'Criar e editar',
            'validate' => 'Validar',

            default => "[{$action}]",
        };
    }

    public function getTitle(): string
    {
        $action = $this->getActionTitle();
        $domain = $this->getDomainTitle();

        return implode(' ', [$action, Str::lower($domain)]);
    }
}
