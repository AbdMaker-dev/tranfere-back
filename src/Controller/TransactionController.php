<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Transaction;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
{
    
    // -------------------------------------------------Pour le depot
    /**
     *  @Route(
     *  "api/transactions/depot",
     *   name="depot",
     *   methods={"POST"}
     * )
     */
    public function depot(SerializerInterface $serializerInterface, Request $request, EntityManagerInterface $manager)
    { 
       $data = json_decode($request->getContent(), true);
       if (!$this->getUser() || $this->getUser()->getAgence() === null) {
            return $this->json(['message' => 'Accès non autorisé'], 403);
       }

       if ($this->getUser()->getAgence()->getCompte()->getMontant() < 5000 || $this->getUser()->getAgence()->getCompte()->getMontant() < $data['montant']) {
            return $this->json(['message' => 'Vous n \'avez assez d\'argent sur votre compte'], 401);
       }
        $data['montant'] = \floatval($data['montant']);
        $transactions = $serializerInterface->denormalize($data, "App\Entity\Transaction");

        $restMontant = $this->getUser()->getAgence()->getCompte()->getMontant() - $transactions->getMontant();
        $this->getUser()->getAgence()->getCompte()->setMontant($restMontant);
        
       
        $transactions->initialise($this->getUser());
        
        $manager->persist($transactions);
        $manager->flush();
        $this->getUser()->setPassword('');
        return $this->json(['message' => 'Succes', 'data'=>$transactions]);

    }

    // -------------------------------------------------Pour recupere les frais de depost
    /**
     *  @Route(
     *  "api/transactions/frais/{montant}",
     *   name="frais",
     *   methods={"GET"}
     * )
     */
    public function frais(float $montant)
    {
        $transaction = new Transaction();
        $transaction->setMontant(\floatval($montant));
        $transaction->calculeFraisTotal();
        return $this->json(['message' => 'Succes', 'frais'=>$transaction->getFraisTotal()]);
    }

    // -------------------------------------------------Pour recupere les frais de depost
    /**
     *  @Route(
     *  "api/transactions/transaction/{code}",
     *   name="transe",
     *   methods={"GET"}
     * )
     */
    public function getTransaction(String $code, TransactionRepository $repo)
    {
       
        $transaction = $repo->findOneByCodeTransaction($code);
        // return $this->json(['message' => 'Succes', 'data'=>'oki']);
        return $this->json(['message' => 'Succes', 'data'=>$transaction]);
    }


    /**
     *  @Route(
     *  "api/transactions/annuller/{code}",
     *   name="anunullation",
     *   methods={"GET"}
     * )
     */  
    public function annullTransaction(String $code, TransactionRepository $repo, EntityManagerInterface $em)
    {
        $transaction = $repo->findOneByCodeTransaction($code);

        if (!$transaction) {
            return $this->json(['message' => 'Succes', 'msg'=>"Code invalide"]);
        }
        $em->remove($transaction);
        $em->flush();
        // return $this->json(['message' => 'Succes', 'data'=>'oki']);
        return $this->json(['message' => 'Succes', 'data'=>"sucess"]);
    }


    // -------------------------------------------------Pour recupere les frais de depost
    /**
     *  @Route(
     *  "api/transactions/user",
     *   name="mesTransaction",
     *   methods={"GET"}
     * )
     */
    public function getuserTransaction(TransactionRepository $repo)
    {
        
        if (!$this->getUser() || $this->getUser()->getAgence() === null) {
            return $this->json(['message' => 'Accès non autorisé'], 403);
        }

        

        $alltransactions = $repo->findAll();
        $user_transactions= [];
        foreach ($alltransactions as $value) {
            if ($value->getUserDepot() == $this->getUser() || $value->getUserRetrait() == $this->getUser()) {
                $user_transactions[] = $value;
            }
        }
        // return $this->json(['message' => 'Succes', 'data'=>'oki']);
        return $this->json(['message' => 'Succes', 'data'=>$user_transactions]);
    }

    /**
     *  @Route(
     *  "api/transactions/encoure",
     *   name="mesTransactionencoure",
     *   methods={"GET"}
     * )
     */
    public function getuserTransactionencoure(TransactionRepository $repo)
    {
        if (!$this->getUser() || $this->getUser()->getAgence() === null) {
            return $this->json(['message' => 'Accès non autorisé'], 403);
        }
        $alltransactions = $repo->findAll();
        $user_transactions= [];
        foreach ($alltransactions as $value) {
            if ($value->getDateRetrait() == null) {
                if ($value->getUserDepot() == $this->getUser() || $value->getUserRetrait() == $this->getUser()) {
                    $user_transactions[] = $value;
                }
            }
        }
        // return $this->json(['message' => 'Succes', 'data'=>'oki']);
        return $this->json(['message' => 'Succes', 'data'=>$user_transactions]);
    }

    // -------------------------------------------------Pour recupere les frais de depost
    /**
     *  @Route(
     *  "api/transactions/agence",
     *   name="agenceTransaction",
     *   methods={"GET"}
     * )
     */
    public function getAllAgenceTransaction(TransactionRepository $repo)
    {
        if (!$this->getUser() || $this->getUser()->getAgence() === null) {
            return $this->json(['message' => 'Accès non autorisé'], 403);
        }
        
        $alltransactions = $repo->findAll();
        
        $user_agence = $this->getUser()->getAgence()->getUsers()->toArray(); 

        // return $this->json(['message' => 'Succes', 'data'=>$user_agence]);

        $user_transactions= [];
        foreach ($alltransactions as $value) {
            if (\in_array($value->getUserDepot(), $user_agence) || \in_array($value->getUserRetrait(), $user_agence)) {
                // $value->setDateDepot($value->getDateDepot()->format('d-m-Y'));
                $user_transactions[] = $value;
            }
        }
        // return $this->json(['message' => 'Succes', 'data'=>'oki']);
        return $this->json(['message' => 'Succes', 'data'=>$user_transactions]);
    }

    /**
     *  @Route(
     *  "api/commissions",
     *   name="commission",
     *   methods={"GET"}
     * )
     */
    public function getAllCommission(TransactionRepository $repo)
    {
        if (!$this->getUser() || $this->getUser()->getAgence() === null) {
            return $this->json(['message' => 'Accès non autorisé'], 403);
        }
        
        $alltransactions = $repo->findAll();
        
        $user_agence = $this->getUser()->getAgence()->getUsers()->toArray(); 
        $commissions= [];
        foreach ($alltransactions as $value) {
            if (\in_array($value->getUserDepot(), $user_agence)) {
                // $value->setDateDepot($value->getDateDepot()->format('d-m-Y'));
                $commissions[] = ["date"=>$value->getDateDepot(), "type"=> "depot", "montant" => $value->getFraisEnvoi(),];
            }else if(\in_array($value->getUserRetrait(), $user_agence)){
                $commissions[] = ["date"=>$value->getDateDepot(), "type"=> "retrait", "montant" => $value->getFraisRetrait()];
            }
        }
        return $this->json(['message' => 'Succes', 'data'=>$commissions]);
    }



    // -------------------------------------------------Pour le retrait
     /**
     *  @Route(
     *  "api/transactions/retrait",
     *   name="retrait",
     *   methods={"POST"}
     * )
     */
    public function retrait(Request $request, EntityManagerInterface $manager, TransactionRepository $repo)
    {
        $data = json_decode($request->getContent(), true);
        if (!$this->getUser() || $this->getUser()->getAgence() === null) {
         return $this->json(['message' => 'Accès non autorisé'], 403);
        }

        $transactions = $repo->findOneByCodeTransaction($data['codeTransaction']);
        if ($transactions->getDateRetrait()) {
            return $this->json(['message' => 'Vous avez déjà recuperé votre argent'], 401);
        }

        if ($this->getUser()->getAgence()->getCompte()->getMontant() < $transactions->getMontant()) {
         return $this->json(['message' => 'Vous n \'avez assez d\'argent sur votre compte'], 401);
        }

        if (!$data['clientRetrait'] || $transactions->getClientRetrait()->getPhone() !== $data['clientRetrait']) {
            return $this->json(['message' => 'les informations du client ne correspondent pas!!!'], 401);
        } 
        $restMontant = $this->getUser()->getAgence()->getCompte()->getMontant() - $transactions->getMontant();
    
        $this->getUser()->getAgence()->getCompte()->setMontant($restMontant);
        $transactions->setDateRetrait(new \DateTime());
        $this->getUser()->setPassword(null);
        $transactions->setUserRetrait($this->getUser());
        
        $manager->flush();
        return $this->json(['message' => 'Succes', 'data'=>$transactions]);

        
    }

    // ----------------------------------------Pour le rechargement d'un compte d'une Agence
     /**
     *  @Route(
     *  "api/rechargeComptes/{id}",
     *   name="rechargeCompte",
     *   methods={"PUT"}
     * )
     */
    public function rechargeCompte(Request $request, EntityManagerInterface $manager, CompteRepository $repo,int $id)
    {
        $data = json_decode($request->getContent(), true);
        if (!$this->getUser() || (!in_array('ROLE_AdminSystem', $this->getUser()->getRoles()) && !in_array('ROLE_Caissier', $this->getUser()->getRoles()))) {
         return $this->json(['message' => 'Accès non autorisé'], 403);
        }
        $compte = $repo->find($id);
        if (!$compte) {
            return $this->json(['message' => 'Le compte n\'existe pas'], 401);
        }
        $newMontantCompte = $compte->getMontant() + $data['montantDepot'];
        $compte->setMontant($newMontantCompte);
        $depot = new Depot();
        $depot->setMontantDepot($data['montantDepot']);
        $depot->setUserDepot($this->getUser());
        $depot->setDateDepot(new \DateTime());
        $compte->addDepot($depot);
        $manager->flush();
        return $this->json(['message' => 'Succes', 'data'=>$compte]);
    } 

    // calcul des parts
    public function calculPart($pourcent, $montant)
    {
            return ($pourcent*$montant)/100;
    }
    
        // pour generer aleatoirement les codes de transaction
    public function genereCodeTransaction($longueur=6) {
            $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $longueurMax = strlen($caracteres);
            $chaineAleatoire = '';
            for ($i = 0; $i < $longueur; $i++)
            {
            $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
            }
            return $chaineAleatoire;
    }


    public function sendSms(){
        $sid ="ACfacc75db86af1d3c2a2aa38ecdbe3697";
        $token = "8c47f7d261ae01b7d3552d480551d7db";
    }

}