<?php

namespace App\Controller;

use App\Repository\ApprenantRepository;
use App\Repository\BriefRepository;
use App\Repository\FormateurRepository;
use App\Repository\PromosRepository;
use App\Repository\ReferentielRepository;
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
     *@Route(
     *      path="api/apprenants/livrablepartiels/{id}/commentaires",
     *      name="apprenant_add_comment",
     *      methods="GET",
     *      defaults={
     *          "_controller"="\app\LivrablePartielController::addApprenantCommentaires",
     *           "_api_resource_class"=LivrablePartiels::class,
     *           "_api_collection_operation_name"="add_apprenant_commentaires"
     *      }
     * )
     */
    public function addApprenantCommentaires(Request $request)
    {
        $commentTab = json_decode($request->getContent(),true);
        dd($commentTab);
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
       $promo = $promosRepository->find($idp);
       $promoTab = $serializer->normalize($promo, 'json');
       foreach ($promoTab['groupes'] as $groupe){
           dd($groupe);

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
        $grpc = $promo->getReferentiel()->getGroupeCompetences();
        $grpc = $serializer->normalize($grpc,'json');
        foreach ($grpc[0]['competences'] as $competence){
            $compentenceTab[] = $competence;
        }
        return $this->json($compentenceTab, Response::HTTP_OK);

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

    public function getLivrablePartiel(PromosRepository $promoRepo,BriefRepository $briefRepo,SerializerInterface $serializer,$idp,$idb){
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
     * @Route(path="/api/apprenant/{id1}/promo/{id2}/referentiel/{id3}/statistisques/briefs",
     *        name="apigetApprenantIdPromoIdReferentielIdStatistiquesBriefs",
     *        methods={"GET"}
     *)
     */

    public function getApprenantBriefs(ApprenantRepository $appRepo,$id1,$id2,$id3,SerializerInterface $serializer,PromoRepository $promoRepo,ReferentielRepository $refeRepo,GroupeApprenantRepository $grpAppRepo){
        $apprenant=$appRepo->find($id1);
        $email_apprenant=$apprenant->getEmail();
        $promo=$promoRepo->find($id2);
        $grp_appr=$promo->getGroupeApprenants();
        $grp_appr=$serializer->normalize($grp_appr,'json');
        $apprenants=$grp_appr[0]["apprenants"];
        foreach ($apprenants as $key_apprenants => $apprenant) {
            if ($apprenant["email"]==$email_apprenant) {
                $promo=$serializer->normalize($promo,'json');
                if ($promo["referentiel"]["id"]==$id3) {
                    $promo=$promo["groupeApprenants"];
                    foreach ($promo as $key_groupe_apprenant => $groupes) {
                        $apprenants=$groupes["apprenants"];
                        foreach ($apprenants as $key_apprenants => $apprenant) {
                            if ($apprenant["email"]==$email_apprenant) {
                                $promo_briefs=$apprenant["promoBriefApprenant"];
                                dd($promo_briefs);
                                /*foreach ($promo_briefs as $key_promo_briefs => $brief) {
                                    dd($brief);
                                    if ($brief=="assigne") {
                                        # code...
                                    }
                                }*/
                            }
                        }

                    }
                }

            }
        }
        return $this->json(["message" => "Cet apprenant n'est pas dans cette promo."], Response::HTTP_FORBIDDEN);

    }
    /**
     * @Route(
     *   name="apprenant_competences",
     *   path="api/apprenant/{id}/promo/{idp}/referentiel/{idr}/competences",
     *   methods={"GET"}
     * )
     */

    public function getReferentielIdCompetences(PromosRepository $promoRepo,ApprenantRepository $apprenantRepository,SerializerInterface $serializer,$id,$idp,$idr){
        $apprenant = $apprenantRepository->find($id);
        $emailApprenant = $apprenant->getId();
        $promo = $promoRepo->find($idp);
        $grpApp = $promo->getGroupes();
        $grpApp = $serializer->normalize($grpApp,'json');
        $apprenants = $grpApp[0]["apprenants"];
        foreach ($apprenants as $apprenant) {
            if ($apprenant["id"] == $emailApprenant) {
                $groupeCompetences=$promo->getReferentiel()->getGroupeCompetences();
                $groupeCompetences=$serializer->normalize($groupeCompetences,'json');
                foreach ($groupeCompetences as $competences) {
                    $competence = $competences["competences"];
                    foreach ($competence as $champCompetence) {
                        if(count($champCompetence["niveau"]) == 3){
                            $competencesTab[] = $competence;
                        }
                    }
                }
                if(empty($competencesTab)){
                    return $this->json('Aucune competence',Response::HTTP_OK);
                }
                return $this->json($competencesTab,Response::HTTP_OK);
            }
        }
    }
}
