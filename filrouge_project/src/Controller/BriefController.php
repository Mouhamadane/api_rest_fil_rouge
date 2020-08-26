<?php

namespace App\Controller;

use App\Entity\Brief;
use App\Entity\BriefLA;
use App\Entity\Ressource;
use App\Entity\PromoBrief;
use App\Entity\LivrablesAttendus;
use App\Repository\TagRepository;
use App\Entity\PromoBriefApprenant;
use App\Repository\BriefRepository;
use App\Repository\NiveauRepository;
use App\Repository\PromosRepository;
use App\Repository\GroupesRepository;
use App\Repository\ApprenantRepository;
use App\Repository\PromoBriefRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReferentielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\LivrablesAttendusRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function dupliquerBrief(BriefRepository $repo, int $id, ValidatorInterface $validator, NormalizerInterface $normalizer) {
        $brief = $repo->find($id);
        if($brief){
            $newBrief = clone $brief;
            $newBrief->setId();
            $newBrief
                ->setFormateur($this->security->getUser())
                ->setStatut("non_assigne")
                ->setDateCreation(new \DateTime());
            
            // Desaffecter les niveaux de competences du Brief dupliqué 
            foreach($newBrief->getNiveaux() as $niveau){
                $newBrief->removeNiveau($niveau);
            }
            
            // Supprimer les livrables des livrables attendus du brief dupliqué

            $newBrief->clearBriefLAs();
            foreach($brief->getBriefLAs() as $k => $briefLA){
                $newBriefLA = clone $briefLA;
                $newBriefLA->setId();
                if(!empty($newBriefLA->getLivrables())){
                    $newBriefLA->clearLivrables();
                }
                $newBrief->addBriefLA($newBriefLA);
            }
            
            // Supprimer les groupes du brief dupliqué
            $newBrief->clearGroupe();
            // dd($normalizer->normalize($newBrief->getGroupes(), 'json', ["groups" => "brief:read"]));
            $errors = $validator->validate($newBrief);
            if (count($errors)){
                $errors = $this->serializer->serialize($errors,"json");
                return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
            }
            $brief = null;
            $this->em->persist($newBrief);
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
        LivrablesAttendusRepository $repoLA,
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
            ->setDateCreation(new \DateTime())
        ;

        // Affecter des tags
        if(isset($briefTab["tags"]) && !empty($briefTab["tags"])){
            foreach($briefTab["tags"] as $idTag){
                $tag = $this->repoTag->find($idTag);
                if($tag){
                    $brief->addTag($tag);
                }
            }
        }

        // Afecter des niveaux de compétence
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
         
        // Ajouter livrables attendus
        if(isset($briefTab["livrablesAtt"]) && !empty($briefTab["livrablesAtt"])){
            $inserted = false;
            foreach($briefTab["livrablesAtt"] as $val){
                $livrableAtt = $repoLA->find($val);
                if($livrableAtt){
                    $briefLA = new BriefLA;
                    $briefLA->setLivrableAttendu($livrableAtt);
                    $brief->addBriefLA($briefLA);
                    $inserted = true;
                }
            }
            if(!$inserted){
                return $this->json(["message" => "Veuillez affecter au moins un livrable attendu"], Response::HTTP_BAD_REQUEST);
            }
        }else{
            return $this->json(["message" => "Veuillez affecter au moins un livrable attendu"], Response::HTTP_BAD_REQUEST);
        }

        // Affecter référentiel
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

        // Affecter groupes
        if(!empty($briefTab["groupes"])){
            foreach($briefTab["groupes"] as $grpe){
                $groupe = $repoGrpe->find($grpe);
                if($groupe){
                    $brief->addGroupe($groupe);
                    $promoBrief = new PromoBrief();
                    $promoBrief
                        ->setStatut("en_cours")
                        ->setPromos($groupe->getPromos())
                        ->setBrief($brief);
                    foreach($groupe->getApprenant() as $appren){
                        $pba = new PromoBriefApprenant;
                        $pba
                            ->setStatut("assigne")
                            ->setPromoBrief($promoBrief)
                            ->setApprenant($appren);
                        $this->em->persist($pba);
                        $texte = (new \Swift_Message("Admission Sonatel Academy"))
                            ->setFrom("damanyelegrand@gmail.com")
                            ->setTo($appren->getEmail())
                            ->setBody("Bonjour ".$appren->getPrenom()." ".$appren->getNom()."\nLe brief ". $brief->getTitre() ." vous a été assigné.\nMerci.");
                        $mailer->send($texte);
                    }
                    $this->em->persist($promoBrief);
                }
            }
            $brief->setStatut("assigne");
        }else{
            $brief->setStatut("valide");
        }
        $brief->setFormateur($this->security->getUser());

        // Ajout ou Supprimer ressource
        if(isset($briefTab["ressources"]) && !empty($briefTab["ressources"])){
            foreach($briefTab["ressources"] as $val){
                if(!empty($val) && isset($val["titre"]) && isset($val["url"])){
                    $ressource = new Ressource;
                    $ressource->setTitre($val["titre"]);
                    $ressource->setUrl($val["url"]);
                    $brief->addRessource($ressource);
                    $message[] = ["message" => "Ressource brief ajouté"];
                }
            }
        }

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
    public function assignerBrief(Request $req, PromosRepository $repoPromo, BriefRepository $repoBrief, PromoBriefRepository $repo, GroupesRepository $repoGroupe, int $id, int $idb, \Swift_Mailer $mailer) {
        $promo = $repoPromo->find($id);
        if($promo){
            $brief = $repoBrief->find($idb);
            if($brief){
                $tab = json_decode($req->getContent(), true);
                // Affecter ou Désaffecter un brief à un apprenant ou des apprenants
                if(isset($tab["apprenants"]) && !empty($tab["apprenants"])){
                    foreach($promo()->getGroupes() as $groupe){
                        if($groupe->getType() === "principal"){
                            $gp = $groupe;
                            break;
                        }
                    }
                    foreach($tab["apprenants"] as $val){
                        $trouve = false;
                        foreach($gp->getApprenant() as $apprenant){
                            if($apprenant->getEmail() == $val["email"]){
                                $trouve = true;
                                $isAssign = false;
                                foreach($apprenant->getPromoBriefApprenants() as $promoBA){
                                    // Désaffecter un brief à un étudiant
                                    if($promoBA->getPromoBrief()->getBrief() == $brief){
                                        $isAssign = true;
                                        $apprenant->removePromoBriefApprenant($promoBA);
                                        $message[] = ["message" => "Brief désassigné à ".$val["email"]];
                                    }
                                } 
                                // Affecter un brief à un étudiant
                                if(!$isAssign){
                                    $promoBrief = $repo->findByPromoAndBrief($id, $idb);
                                    if(!$promoBrief){
                                        $promoBrief = new PromoBrief;
                                        $promoBrief
                                            ->setStatut("en_cours")
                                            ->setPromos($promo)
                                            ->setBrief($brief)
                                        ;
                                    }
                                    $pba = new PromoBriefApprenant;
                                    $pba
                                        ->setStatut("assigne")
                                        ->setPromoBrief($promoBrief);
                                    $texte = (new \Swift_Message("Admission Sonatel Academy"))
                                        ->setFrom("damanyelegrand@gmail.com")
                                        ->setTo($apprenant->getEmail())
                                        ->setBody("Bonjour ".$apprenant->getPrenom()." ".$apprenant->getNom()."\nLe brief ". $brief->getTitre() ." vous a été assigné.\nMerci.");
                                    $mailer->send($texte);
                                    $apprenant->addPromoBriefApprenant($pba);
                                    $message[] = ["message" => "Brief assigné à ".$val["email"]];
                                }
                            }
                        }
                        if(!$trouve){
                            return $this->json(["message" => $val["email"]." n'est pas dans le groupe principale de la promo"], Response::HTTP_NOT_FOUND);
                        }
                    }
                }
            }

            // Affecter brief à un ou plusieurs groupes
            if(isset($tab["groupes"]) && !empty($tab["groupes"])){
                foreach($tab["groupes"] as $val){
                    $groupe = $repoGroupe->find($val);
                    if($groupe){
                        if($groupe->getPromos() == $promoBrief->getPromos()){
                            if (!$groupe->getBriefs()->contains($promoBrief->getBrief())){
                                dd("assigné groupe");
                            }else{
                                return $this->json(["message" => "Le brief est déjà assigné au groupe"], Response::HTTP_NOT_FOUND);
                            }
                        }else{
                            return $this->json(["message" => "Le groupe n'est pas dans la promo"], Response::HTTP_NOT_FOUND);
                        }
                        dd("Groupe trouvé");
                    }else{
                        return $this->json(["message" => "Groupe Not Found"], Response::HTTP_NOT_FOUND);
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
}