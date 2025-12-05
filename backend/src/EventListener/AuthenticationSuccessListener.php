<?php

namespace App\EventListener;


use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class AuthenticationSuccessListener
{
    public function __construct(
        //Service permettant de décoder un JWT (entre autres)
        private JWTTokenManagerInterface $jwtManager
    )
    {}

    //On doit indiquer le nom de l'événement qu'on souhaite écouter (car il provient d'un bundle et n'est pas natif à Symfony)
    #[AsEventListener('lexik_jwt_authentication.on_authentication_success')]
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        //Insertion de données de l'utilisateur ici - À compléter
        $data['email'] = $user->getUserIdentifier();
        $data['firstName'] = $user->getFirstName();

        //Récupération des données contenues de le JWT - À compléter
        //On décode le jwt qui est déjà encodé, à ce stade, afin de récupérer les informations qui nous intéressent.
        $jwt = $this->jwtManager->parse($data['token']);
        $data['token_exp'] = $jwt['exp'];

        $event->setData($data);
    }
}