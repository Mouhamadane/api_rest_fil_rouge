<?php

namespace App\Controller;

use App\Entity\StatistiquesCompetences;
use DateTime;
use App\Entity\Promos;
use App\Entity\Groupes;
use App\Entity\Apprenant;
use App\Entity\Referentiel;
use App\Repository\ApprenantRepository;
use App\Repository\FormateurRepository;
use App\Repository\PromosRepository;
use App\Repository\ReferentielRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
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
                        $tab = new ArrayCollection();
                        $groupeCompetences = $referentiel->getGroupeCompetences();
                        foreach ($groupeCompetences as $groupeCompetence){
                            $competences = $groupeCompetence->getCompetences();
                            foreach ($competences as $competence){
                                if (!$tab->contains($competence)){
                                    $statistique = new StatistiquesCompetences();
                                    $statistique
                                        ->setReferentiel($referentiel)
                                        ->setCompetence($competence)
                                        ->setNiveau1(false)
                                        ->setNiveau2(false)
                                        ->setNiveau3(false)
                                    ;
                                    $promos->addStatistiquesCompetence($statistique);
                                    $tab[] = $competence;
                                }
                            }

                        }

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
        $errors = $this->validator->validate($promos);
        if (count($errors)){
            $errors = $this->serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $this->em->persist($promos);
        $this->em->flush();
        return $this->json("Promo ajoutée avec succès", Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *      path="/api/admin/promos/{id}", 
     *      name="update_promo",
     *      methods="PUT",
     *      defaults={
     *           "_controller"="\app\PromosController::updatePromo",
     *           "_api_resource_class"=Promos::class,
     *           "_api_item_operation_name"="update_promo"
     *  }
     * )
     */
    public function updatePromo(Request $req, PromosRepository $repo, int $id)
    {
        $ref = json_decode($req->getContent(), true);
        $promo = $repo->find($id);
        if(!empty($ref["referentiel"]) and isset($ref["referentiel"]["id"])){
            if($ref["referentiel"]["id"] === $promo->getReferentiel()->getId()){
                if(isset($ref["referentiel"]["libelle"])){
                    $promo->getReferentiel()->setLibelle($ref["referentiel"]["libelle"]);
                }
            }
        }
        $this->em->flush();
        return $this->json($promo, Response::HTTP_OK);
    }

    /**
     * @Route(
     *      path="/api/admin/promos/{id}/apprenants", 
     *      name="update_promo_apprenant",
     *      methods="PUT",
     *      defaults={
     *           "_controller"="\app\PromosController::updatePromoApprenant",
     *           "_api_resource_class"=Promos::class,
     *           "_api_item_operation_name"="update_promo_apprenant"
     *  }
     * )
     */
    public function updatePromoApprenant(Request $req, PromosRepository $repo, int $id, \Swift_Mailer $mailer, ApprenantRepository $repoApprenant)
    {
        $promo = $repo->find($id);
        $tab = json_decode($req->getContent(), true);
        if(!empty($tab["apprenants"])){
            foreach($tab["apprenants"] as $apprenant){
                if(isset($apprenant["id"]) && !isset($apprenant["email"])){
                    foreach($promo->getGroupes() as $k=>$groupe){
                        foreach($groupe->getApprenant() as $app){
                            if($app->getId() == $apprenant["id"]){
                                $promo->getGroupes()[$k]->removeApprenant($app);
                            }
                        }
                    }
                }elseif(!isset($apprenant["id"]) && isset($apprenant["email"])){
                    $newApprenant = $repoApprenant->findOneBy($apprenant);
                    if ($newApprenant) {
                        foreach($promo->getGroupes() as $k=>$groupe){
                            if($groupe->getType() == "principal"){
                                $promo->getGroupes()[$k]->addApprenant($newApprenant);
                            }
                        }
                        $message = (new \Swift_Message("Admission Sonatel Academy"))
                            ->setFrom("damanyelegrand@gmail.com")
                            ->setTo($newApprenant->getEmail())
                            ->setBody("Bonjour ".$newApprenant->getPrenom()." ".$newApprenant->getNom()." vous êtes selectionnés à la 3èm cohorte de la Sonatel Academy.\nNous vous souhaitons la bienvenue et vous prions de suivre ce lien afin de confirmer votre admission.\nMerci.");
                        $mailer->send($message);
                    }
                }
            }
        }
        $this->em->flush();
        return $this->json($promo, Response::HTTP_OK);
    }

    /**
     * @Route(
     *      path="/api/admin/promos/{id}/formateurs", 
     *      name="update_promo_formateur",
     *      methods="PUT",
     *      defaults={
     *           "_controller"="\app\PromosController::updatePromoFormateur",
     *           "_api_resource_class"=Promos::class,
     *           "_api_item_operation_name"="update_promo_formateur"
     *  }
     * )
     */
    public function updatePromoFormateur(Request $req, PromosRepository $repo, int $id, \Swift_Mailer $mailer, FormateurRepository $repoForm)
    {
        $promo = $repo->find($id);
        $tab = json_decode($req->getContent(), true);
        if(!empty($tab["formateurs"])){
            foreach($tab["formateurs"] as $id){
                $trouve = false;
                foreach($promo->getFormateur() as $formateur){
                    if($formateur->getId() == $id["id"]){
                        $trouve = true;
                        $promo->removeFormateur($formateur);
                    }
                }
                if(!$trouve){
                    $formateur = $repoForm->findOneBy($id);
                    if($formateur){
                        $promo->addFormateur($formateur);
                    }
                }
            }
        }
        $this->em->flush();
        return $this->json($promo, Response::HTTP_OK);
    }
    
    /**
     * @Route(
     *      path="/api/admin/promos/{id}/groupes", 
     *      name="ajouter_promo_groupe",
     *      methods="PUT",
     *      defaults={
     *           "_controller"="\app\PromosController::ajouterGroupePromo",
     *           "_api_resource_class"=Promos::class,
     *           "_api_item_operation_name"="ajouter_promo_groupe"
     *  }
     * )
     */
    public function ajouterGroupePromo(Request $req, PromosRepository $repo, int $id, Security $security)
    {
        if($security->getUser()->getRoles()[0] === "ROLE_FORMATEUR"){
            $promo = $repo->find($id);
            $groupePrincipal = $promo->getGroupes()[0];
            $promoTab = json_decode($req->getContent(), true);
            $groupes = $promoTab["groupeTab"];
            foreach($groupes as $grp){
                $groupe = new Groupes();
                $groupe
                    ->setNom($grp['nom'])
                    ->setType("secondaire")
                    ->setStatut(true)
                    ->setDateCreation(new \DateTime());
                if(!empty($grp["apprenants"])){
                    foreach($grp["apprenants"] as $app){
                        $apprenantTrouve = null;
                        foreach($groupePrincipal->getApprenant() as $apprenant){
                            if($apprenant->getEmail() == $app["email"]){
                                $apprenantTrouve = $apprenant;
                            }
                        }
                        if($apprenantTrouve){
                            $groupe->addApprenant($apprenantTrouve);
                        }else{
                            return $this->json(["message" => $app["email"]." n'est pas dans la promotion." ], Response::HTTP_BAD_REQUEST);
                        }
                    }
                }
                $groupe->addFormateur($security->getUser());
                $promo->addGroupe($groupe);
            }
            
            $this->em->flush();
            return $this->json($promo, Response::HTTP_OK);

        }
    }

    /**
     * @Route(
     *      path="/api/admin/promos/{id}/groupes/{idgrpe}", 
     *      name="update_promo_groupe",
     *      methods="PUT",
     *      defaults={
     *           "_controller"="\app\PromosController::updatePromoGroupe",
     *           "_api_resource_class"=Promos::class,
     *           "_api_item_operation_name"="update_promo_groupe"
     *  }
     * )
     */
    public function updatePromoGroupe(Request $req, PromosRepository $repo, int $id, int $idgrpe)
    {
        $promo = $repo->find($id);
        foreach($promo->getGroupes() as $groupe){
            if($groupe->getId() == $idgrpe){
                $groupe->setStatut(false);
            }
        }
        $this->em->flush();
        return $this->json($promo, Response::HTTP_OK);
    }
}
