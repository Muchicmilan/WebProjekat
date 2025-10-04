<?php

namespace App\Entity\Enums;

enum UserRole: string {
    case ADMIN = 'admin';
    case USER = 'user';
    case GUEST = 'guest';
}