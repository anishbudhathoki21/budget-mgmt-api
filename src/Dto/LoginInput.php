<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LoginInput
{
  #[Assert\NotBlank(message: 'Email is required')]
  #[Assert\Email(message: 'Invalid email address')]
  public string $email;

  #[Assert\NotBlank(message: 'Password is required')]
  public string $password;
}
