<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws \Exception
     */
    public function register(array $data) {
        if (empty($data['email'])) {
            throw new \InvalidArgumentException("L'email ne peut pas être vide.");
        }

        if (empty($data['password'])) {
            throw new \InvalidArgumentException("Le mot de passe ne peut pas être vide.");
        }

        if (empty($data['name'])) {
            throw new \InvalidArgumentException("Le nom ne peut pas être vide.");
        }

        if (empty($data['firstName'])) {
            throw new \InvalidArgumentException("Le prénom ne peut pas être vide.");
        }

        if ($this->userRepository->findByEmail($data['email'])) {
            throw new \InvalidArgumentException("Cet email est déjà utilisé.");
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
        $user->setName($data['name']);
        $user->setFirstName($data['firstName']);

        if (!empty($data['phoneNumber'])) {
            $user->setPhoneNumber($data['phoneNumber']);
        }

        if (!empty($data['birthdate'])) {
            try {
                $user->setBirthdate(new \DateTime($data['birthdate']));
            } catch (\Exception) {
                throw new \InvalidArgumentException("Le format de la date d'anniversaire est invalide");
            }
        }

        $this->userRepository->save($user);

    }

}