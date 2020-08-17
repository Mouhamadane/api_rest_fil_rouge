<?php

namespace App\Controller;

use DateTime;
use App\Entity\Promos;
use App\Entity\Groupes;
use App\Entity\Apprenant;
use App\Entity\Referentiel;
use App\Repository\ApprenantRepository;
use App\Repository\FormateurRepository;
use App\Repository\PromosRepository;
use App\Repository\ReferentielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PromosController extends AbstractController
{
    private $encoder;
    private $validator;
    private $em;
    private $serializer;

    public function __construct(UserPasswordEncoderInterface $encoder, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->encoder = $encoder;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * @Route(
     *      path="api/admin/promos/principal",
     *      name="promos_groupe_principal",
     *      methods="GET",
     *      defaults={
     *          "_controller"="\app\PromosController::getPromoGroupesPrincipal",
     *           "_api_resource_class"=Promos::class,
     *           "_api_collection_operation_name"="get_Promos_Principal"
     *      }
     * )
     */
    public function getPromosGroupePrincipal(PromosRepository $repo){
        $promos = $repo->findByGroup("principal");
        return $this->json($promos, Response::HTTP_OK);
    }
    
    /**
     * @Route(
     *      path="api/admin/promos/{id}/principal",
     *      name="promo_groupe_principal",
     *      methods="GET",
     *      defaults={
     *          "_controller"="\app\PromosController::getPromoGroupePrincipal",
     *           "_api_resource_class"=Promos::class,
     *           "_api_item_operation_name"="get_Promo_Principale"
     *      }
     * )
     */
    public function getPromoGroupePrincipal(PromosRepository $repo, $id){
        $promo = $repo->findOneByGroup("principal", $id);
        return $this->json($promo, Response::HTTP_OK);
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
     *      }
     * )
     */
    public function addPromo(Request $req, ReferentielRepository $reporef, FormateurRepository $repoformateurs, \Swift_Mailer $mailer, ApprenantRepository $repoApprenant)
    {
        $promos= new Promos();

        if(!$this->isGranted('PROMO_CREATE', $promos)){
            return $this->json([
                "message" => "Vous n'avez pas accès à cette ressource"
            ], Response::HTTP_FORBIDDEN);
        }

        $promoTab=json_decode($req->getContent(),true);
        $promos
            ->setLangue($promoTab["langue"])
            ->setTitre($promoTab["titre"])
            ->setDateProvisoire(new \DateTime($promoTab["dateProvisoire"]))
            ->setDateDebut(new \DateTime($promoTab["dateDebut"]))
            ->setFabrique("Sonatel Academie")
            ->setDescription($promoTab["description"]);
        
        $referentiel= $reporef->find($promoTab["referentiel"]["id"]); 
        if($referentiel){
            $promos->setReferentiel($referentiel);
        }else{
            return $this->json(["message"=>"Le referentiel est obligatoire"]);
        }
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
            $groupe= new Groupes();
            $groupeTab=$promoTab["groupes"][0];
            $groupe
                ->setNom($groupeTab['nom'])
                ->setType("principal")
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
            // dd($groupe);
            $promos->addGroupe($groupe);
        }else{
            return $this->json(["message"=>"l'ajout de  groupe est obligatoire principale"]);
        }
        $errors = $this->validator->validate($promos);
        if (count($errors)){
            $errors = $this->serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $this->em->persist($promos);
        $this->em->flush();
        return $this->json("Promo ajoutée avec succès", Response::HTTP_CREATED);
    }
}
