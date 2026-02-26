<?php

namespace App\Dto;

class RegisterResponse
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $role,
        public string $message = 'Registration successful'
    ) {}
}
