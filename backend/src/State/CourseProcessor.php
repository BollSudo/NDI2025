<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CourseProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
        private EntityManagerInterface $em
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!empty($data->getUserCredential())) {
            $user = $this->em->getRepository(User::class)
                ->findOneBy(['email' => $data->userCredential]);

            if (!$user) {
                throw new \InvalidArgumentException('User not found.');
            }

            $data->setUser($user);
            $data->eraseCredentials();
        } else {
            throw new \InvalidArgumentException('User credentials not found.');
        }
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}