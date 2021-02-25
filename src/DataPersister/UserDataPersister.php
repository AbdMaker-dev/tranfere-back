<?php

namespace App\DataPersister;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private $manager;
    private $encode;
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encode)
    {
      $this->manager=$manager;
      $this->encode = $encode;
    }
    public function supports($data, array $context = []): bool
    {
        
        return $data instanceof User;
    }

    public function persist($data, array $context = [])
    {
      // call your persistence layer to save $data
      $pwd_encoder = $this->encode->encodePassword($data, $data->getPassword());
      $data->setPassword($pwd_encoder);
      
      $this->manager->persist($data);
      $this->manager->flush();
      return $data;
    }

    public function remove($data, array $context = [])
    {
      $data->setStatut(!$data->getStatut()); 
      $this->manager->persist($data);
      $this->manager->flush();
      // call your persistence layer to delete $data
    }
}