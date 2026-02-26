<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ApproveExpenseInput
{
    #[Assert\NotBlank(message: 'Decision is required')]
    #[Assert\Choice(choices: ['approved', 'rejected'], message: 'Decision must be approved or rejected')]
    public string $decision = '';

    #[Assert\Length(max: 500, maxMessage: 'Comment must not exceed {{ limit }} characters')]
    public ?string $comment = null;
}
