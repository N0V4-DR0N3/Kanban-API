<?php

namespace App\Services\Facades;

use App\Data\Log\InsertData;
use App\Enums\Log\LogAction;
use App\Enums\Task\TaskStatus;
use App\Models\Log;
use App\Models\Task;
use App\Models\User;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

/**
 * @final
 * @codeCoverageIgnore
 */
class LogFacadeService
{
    protected ?User $forcedUser = null;

    public function __construct(
        protected LogService $service
    ) {
    }

    public function asUser(User $user, callable $callback): mixed
    {
        $this->forcedUser = $user;
        $value = $callback($this);
        $this->forcedUser = null;

        return $value;
    }

    /**
     * @param LogAction $action
     * @param string $description
     * @param array<string, mixed> $payload
     *
     * @return Log
     */
    protected function log(LogAction $action, string $description, array $payload = []): Log
    {
        return $this->service->insert(new InsertData(
            user: $this->forcedUser ?? Auth::user(),

            action: $action,
            description: $description,
            payload: $payload,

            ip: request()->ip(),
        ));
    }

    #region [Auth]

    public function auth_login(User $user): Log
    {
        $description = 'Login';

        return $this->asUser(
            user: $user,
            callback: fn () => $this->log(LogAction::Auth_Login, $description),
        );
    }

    public function auth_loginFailed(User $user, string $reason): Log
    {
        $description = "Tentativa falha de login: `{$reason}`";

        return $this->asUser(
            user: $user,
            callback: fn () => $this->log(LogAction::Auth_LoginFailed, $description),
        );
    }

    public function auth_logout(User $user): Log
    {
        $description = 'Logout';

        return $this->asUser(
            user: $user,
            callback: fn () => $this->log(LogAction::Auth_Logout, $description),
        );
    }

    public function auth_passwordForgotten(User $user): Log
    {
        $description = 'Solicitou a recuperação de senha';

        return $this->asUser(
            user: $user,
            callback: fn () => $this->log(LogAction::Auth_PasswordForgotten, $description),
        );
    }

    public function auth_passwordReset(User $user): Log
    {
        $description = 'Redefiniu a senha';

        return $this->asUser(
            user: $user,
            callback: fn () => $this->log(LogAction::Auth_PasswordReset, $description),
        );
    }

    #endregion

    #region [Task]
    public function task_created(Task $task): Log
    {
        $description = "Criou a tarefa **{$task->title}**";

        return $this->log(LogAction::Task_Created, $description, [
            'task' => $task->toArray(),
        ]);
    }

    public function task_updated(Task $task, array $data): Log
    {
        $description = "Atualizou a tarefa **{$task->title}**";

        return $this->log(LogAction::Task_Updated, $description, [
            'task' => $task->toArray(),
            'data' => $data,
        ]);
    }

    public function task_deleted(Task $task): Log
    {
        $description = "Deletou a tarefa **{$task->title}**";

        return $this->log(LogAction::Task_Deleted, $description, [
            'task' => $task->toArray(),
        ]);
    }

    public function task_descriptionUpdated(Task $task, ?string $old, ?string $new): Log
    {
        $description = match (true) {
            $old && !$new => "Removeu a descrição da tarefa **{$task->title}**",
            !$old && $new => "Adicionou uma descrição à tarefa **{$task->title}**",
            default => "Atualizou a descrição da tarefa **{$task->title}**",
        };

        return $this->log(LogAction::Task_DescriptionUpdated, $description, [
            'task' => $task->toArray(),
            'old' => $old,
            'new' => $new,
        ]);
    }

    public function task_responsiblesUpdated(Task $task, array $old, array $new): Log
    {
        $description = match (true) {
            $old && !$new => "Removeu os responsáveis da tarefa **{$task->title}**",
            !$old && $new => "Adicionou responsáveis à tarefa **{$task->title}**",
            default => "Atualizou os responsáveis da tarefa **{$task->title}**",
        };

        return $this->log(LogAction::Task_ResponsibleUpdated, $description, [
            'task' => $task->toArray(),
            'old' => $old,
            'new' => $new,
        ]);
    }

    public function task_statusUpdated(Task $task, TaskStatus $old, TaskStatus $new): Log
    {
        $description = "Atualizou o status da tarefa **{$task->title}** de **{$old->label()}** para **{$new->label()}**";

        return $this->log(LogAction::Task_StatusUpdated, $description, [
            'task' => $task->toArray(),
            'old' => $old,
            'new' => $new,
        ]);
    }

    public function task_titleUpdated(Task $task, string $old, string $new): Log
    {
        $description = "Atualizou o título da tarefa de **{$old}** para **{$new}**";

        return $this->log(LogAction::Task_TitleUpdated, $description, [
            'task' => $task->toArray(),
            'old' => $old,
            'new' => $task->title,
        ]);
    }

    #endregion
}
