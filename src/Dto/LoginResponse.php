<?php

namespace App\Dto;

class LoginResponse
{
    public function __construct(
        public string $token,
        public int $userId,
        public string $email,
        public string $name,
        public string $role,
        public int $expiresIn = 3600
    ) {}
}
