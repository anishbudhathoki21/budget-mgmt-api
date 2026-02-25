<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;

class RegisterProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // This provider is only used for output transformation
        // The actual user comes from the processor
        if (isset($context['previous_data']) && $context['previous_data'] instanceof User) {
            $user = $context['previous_data'];
            return [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'role' => [
                    'id' => $user->getRole()?->getId(),
                    'name' => $user->getRole()?->getName()
                ],
                'message' => 'Registration successful'
            ];
        }

        return null;
    }
}
