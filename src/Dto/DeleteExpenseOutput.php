<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class DeleteExpenseOutput
{
    #[Groups(['expense:read'])]
    public string $message;

    #[Groups(['expense:read'])]
    public ?int $expenseId = null;

    public function __construct(string $message = '', ?int $expenseId = null)
    {
        $this->message = $message;
        $this->expenseId = $expenseId;
    }
}
