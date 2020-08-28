<?php

namespace App\Controller;

use App\Entity\Niveau;
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
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LivrablePartielController extends AbstractController
{
    private $user;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }
    /**
     * @Route(
     *   name="promo_competences",
     *   path="api/formateurs/promo/{idp}/referentiel/{idr}/competences",
     *   methods={"GET"}
     * )
     */
    public function getApprenantCompetences(FormateurRepository $repo,SerializerInterface $serializer,PromosRepository $promosRepository, $idp,$idr)
    {
        //Manque collection Apprenant
       $promo = $promosRepository->find($idp);
       if($promo){
           $referentiel = $promo->getReferentiel();
            if ($referentiel->getId() == $idr){
                $groupeCompetence = $referentiel->getGroupeCompetences();
                $groupeCompetenceTab = $serializer->normalize($groupeCompetence,'json');
                foreach ($groupeCompetenceTab as $competence){
                    $competenceTab[] = $competence;
                }
                return $this->json($competenceTab, Response::HTTP_OK);
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
    public function getCompetences(ReferentielRepository $repo,SerializerInterface $serializer,PromosRepository $promosRepository, $idp,$idr)
    {
        $promo = $promosRepository->find($idp);
        if ($promo) {
            $referentiel = $promo->getReferentiel();
            if ($referentiel->getId() == $idr) {
                $stats = $promo->getStatistiquesCompetences();
                $competences = $referentiel->getGroupeCompetences();
                $grpcs = $serializer->normalize($competences, 'json');
                $statTab = $serializer->normalize($stats, 'json');
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

    public function getApprenantBriefs(ApprenantRepository $appRepo,$id,$idp,$idr,SerializerInterface $serializer){
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

    public function getCommentaireLivrable(LivrableRenduRepository $repository,SerializerInterface $serializer,$id){
        $apprenantID = $this->user->getId();
        $livrableRendu = $repository->findOneBy(array("apprenant"=>$apprenantID,"livrablePartiel"=>$id));
        if ($livrableRendu){
            $commentaire = $livrableRendu->getCommentaires();
            $commentTab = $serializer->normalize($commentaire,'json',["groups"=>"commentaire:read"]);
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

    public function putLivrablePartiel(Request $request,BriefRepository $briefRepository,EntityManagerInterface $manager,ApprenantRepository $appRepo,$idb,SerializerInterface $serializer,ValidatorInterface $validator, \Swift_Mailer $mailer){
        $livrablePartiel = new LivrablePartiels();
        $livTab = json_decode($request->getContent(),true);
        if (!empty($livTab) && !empty($livTab["brief"])){
            $brief = $briefRepository->find($idb);
            foreach ($livTab["livrablePartiel"] as $liv){
                if (!empty($liv["id"]) && !empty($liv["libelle"])){
                    $livrablePartiel
                        ->setLibelle($liv["libelle"])
                        ->setDescription($liv["description"])
                        ->setDateCreation(new \DateTime())
                        ->setDelai(new \DateTime($liv["delai"]))
                        ->setType($liv["type"]);
                    if (!empty($liv["niveaux"])){
                        foreach ($liv["niveaux"] as $val){
                            $niveau = new Niveau();
                            $niveau
                                ->setLibelle($val["libelle"])
                                ->setCritereEvaluation($val["critereEvaluation"])
                                ->setGroupeAction($val["groupeAction"]);

                        }
                        $livrablePartiel->addNiveau($niveau);
                    }
                    if (!empty($liv["apprenant"])){
                        foreach ($liv["apprenant"] as $email){
                            $apprenant = $appRepo->findOneBy(["email"=>$email["email"]]);
                            if ($apprenant){
                                $message = (new \Swift_Message("Admission Sonatel Academy"))
                                    ->setFrom("damanyelegrand@gmail.com")
                                    ->setTo($apprenant->getEmail())
                                    ->setBody("Bonjour ".$apprenant->getPrenom()." ".$apprenant->getNom()." un nouveau livrable vous a été assigné .\nBon courage.\nMerci.");
                                $mailer->send($message);
                            }
                        }
                    }

                }
                elseif (!empty($liv["id"]) && !isset($liv["libelle"])){
                    foreach ($brief->getPromoBriefs() as $promoBrief){
                        if($promoBrief->getLivrablePartiels()->getId() == $liv["id"]){
                            $promoBrief->removeLivrablePartiel($liv);
                        }

                    }
                }

            }

        }
        $errors = $validator->validate($livrablePartiel);
        if (count($errors)){
            $errors = $serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $manager->flush();
        return $this->json("done", Response::HTTP_OK);
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

    public function putAppLiv(Request $request,EntityManagerInterface $manager, ApprenantRepository $appRepo, LivrablePartielsRepository $liveRepo, int $id, int $idl)
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
}
