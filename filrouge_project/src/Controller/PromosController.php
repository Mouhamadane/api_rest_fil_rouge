<?php

namespace App\Controller;

use DateTime;
use App\Entity\Promos;
use App\Entity\Groupes;
use App\Entity\Apprenant;
use App\Entity\Referentiel;
use PhpParser\Node\Stmt\Foreach_;
use App\Repository\ApprenantRepository;
use App\Repository\FormateurRepository;
use App\Repository\ReferentielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ContainerQb8dLCW\getPromosRepositoryService;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PromosController extends AbstractController
{
    private $encode;
    public function __construct(UserPasswordEncoderInterface $encode, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->encode = $encode;
        $this->serializer = $serializer;
        $this->em = $em;
    }
    
    /**
     * @Route(
     *      path="/api/admin/promos", 
     *      name="promos",
     *      methods="POST",
     *      defaults={
     *           "_controller"="\app\PromosController::addPromo",
     *           "_api_resource_class"=Promos::class,
     *           "_api_collection_operation_name"="add_Promos"
     *  }
     * )
     */
    public function addPromo(Request $req, ReferentielRepository $reporef, FormateurRepository $repoformateurs, \Swift_Mailer $mailer, ApprenantRepository $repoApprenant, ValidatorInterface $validate)
    {
        
        $promoTab=json_decode($req->getContent(),true);
        $promos= new Promos();
        $promos
            ->setLangue($promoTab['langue'])
            ->setTitre($promoTab['titre'])
            ->setDateProvisoire(new \DateTime($promoTab['dateProvisoire']))
            ->setDateDebut(new \DateTime($promoTab['dateDebut']))
            ->setFabrique($promoTab['fabrique'])
            ->setLieu($promoTab['lieu'])
            ->setDescription($promoTab['description']);
        $referentiel= $reporef->find($promoTab['referentiel']['id']); 
        if($referentiel){
            $promos->setReferentiel($referentiel);
        }else{
            return $this->json(['message'=>'Le referentiel est obligatoire']);
        }
        $groupe= new Groupes();
        // Ajouter un Formateur
        if(!empty($promoTab["formateurs"])){
            foreach ($promoTab["formateurs"] as $key) {
                $formateur = $repoformateurs->find($key["id"]);
                if($formateur){
                        $promos->addFormateur($formateur);
                        
                 }else{
                    return $this->json(["message"=>"l'id n'est pas celui d'un formateur"],Response::HTTP_BAD_REQUEST);
                 }
            }
        }else{
            return $this->json(["message"=>"l'ajout d'un formateur est obligatoire"]);
        }
        if(!empty($promoTab["groupes"])){
            $groupeTab=$promoTab["groupes"][0];
            $groupe
                ->setNom($groupeTab['nom'])
                ->setType($groupeTab['type'])
                ->setStatut(true)
                ->setDateCreation(new \DateTime());
            // Ajouter un apprenant
            if (!empty($groupeTab["apprenants"])) {
                foreach ($groupeTab["apprenants"] as  $email) {
                    $apprenant = $repoApprenant->findOneBy($email);
                    if ($apprenant) {
                        $groupe->addApprenant($apprenant);
                        $message = (new \Swift_Message("Admission Sonatel Academy"))
                                ->setFrom("damanyelegrand@gmail.com")
                                ->setTo($apprenant->getEmail())
                                ->setBody("Bonjour ".$apprenant->getPrenom()." ".$apprenant->getNom()." vous êtes selectionnés à la 3èm cohorte de la Sonatel Academy.\nNous vous souhaitons la bienvenue et vous prions de suivre ce lien afin de confirmer votre admission.\nMerci.");
                        $mailer->send($message);
                    }
                }
            }
            $promos->addGroupe($groupe);

        }else{
            return $this->json(["message"=>"l'ajout de  groupe est obligatoire principale"]);
        }
        $errors = $validate->validate($promos);
        if (count($errors)){
            $errors = $this->serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $this->em->flush();
        return $this->json($promos, Response::HTTP_CREATED);
    }
}
