<?php

namespace App\Controller;

use App\Entity\Brief;
use App\Entity\LivrablesAttendus;
use App\Entity\PromoBrief;
use App\Entity\PromoBriefApprenant;
use App\Entity\Ressource;
use App\Repository\ApprenantRepository;
use App\Repository\BriefRepository;
use App\Repository\TagRepository;
use App\Repository\NiveauRepository;
use App\Repository\GroupesRepository;
use App\Repository\PromoBriefRepository;
use App\Repository\PromosRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReferentielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class BriefController extends AbstractController
{
    private $serializer;
    private $em;
    private $security;
    private $repoNiveau;
    private $repoTag;
    private $validator;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        Security $security,
        ValidatorInterface $validator,
        TagRepository $repoTag,
        NiveauRepository $repoNiveau
    )
    {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->security = $security;
        $this->repoNiveau = $repoNiveau;
        $this->repoTag = $repoTag;
        $this->validator = $validator;
    }

    /**
     * @Route(
     *      name="dupliquer_brief",
     *      path="api/formateurs/briefs/{id}",
     *      methods="POST",
     *      defaults={
     *          "_controller"="\app\BriefController::dupliquerBrief",
     *          "_api_resource_class"=Brief::class,
     *          "_api_collection_operation"="dupliquer_brief"
     *      }
     * )
     */
    public function dupliquerBrief(BriefRepository $repo, int $id, ValidatorInterface $validator) {
        $newBrief = new Brief;
        $brief = $repo->find($id);
        if($brief){
            $newBrief
                ->setLangue($brief->getLangue())
                ->setTitre($brief->getTitre())
                ->setDescription($brief->getDescription())
                ->setContexte($brief->getContexte())
                ->setLivrablesAttendus($brief->getLivrablesAttendus())
                ->setModaliteEvaluation($brief->getModaliteEvaluation())
                ->setCriterePerformance($brief->getCriterePerformance())
                ->setmodalitePedagogique($brief->getmodalitePedagogique())
                ->setReferentiel($brief->getReferentiel())
                ->setFormateur($this->security->getUser())
                ->setStatut("valide")
                ->setDateCreation(new \DateTime());

            foreach($brief->getTags() as $tag){
                $newBrief->addTag($tag);
            }

            foreach($brief->getLivrablesAttenduses() as $livrableAtt){
                $newBrief->addLivrablesAttendus($livrableAtt);
            }
            
            /* foreach($brief->getNiveaux() as $niveau){
                $newBrief->addNiveau($niveau);
            } */
            
            foreach($brief->getRessources() as $res){
                $newBrief->addRessource($res);
            }
            $errors = $validator->validate($brief);
            if (count($errors)){
                $errors = $this->serializer->serialize($errors,"json");
                return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
            }
            $this->em->persist($brief);
            $this->em->flush();
            return $this->json(["message" => "Brief dupliqué"], Response::HTTP_CREATED);
        }else{
            return $this->json(["message" => "Resource Not found"], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route(
     *      name="ajouter_livrables",
     *      path="api/apprenants/{id}/groupes/{idg}/livrables",
     *      methods="POST",
     *      defaults={
     *          "_controller"="\app\BriefController::ajouterLivrables",
     *          "_api_resource_class"=Brief::class,
     *          "_api_collection_operation"="ajouter_livrables"
     *      }
     * )
     */
    public function ajouterLivrable(Request $req, int $id, int $idg, ApprenantRepository $repoApp, GroupesRepository $repoGroupe) {
        $apprenant = $repoApp->find($id);
        if($apprenant === $this->security->getUser()){
            $groupe = $repoGroupe->find($idg);
            if($groupe){
                
            }else{
                return $this->json(["message" => "Le groupe n'esxite pas"], Response::HTTP_BAD_REQUEST);
            }
        }else{
            return $this->json(["message" => "Vous n'avez pas accès à cette ressource"], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route(
     *      name="ajouter_brief",
     *      path="api/formateurs/briefs",
     *      methods="POST",
     *      defaults={
     *          "_controller"="\app\BriefController::ajouterBrief",
     *          "_api_resource_class"=Brief::class,
     *          "_api_collection_operation"="ajouter_brief"
     *      }
     * )
     */
    public function ajouterBrief(
        Request $req,
        ReferentielRepository $repoRef,
        GroupesRepository $repoGrpe,
        \Swift_Mailer $mailer
    ) {
        $brief = new Brief;
        $briefTab = json_decode($req->getContent(), true);

        $brief
            ->setLangue($briefTab["langue"])
            ->setTitre($briefTab["titre"])
            ->setDescription($briefTab["description"])
            ->setContexte($briefTab["contexte"])
            ->setLivrablesAttendus($briefTab["livrablesAttendus"])
            ->setModalitePedagogique($briefTab["modalitePedagogique"])
            ->setCriterePerformance($briefTab["criterePerformance"])
            ->setModaliteEvaluation($briefTab["modaliteEvaluation"])
            ->setDateCreation(new \DateTime());

        if(isset($briefTab["tags"]) && !empty($briefTab["tags"])){
            foreach($briefTab["tags"] as $idTag){
                $tag = $this->repoTag->find($idTag);
                if($tag){
                    $brief->addTag($tag);
                }
            }
        }

        if(isset($briefTab["niveaux"]) && !empty($briefTab["niveaux"])){
            foreach($briefTab["niveaux"] as $idNiveau){
                $niveau = $this->repoNiveau->find($idNiveau);
                if($niveau){
                    if($niveau->getBrief()){
                        return $this->json(["message" => "Le niveau de competence". $niveau->getCompetence()->getLibelle() ."est déjà affecté."], Response::HTTP_BAD_REQUEST);
                    }else{
                        $brief->addNiveau($niveau);
                    }
                }
            }
        }
        
        if(isset($briefTab["livrablesAttenduses"]) && !empty($briefTab["livrablesAttenduses"])){
            foreach($briefTab["livrablesAttenduses"] as $lvb){
                $livrableAtt = new LivrablesAttendus();
                $livrableAtt->setLibelle($lvb["libelle"]);
                $brief->addLivrablesAttendus($livrableAtt);
            }
        }

        if(isset($briefTab["referentiel"]) && !empty($briefTab["referentiel"])){
            $ref = $repoRef->find($briefTab["referentiel"]);
            if($ref){
                $brief->setReferentiel($ref);
            }else{
                return $this->json(["message" => "Le referentiel est obligatoire"], Response::HTTP_BAD_REQUEST);
            }
        }else{
            return $this->json(["message" => "Le referentiel est obligatoire"], Response::HTTP_BAD_REQUEST);
        }

        if(!empty($briefTab["groupes"])){
            foreach($briefTab["groupes"] as $grpe){
                $groupe = $repoGrpe->find($grpe);
                if($groupe){
                    $brief->addGroupe($groupe);
                    foreach($groupe->getApprenant() as $appren){
                        $message = (new \Swift_Message("Admission Sonatel Academy"))
                            ->setFrom("damanyelegrand@gmail.com")
                            ->setTo($appren->getEmail())
                            ->setBody("Bonjour ".$appren->getPrenom()." ".$appren->getNom()."\nLe brief ". $brief->getTitre() ." vous a été assigné.\nMerci.");
                        $mailer->send($message);
                    }
                    $promoBrief = new PromoBrief();
                    $promoBrief
                        ->setStatut("en_cours")
                        ->setPromos($groupe->getPromos())
                        ->setBrief($brief);
                    $this->em->persist($promoBrief);
                }
            }
            $brief->setStatut("assigne");
        }else{
            $brief->setStatut("brouillon");
        }
        $brief->setFormateur($this->security->getUser());
        $errors = $this->validator->validate($brief);
        if (count($errors)){
            $errors = $this->serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $this->em->flush();
        return $this->json(["message" => "Brief créé"], Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *      name="update_brief",
     *      path="api/formateurs/promos/{id}/briefs/{idb}",
     *      methods="PUT",
     *      defaults={
     *          "_controller"="\app\BriefController::updateBrief",
     *          "_api_resource_class"=Brief::class,
     *          "_api_collection_operation"="update_brief"
     *      }
     * )
     */
    public function updateBrief(Request $req, PromoBriefRepository $repo, int $id, int $idb) {
        $promoBrief = $repo->findByPromoAndBrief($id, $idb);
        // Si le brief de la promo existe!!!
        if($promoBrief){
            $tab = json_decode($req->getContent(), true);

            // Archiver le brief,Clocturer le brief
            if(isset($tab["statut"]) && !empty($tab["statut"])){
                if($tab["statut"] == "cloturer"){
                    $promoBrief->setStatut("cloture");
                    $message = ["message" => "Le brief de la promo cloturé"];
                }elseif($tab["statut"] === "archiver"){
                    if($promoBrief->getBrief()->getFormateur() === $this->security->getUser()){
                        $promoBrief->getBrief()->setStatut("archive");
                        $message = ["message" => "Brief supprimé(archive)"];
                    }else{
                        return $this->json(["message" => "Vous n'avez pas le droit d'archiver le brief."], Response::HTTP_UNAUTHORIZED);
                    }
                }
            }

            // Ajout ou Supprimer un niveau de compétence
            if(isset($tab["niveaux"]) && !empty($tab["niveaux"])){
                foreach($tab["niveaux"] as $idNiveau){
                    $trouve = false;
                    foreach($promoBrief->getBrief()->getNiveaux() as $niv){
                        if($niv->getId() == $idNiveau["id"]){
                            $trouve = true;
                            $promoBrief->getBrief()->removeNiveau($niv);
                            $message[] = ["message" => "Niveau brief supprimé"];
                        }
                    }
                    if(!$trouve){
                        $niveau = $this->repoNiveau->find($idNiveau);
                        if($niveau){
                            // Vérifier si le niveau est déjà affecter à un brief
                            if($niveau->getBrief()){
                                return $this->json(["message" => "Le niveau de competence". $niveau->getCompetence()->getLibelle() ."est déjà affecté."], Response::HTTP_BAD_REQUEST);
                            }else{
                                $promoBrief->getBrief()->addNiveau($niveau);
                                $message[] = ["message" => "Niveau brief ajouté"];
                            }
                        }else{
                            return $this->json(["message" => "Niveau Not Found."], Response::HTTP_NOT_FOUND);
                        }
                    }
                }
            }

            // Ajout ou Supprimer livrables attendus,
            if(isset($tab["livrablesAttendus"]) && !empty($tab["livrablesAttendus"])){
                foreach($tab["livrablesAttendus"] as $val){
                    if(!empty($val) && isset($val["id"]) && !isset($val["libelle"])){
                        foreach($promoBrief->getBrief()->getLivrablesAttenduses() as $livrableAtt){
                            if($livrableAtt->getId() == $val["id"]){
                                $promoBrief->getBrief()->removeLivrablesAttendus($livrableAtt);
                                $message[] = ["message" => "Livrable attendu brief supprimé"];
                            }
                        }
                    }elseif(!empty($val) && !isset($val["id"]) && isset($val["libelle"])){
                        $livAtt = new LivrablesAttendus;
                        $livAtt->setLibelle($val["libelle"]);
                        $promoBrief->getBrief()->addLivrablesAttendus($livAtt);
                        $message[] = ["message" => "Livrable attendu brief ajouté"];
                    }
                }
            }

            // Ajout ou Supprimer Tags
            if(isset($tab["tags"]) && !empty($tab["tags"])){
                foreach($tab["tags"] as $val){
                    $trouve = false;
                    foreach($promoBrief->getBrief()->getTags() as $tag){
                        if($tag->getId() == $val["id"]){
                            $trouve = true;
                            $promoBrief->getBrief()->removeTag($tag);
                            $message[] = ["message" => "Tag brief supprimé"];
                        }
                    }
                    if(!$trouve){
                        $tag = $this->repoTag->find($val);
                        if($tag){
                            $promoBrief->getBrief()->addTag($tag);
                            $message[] = ["message" => "Tag brief ajouté"];
                        }else{
                            return $this->json(["message" => "Tag Not Found."], Response::HTTP_NOT_FOUND);
                        }
                    }
                }
            }

            // Ajout ou Supprimer ressource
            if(isset($tab["ressources"]) && !empty($tab["ressources"])){
                foreach($tab["ressources"] as $val){
                    if(!empty($val) && isset($val["id"]) && !isset($val["titre"]) && !isset($val["url"])){
                        foreach($promoBrief->getBrief()->getRessources() as $res){
                            if($res->getId() == $val["id"]){
                                $promoBrief->getBrief()->removeRessource($res);
                                $message[] = ["message" => "Ressource brief supprimé"];
                            }
                        }
                    }elseif(!empty($val) && !isset($val["id"]) && isset($val["titre"]) && isset($val["url"])){
                        $ressource = new Ressource;
                        $ressource->setTitre($val["titre"]);
                        $ressource->setUrl($val["url"]);
                        $promoBrief->getBrief()->addRessource($ressource);
                        $message[] = ["message" => "Ressource brief ajouté"];
                    }
                }
            }
            
            $errors = $this->validator->validate($promoBrief);
            if (count($errors)){
                $errors = $this->serializer->serialize($errors,"json");
                return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
            }
            $this->em->flush();
            return $this->json($message, Response::HTTP_OK);

        }else{
            return $this->json(["message" => "PromoBrief Not Found"], Response::HTTP_NOT_FOUND);
        }
        
    }

    /**
     * @Route(
     *      name="assigner_brief",
     *      path="api/formateurs/promos/{id}/briefs/{idb}/assignation",
     *      methods="PUT",
     *      defaults={
     *          "_controller"="\app\BriefController::assignerBrief",
     *          "_api_resource_class"=Brief::class,
     *          "_api_collection_operation"="assigner_brief"
     *      }
     * )
     */
    public function assignerBrief(Request $req, PromosRepository $repoPromo, BriefRepository $repoBrief, int $id, int $idb) {
        
    }
}