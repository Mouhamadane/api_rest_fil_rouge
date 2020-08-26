<?php

namespace App\Controller;

use App\Repository\ApprenantRepository;
use App\Repository\BriefRepository;
use App\Repository\FormateurRepository;
use App\Repository\PromosRepository;
use App\Repository\ReferentielRepository;
use App\Repository\StatistiquesCompetencesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LivrablePartielController extends AbstractController
{
    /**
     * @Route(
     *   name="promo_competences",
     *   path="api/formateurs/promo/{idp}/referentiel/{idr}/competences",
     *   methods={"GET"}
     * )
     */
    public function getApprenantCompetences(FormateurRepository $repo,SerializerInterface $serializer,PromosRepository $promosRepository, $idp,$idr)
    {
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
     * @Route(path="/api/formateurs/promo/{id1}/brief/{id2}/livrablepartiels",
     *        name="apigetFormateurPromoIdBriefIdLivablespartiels",
     *        methods={"GET"}
     *)
     */

    public function getBriefLivablesPartiels(PromosRepository $promoRepo,BriefRepository $briefRepo,SerializerInterface $serializer,$id1,$id2){

        $promo=$promoRepo->find($id1);
        if ($promo) {
            $promo_briefs=$promo->getPromos();
            dd($promo_briefs);
            $promo_briefs=$serializer->normalize($promo_briefs,'json');
            foreach ($promo_briefs as $key_promo_brief => $promo_brief) {
                dd($promo_brief);
                if ($promo_brief["brief"]) {
                    # code...
                }
            }
        }
    }
    /**
     * @Route(path="/api/formateurs/promo/{idp}/brief/{idb}/livrablepartiels",
     *        name="apigetFormateurPromoIdBriefIdLivablespartiels",
     *        methods={"GET"}
     *)
     */

    public function getLivrablePartiel(PromosRepository $promoRepo,SerializerInterface $serializer,$idp,$idb){
        $promo =$promoRepo->find($idp);
        if ($promo) {
            $promoBriefs = $promo->getPromosbrief();
            foreach ($promoBriefs as $promoBrief) {
                if ($promoBrief->getBrief()->getId() == $idb) {
                    $livrablesPartiels = $promoBrief->getLivrablePartiels();
                    $livrablesPartielTab = $serializer->normalize($livrablesPartiels,'json');
                    dd($livrablesPartielTab);
                    return $this->json($livrablesPartiels,Response::HTTP_OK);
                }
            }
            return $this->json("Aucun librable partiel trouvé",Response::HTTP_BAD_REQUEST);
        }
    }
    /**
     * @Route(path="/api/apprenant/{id}/promo/{idp}/referentiel/{idr}/statistisques/briefs",
     *        name="apigetApprenantIdPromoIdReferentielIdStatistiquesBriefs",
     *        methods={"GET"}
     *)
     */

    public function getApprenantBriefs(ApprenantRepository $appRepo,$id,$idp,$idr,SerializerInterface $serializer){
        $appenant = $appRepo->find($id);
        $grpApp = $appenant->getGroupes();
        $grpAppTab = $serializer->normalize($grpApp,'json',["groups"=>"livrable:briefs:read"]);
        dd($grpAppTab);
    }
    /**
     * @Route(
     *   name="apprenant_competences",
     *   path="api/apprenant/{id}/promo/{idp}/referentiel/{idr}/competences",
     *   methods={"GET"}
     * )
     */

    public function getReferentielIdCompetences(StatistiquesCompetencesRepository $statRepo, SerializerInterface $serializer,$id,$idp,$idr){
        $stats = $statRepo->findAll();
        foreach ($stats as $stat){
            $promo = $stat->getPromos();
            $apprenant = $stat->getApprenant();
            $referentiel = $promo->getReferentiel();
            if(($promo->getId() == $idp) && ($apprenant->getId() == $id) && ($referentiel->getId()==$idr)){
                $competence = $stat->getCompetence();
                $competenceTab = $serializer->normalize($competence,'json',["groups"=>"competence:read"]);
                $tab[] = ["competence"=>$competenceTab];
            }
        }
        if (empty($tab)){
            return $this->json("Aucune competence trouvee",203,[]);
        }
        return $this->json($tab,200,[]);
    }
}
