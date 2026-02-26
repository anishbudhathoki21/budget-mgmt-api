<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Budget;
use App\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateBudgetProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Budget
    {
        $user = $this->security->getUser();

        if (!$this->security->isGranted('BUDGET_CREATE')) {
            throw new AccessDeniedHttpException('Only managers can create budgets');
        }

        $errors = $this->validator->validate($data);
        if (\count($errors) > 0) {
            $violations = [];
            foreach ($errors as $error) {
                $violations[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }
            throw new BadRequestHttpException(json_encode(['errors' => $violations]));
        }

        $department = $this->entityManager->getRepository(Department::class)->find($data->departmentId);
        if (!$department) {
            throw new BadRequestHttpException(json_encode(['errors' => [['field' => 'departmentId', 'message' => 'Department not found']]]));
        }

        $startDate = \DateTime::createFromFormat('Y-m-d', $data->startDate);
        $endDate = \DateTime::createFromFormat('Y-m-d', $data->endDate);

        if ($startDate >= $endDate) {
            throw new BadRequestHttpException(json_encode(['errors' => [['field' => 'endDate', 'message' => 'End date must be after start date']]]));
        }

        $budget = new Budget();
        $budget->setName($data->name);
        $budget->setTotalAmount((string) $data->totalAmount);
        $budget->setStartDate($startDate);
        $budget->setEndDate($endDate);
        $budget->setCurrency($data->currency);
        $budget->setDepartment($department);
        $budget->setCreatedBy($user);

        $this->entityManager->persist($budget);
        $this->entityManager->flush();

        return $budget;
    }
}
