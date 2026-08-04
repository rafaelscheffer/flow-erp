<?php

declare(strict_types=1);

namespace App\Enums;

enum AuditEventType: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case LoggedIn = 'logged_in';
    case LoggedOut = 'logged_out';
    case LoginFailed = 'login_failed';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Criado',
            self::Updated => 'Atualizado',
            self::Deleted => 'Removido',
            self::LoggedIn => 'Login realizado',
            self::LoggedOut => 'Logout realizado',
            self::LoginFailed => 'Tentativa de login falhou',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Created, self::LoggedIn => 'success',
            self::Updated => 'warning',
            self::Deleted, self::LoginFailed => 'danger',
            self::LoggedOut => 'gray',
        };
    }
}
