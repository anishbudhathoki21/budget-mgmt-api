<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterInput
{
  #[Assert\NotBlank(message: 'Name is required')]
  #[Assert\Length( max: 100,  maxMessage: 'Name cannot exceed {{ limit }} characters')]
  public string $name;

  #[Assert\NotBlank(message: 'Email is required')]
  #[Assert\Email(message: 'Invalid email address')]
  public string $email;

  #[Assert\NotBlank(message: 'Password is required')]
  #[Assert\Length(min: 8, minMessage: 'Password must be at least {{ limit }} characters')]
  public string $password;
}