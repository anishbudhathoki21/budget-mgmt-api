<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateExpenseInput
{
    #[Assert\Choice(choices: ['cancelled'], message: 'Invalid status')]
    public ?string $status = null;
}
