<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompteController extends AbstractController
{
 // -------------------------------------------------
    /**
     *  @Route(
     *  "api/user/comptes",
     *   name="compte_user",
     *   methods={"GET"}
     * )
     */
    public function depot(SerializerInterface $serializerInterface): Response
    { 
       if (!$this->getUser() || $this->getUser()->getAgence() === null) {
            return $this->json(['message' => 'Accès non autorisé'], 403);
       }
        $user = $this->getUser()->setPassword('');
        
        $user = $serializerInterface->serialize($user, "json", ["groups"=>"user:read"]);
        return $this->json([
            'message' => 'oki',
            'code' => 200,
            'data' => $user
        ]);
    }
}