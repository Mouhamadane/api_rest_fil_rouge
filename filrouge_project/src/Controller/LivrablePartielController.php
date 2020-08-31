<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Niveau;
use App\Repository\NiveauRepository;
use DateTime;
use App\Entity\LivrablePartiels;
use App\Repository\ApprenantRepository;
use App\Repository\BriefRepository;
use App\Repository\FormateurRepository;
use App\Repository\LivrablePartielsRepository;
use App\Repository\LivrableRenduRepository;
use App\Repository\PromosRepository;
use App\Repository\ReferentielRepository;
use App\Repository\StatistiquesCompetencesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LivrablePartielController extends AbstractController
{
    
    private $user;
    private $validator;
    private $em;
    private $serializer;

    public function __construct(TokenStorageInterface $tokenStorage, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->validator = $validator;
        $this->user = $tokenStorage->getToken()->getUser();
    }
    /**
     * @Route(
     *   name="promo_competences",
     *   path="api/formateurs/promo/{idp}/referentiel/{idr}/competences",
     *   methods={"GET"}
     * )
     */
    public function getApprenantCompetences(FormateurRepository $repo,StatistiquesCompetencesRepository $repository,PromosRepository $promosRepository, $idp,$idr)
    {
        //Manque collection Apprenant
       $promo = $promosRepository->find($idp);
       if($promo){
           $referentiel = $promo->getReferentiel();
            if ($referentiel->getId() == $idr){
                $groupes = $promo->getGroupes();
                foreach ($groupes as $groupe){
                    if (strtolower($groupe->getType())== "principal"){
                        $apprenants = $groupe->getApprenant();
                        foreach ($apprenants as $apprenant){
                            $stats = $repository->findOneBy(array("apprenant"=>$apprenant->getId(),"promos"=>$idp,"referentiel"=>$idr));
                            $competence = $stats->getCompetence();
                            $niveau1 = $stats->getNiveau1();
                            $niveau2 = $stats->getNiveau2();
                            $niveau3 = $stats->getNiveau3();
                            $tab[] = ["apprenant"=>$apprenant,"competence"=>$competence,"niveau 1"=>$niveau1,"niveau 2"=>$niveau2,"niveau 3"=>$niveau3];
                        }
                    }
                    return $this->json($tab, Response::HTTP_OK);
                }
            }

       }
    }
    /**
     * @Route(
     *   name="referentiel_competences",
     *   path="api/formateurs/promo/{idp}/referentiel/{idr}/statistiques/competences",
     *   methods={"GET"}
     * )
     */
    public function getCompetences(ReferentielRepository $repo,PromosRepository $promosRepository, $idp,$idr)
    {
        $promo = $promosRepository->find($idp);
        if ($promo) {
            $referentiel = $promo->getReferentiel();
            if ($referentiel->getId() == $idr) {
                $stats = $promo->getStatistiquesCompetences();
                $competences = $referentiel->getGroupeCompetences();
                $grpcs = $this->serializer->normalize($competences, 'json');
                $statTab = $this->serializer->normalize($stats, 'json');
                foreach ($grpcs as $grpc){
                    foreach ($grpc["competences"] as $competence) {
                        $nbre1= 0; $nbre2= 0; $nbre3= 0;
                        foreach ($statTab as $stat){
                            if ($stat["competence"]["id"] == $competence["id"]){
                                if ($stat["niveau1"] == true){
                                    $nbre1 += 1;
                                }
                                if ($stat["niveau2"] == true){
                                    $nbre2 += 1;
                                }
                                if ($stat["niveau3"] == true){
                                    $nbre3 += 1;
                                }
                            }
                        }
                        $tab[] = ["compentence"=>$competence,"niveau 1"=>$nbre1,"niveau 2"=>$nbre2,"niveau 3"=>$nbre3];
                    }
                }
                return $this->json($tab,200,[]);

            }
        }
    }

    /**
     * @Route(path="/api/apprenant/{id}/promo/{idp}/referentiel/{idr}/statistisques/briefs",
     *        name="apigetApprenantIdPromoIdReferentielIdStatistiquesBriefs",
     *        methods={"GET"}
     *)
     */

    public function getApprenantBriefs(ApprenantRepository $appRepo,$id,$idp,$idr){
        $apprenant = $appRepo->find($id);
        $groupes = $apprenant->getGroupes();
        foreach ($groupes as $groupe){
            if ($groupe->getPromos()->getId() == $idp){
                $briefs = $groupe->getBriefs();
                $nbreAssigne=0;$nreValid=0;$nbreNonValid=0;
                foreach ($briefs as $brief){
                    $AppBriefs = $brief->getPromoBriefApp();
                    foreach ($AppBriefs as $appbrief){
                        $statut = $appbrief->getStatut();
                        if ($statut === "valide"){
                            $nreValid +=1;
                        }elseif ($statut ==="non valide"){
                            $nbreNonValid +=1;
                        }else{
                            $nbreAssigne +=1;
                        }
                    }
                }
            }
            $tab [] =["Apprenant"=>$apprenant,"Valide"=>$nreValid,"Non Valide"=>$nbreNonValid,"Assigne"=>$nbreAssigne];
        }
        return $this->json($tab,200,[]);
    }
    /**
     * @Route(
     *   name="apprenant_competences",
     *   path="api/apprenant/{id}/promo/{idp}/referentiel/{idr}/competences",
     *   methods={"GET"}
     * )
     */

    public function getApprenantCompetencesStat(ApprenantRepository $repository,StatistiquesCompetencesRepository $statRepo, SerializerInterface $serializer,$id,$idp,$idr){
        $appenant = $repository->find($id);
        $appenantTab = $serializer->normalize($appenant,'json');
        if ($appenant){
            $stats = $statRepo->findBy(["apprenant"=>$id,"promos"=>$idp,"referentiel"=>$idr]);
            foreach ($stats as $stat){
                $competence = $stat->getCompetence();
                $niveaux = ["niveau 1"=>$stat->getNiveau1(),"niveau 2"=>$stat->getNiveau2(),"niveau 3"=>$stat->getNiveau3()];
                $tab [] = [$competence,$niveaux];

            }
            $result = [$appenant, $tab];
        }
        return $this->json($result,200,[]);
    }
    /**
     * @Route(path="/api/apprenants/livrablepartiels/{id}/commentaires",
     *        name="commentaires_livrables",
     *        methods={"GET"}
     *)
     */

    public function getCommentaireLivrable(LivrableRenduRepository $repository,$id){
        $apprenantID = $this->user->getId();
        $livrableRendu = $repository->findOneBy(array("apprenant"=>$apprenantID,"livrablePartiel"=>$id));
        if ($livrableRendu){
            $commentaire = $livrableRendu->getCommentaires();
            $commentTab = $this->serializer->normalize($commentaire,'json',["groups"=>"commentaire:read"]);
            return $this->json($commentTab,Response::HTTP_OK,[]);
        }else{
            return $this->json("Aucun commentaire", Response::HTTP_OK);
        }
    }
    //METHODES PUT
    /**
     * @Route(path="/api/formateurs/promo/{idp}/brief/{idb}/livrablepartiels",
     *        name="AddLivrablePartielorDelete",
     *        methods={"PUT"},
     *     defaults={
     *          "__controller"="App\Controller\LivrablePartielController::putDeletAppLiv",
     *          "__api_resource_class"=LivrablePartiels::class,
     *          "__api_item_operation_name"="put_or_delete_live"
     *     }
     *)
     */

    public function putLivrablePartiel(Request $request,LivrablePartielsRepository $partielsRepo,BriefRepository $briefRepository,NiveauRepository $niveauRepository,ApprenantRepository $appRepo,$idb, \Swift_Mailer $mailer){
        $livTab = json_decode($request->getContent(),true);
        if (!empty($livTab) && !empty($livTab["brief"])){
            $brief = $briefRepository->find($idb);
            foreach ($livTab["livrablePartiel"] as $liv){
                if (empty($liv["id"]) && $liv["action"]=="modif"){
                    $livrablePartiel = $partielsRepo->find($liv["id"]);
                    if ($livrablePartiel) {
                        $livrablePartiel
                            ->setLibelle($liv["libelle"])
                            ->setDescription($liv["description"])
                            ->setDateCreation(new \DateTime())
                            ->setDelai(new \DateTime($liv["delai"]))
                            ->setType($liv["type"]);
                    }
                    if (!empty($liv["niveaux"])){
                        foreach ($liv["niveaux"] as $val){
                            $niveau = $niveauRepository->find($val["id"]);
                            if ($niveau){
                                $livrablePartiel->addNiveau($niveau);
                            }

                        }
                    }
                    if (!empty($liv["apprenant"])){
                        foreach ($liv["apprenant"] as $email){
                            $apprenant = $appRepo->findOneBy(["email"=>$email["email"]]);
                            if ($apprenant){
                                $message = (new \Swift_Message("Modification d'un livrable Partiel"))
                                    ->setFrom("damanyelegrand@gmail.com")
                                    ->setTo($apprenant->getEmail())
                                    ->setBody("Bonjour ".$apprenant->getPrenom()." ".$apprenant->getNom()." le livrable a été modifié .\nBon courage.\nMerci.");
                                $mailer->send($message);
                            }
                        }
                    }
                }elseif (empty($liv["id"]) && $liv["action"]=="ajout"){
                    $livrablePartiel = new LivrablePartiels();
                    $livrablePartiel
                        ->setLibelle($liv["libelle"])
                        ->setDescription($liv["description"])
                        ->setDateCreation(new \DateTime())
                        ->setDelai(new \DateTime($liv["delai"]))
                        ->setType($liv["type"]);
                    if (!empty($liv["niveaux"])){
                        foreach ($liv["niveaux"] as $val){
                            $niveau = $niveauRepository->find($val["id"]);
                            if ($niveau){
                                $livrablePartiel->addNiveau($niveau);
                            }
                        }
                    }
                    if (!empty($liv["apprenant"])){
                        foreach ($liv["apprenant"] as $email){
                            $apprenant = $appRepo->findOneBy(["email"=>$email["email"]]);
                            if ($apprenant){
                                $message = (new \Swift_Message("Assignation de Livrable Partiel"))
                                    ->setFrom("damanyelegrand@gmail.com")
                                    ->setTo($apprenant->getEmail())
                                    ->setBody("Bonjour ".$apprenant->getPrenom()." ".$apprenant->getNom()." un nouveau livrable vous a été assigné .\nBon courage.\nMerci.");
                                $mailer->send($message);
                            }
                        }
                    }
                }
                elseif (isset($liv["id"]) && $liv["action"]=="delete"){
                    foreach ($brief->getPromoBriefs() as $promoBriefs){
                        foreach ($promoBriefs as $promoBrief){
                            if($promoBrief->getLivrablePartiels() == $liv["id"]){
                                $promoBrief->removeLivrablePartiel($liv);
                            }

                        }

                    }
                }

            }

        }
        $errors = $this->validator->validate($livrablePartiel);
        if (count($errors)){
            $errors = $this->serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $this->em->flush();
        return $this->json($message, Response::HTTP_OK);
    }
    /**
     * @Route(
     *     name="get_deux_it",
     *     path="/api/apprenants/{id}/livrablepartiels/{idl}",
     *     methods={"PUT"},
     *     defaults={
     *          "__controller"="App\Controller\LivrablePartielController::putAppLiv",
     *          "__api_resource_class"=LivrablePartiels::class,
     *          "__api_item_operation_name"="get_deux_it"
     *     }
     * )
     */

    public function putAppLiv(Request $request, ApprenantRepository $appRepo, LivrablePartielsRepository $liveRepo, int $id, int $idl)
    {
        $statut = json_decode($request->getContent(),true);
        $apprenant = $appRepo->findOneBY(["id" => $id]);
        $livrapartiel = $liveRepo->findOneBY(["id" => $idl]);
        if (!$apprenant) {
            return new JsonResponse("Cet Apprenant n'existe pas", Response::HTTP_BAD_REQUEST, [], true);
        }
        if (!$livrapartiel) {
            return new JsonResponse("Ce livrable partiel n'existe pas", Response::HTTP_BAD_REQUEST, [], true);

        }
        foreach ($apprenant->getLivrableRendus() as $livrableRendu) {
            if ($livrableRendu->getLivrablePartiel()->getId() == $idl){
                $livrableRendu->setStatut($statut["statut"]);
            }
        }
        $this->em->flush();
        return $this->json("Modification is done", Response::HTTP_OK);

    }
    /**
     * @Route(
     *     name="update_statut_by_apprenant",
     *     path="/api/apprenants/{id}/livrablepartiels/{idl}",
     *     methods={"PUT"},
     *     defaults={
     *          "__controller"="App\Controller\LivrablePartielController::putAppLiv",
     *          "__api_resource_class"=LivrablePartiels::class,
     *          "__api_item_operation_name"="update_apprenant_statut_livrable"
     *     }
     * )
     */

    public function updateSatutByApprenant(Request $request,EntityManagerInterface $manager, ApprenantRepository $appRepo, LivrablePartielsRepository $liveRepo, int $id, int $idl)
    {
        $statut = json_decode($request->getContent(),true);
        $apprenant = $appRepo->findOneBY(["id" => $id]);
        $livrapartiel = $liveRepo->findOneBY(["id" => $idl]);
        if (!$apprenant) {
            return new JsonResponse("Cet Apprenant n'existe pas", Response::HTTP_BAD_REQUEST, [], true);
        }
        if (!$livrapartiel) {
            return new JsonResponse("Ce livrable partiel n'existe pas", Response::HTTP_BAD_REQUEST, [], true);

        }
        foreach ($apprenant->getLivrableRendus() as $livrableRendu) {
            if ($livrableRendu->getLivrablePartiel()->getId() == $idl){
                $livrableRendu->setStatut($statut["statut"]);
            }
        }
        $manager->flush();
        return $this->json("Modification is done", Response::HTTP_OK);

    }
    /**
     * @Route(
     *     name="formateurAddComment",
     *     path="/api/formateurs/livrablepartiels/{id}/commentaires",
     *     methods={"POST"},
     *     defaults={
     *          "__controller"="App\Controller\LivrablePartielController::putAppLiv",
     *          "__api_resource_class"=LivrablePartiels::class,
     *          "__api_collection_operation_name"="formateur_add_comment"
     *     }
     * )
     */
    public function formateurAddComment(Request $request,LivrableRenduRepository $renduRepository){
        $commentTab = json_decode($request->getContent(),true);
        $livRendu = $renduRepository->find($commentTab["id"]);
        if ($livRendu){
            $tab = $commentTab["commentaire"];
            $pj = $request->files->get('piecejointe');
            $commentaire = new Commentaire();
            $commentaire
                ->setContent($tab["content"])
                ->setDate(new \DateTime())
                ->setLivrableRendu($livRendu)
                ->setFormateur($this->user);
            if ($pj){
                $pj = fopen($pj->getRealPath(),'rb');
                $commentaire->setPieceJointe($pj);
            }
            $this->em->persist($commentaire);
            $this->em->flush();
            return $this->json("Envoyé",Response::HTTP_OK);
        }
    }
    /**
     * @Route(
     *     name="apprenantAddComment",
     *     path="/api/apprenants/livrablepartiels/{id}/commentaires",
     *     methods={"POST"},
     *     defaults={
     *          "__controller"="App\Controller\LivrablePartielController::postComLiv",
     *          "__api_resource_class"=LivrablePartiels::class,
     *          "__api_collection_operation_name"="apprenant_add_comment"
     *     }
     * )
     */
    public function apprenantAddComment(Request $request,LivrableRenduRepository $repository){
        $commentTab = json_decode($request->getContent(),true);
        $livRendu = $repository->find($commentTab["id"]);
        if ($livRendu){
            $tab = $commentTab["commentaire"];
            $commentaire = new Commentaire();
            $pj = $request->files->get('piecejointe');
            $commentaire
                ->setContent($tab["content"])
                ->setDate(new \DateTime())
                ->setLivrableRendu($livRendu);
            if ($pj){
                $pj = fopen($pj->getRealPath(),'rb');
                $commentaire->setPieceJointe($pj);
            }
            $errors = $this->validator->validate($commentaire);
            if (count($errors)){
                $errors = $this->serializer->serialize($errors,"json");
                return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
            }
            $this->em->persist($commentaire);
            $this->em->flush();
            return $this->json($commentaire,Response::HTTP_OK,[],["groups"=>"commentaire:read"]);
        }
    }
}
