<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Budget;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateBudgetProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Budget
    {
        $budget = $context['previous_data'] ?? null;

        if (!$budget instanceof Budget) {
            throw new BadRequestHttpException('Budget not found');
        }

        if (!$this->security->isGranted('BUDGET_EDIT', $budget)) {
            throw new AccessDeniedHttpException('You do not have permission to edit this budget');
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

        if ($data->name !== null) {
            $budget->setName($data->name);
        }

        if ($data->totalAmount !== null) {
            $budget->setTotalAmount((string) $data->totalAmount);
        }

        if ($data->startDate !== null) {
            $startDate = \DateTime::createFromFormat('Y-m-d', $data->startDate);
            $budget->setStartDate($startDate);
        }

        if ($data->endDate !== null) {
            $endDate = \DateTime::createFromFormat('Y-m-d', $data->endDate);
            $budget->setEndDate($endDate);
        }

        if ($data->currency !== null) {
            $budget->setCurrency($data->currency);
        }

        if ($data->status !== null) {
            $budget->setStatus($data->status);
        }

        $budget->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $budget;
    }
}
