<?php

namespace App\Enums\Log;

use App\Enums\Concerns\Coerceable;

enum LogAction: string
{
    use Coerceable;

    #region [Auth]
    case Auth_Login = 'auth.login';
    case Auth_LoginFailed = 'auth.login_failed';
    case Auth_Logout = 'auth.logout';
    case Auth_PasswordForgotten = 'auth.password_forgotten';
    case Auth_PasswordReset = 'auth.password_reset';
    #endregion

    #region [User]
    case User_Activated = 'user.activated';
    case User_Created = 'user.created';
    case User_Deleted = 'user.deleted';
    case User_EmailUpdated = 'user.email_updated';
    case User_Inactivated = 'user.inactivated';
    case User_NameUpdated = 'user.name_updated';
    case User_PasswordUpdated = 'user.password_updated';
    case User_Update = 'user.updated';
    #endregion

    #region [Task]
    case Task_Created = 'task.created';
    case Task_Deleted = 'task.deleted';
    case Task_DescriptionUpdated = 'task.description_updated';
    case Task_ResponsibleUpdated = 'task.responsible_updated';
    case Task_TitleUpdated = 'task.title_updated';
    case Task_StatusUpdated = 'task.status_updated';
    case Task_Updated = 'task.updated';
    #endregion
}
