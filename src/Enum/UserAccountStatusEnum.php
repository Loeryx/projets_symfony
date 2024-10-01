<?php
declare(strict_types=1);

namespace App\Enum;

enum UserAccountStatusEnum : string {
    case ACTIVE = "active";
    case PENDING = "pending";
    case BLOCKED = "blocked";
    case DELETED = "deleted";
    case BANNED = "banned";
}