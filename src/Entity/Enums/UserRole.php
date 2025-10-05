<?php

namespace App\Entity\Enums;

enum UserRole: string {
    case ADMIN = 'admin';
    case TRAINER = 'trainer'; // Kako bi imalo vise smisla nek ovo bude "menadzer" role
    case USER = 'user';
}