<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\RegisterResponse;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterProcessor implements ProcessorInterface
{
    public function __construct(
      private EntityManagerInterface $entityManager,
      private UserPasswordHasherInterface $passwordHasher,
      private ValidatorInterface $validator
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): RegisterResponse
    {
        $errors = $this->validator->validate($data);
        if(\count($errors) > 0) {
          $violations = [];
          foreach($errors as $error) {
            $violations[] = [
              'field' => $error->getPropertyPath(),
              'message' => $error->getMessage()
            ];
          }
          throw new BadRequestHttpException(json_encode(['errors' => $violations]));
        }

        if($this->entityManager->getRepository(User::class)->findOneBy(['email' => $data->email])) {
          throw new BadRequestHttpException(json_encode(['errors' => [['field' => 'email', 'message' => 'Email already exists']]]));
        }

        $role = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'EMPLOYEE']);
        if (!$role) {
          throw new BadRequestHttpException(json_encode(['errors' => [['field' => 'role', 'message' => 'Default role not found']]]));
        }
        $user = new User();
        $user->setName($data->name);
        $user->setEmail($data->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data->password));
        $user->setRole($role);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new RegisterResponse(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail(),
            role: $user->getRole()?->getName() ?? 'EMPLOYEE'
        );
    }
}