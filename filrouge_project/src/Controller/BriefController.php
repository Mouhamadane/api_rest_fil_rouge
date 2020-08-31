<?php

namespace App\Controller;

use App\Entity\Brief;
use App\Entity\Promos;
use App\Repository\BriefRepository;
use App\Repository\FormateurRepository;
use App\Repository\PromosRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BriefController extends AbstractController
{
  /**
     * @Route(
     *     path="api/formateurs/promos/{id}/briefs",
     *     methods={"GET"},
     *     name=" promoBriefs"
     * )
    */
public function promoBriefs(PromosRepository $promoRepo,$id,BriefRepository $briefrepo){
    $promos=$promoRepo->findOneBy(['id'=>$id]);
   $briefs=[];
    if($promos){
       
        foreach($promos->getPromoBrief() as $promoBrief){
            $briefs[]=$briefrepo->find($promoBrief->getBrief()->getId());
          
        }
 return $this->json($briefs, Response::HTTP_OK,[],['groups'=>['promo_brief:read']]);

    }
    return $this->json("promo introuvable", Response::HTTP_NOT_FOUND);

      }
       /**
     * @Route(
     *     path="api/formateurs/{id}/briefs/brouillons",
     *     methods={"GET"},
     *     name=" briefbrouillons"
     * )
    */
    public function briefbrouillons( $id,BriefRepository $briefrepo,FormateurRepository $formateurrepo)
    {
        $formateur=$formateurrepo->find($id);
        $briefs=[];
        if($formateur){
            if($formateur==$this->get('security.token_storage')->getToken()->getUser()){
                foreach($formateur->getBriefs() as $brief){
                    if($brief->getStatut()=='brouillon'){
                        $briefs[]=$brief; 
                      
                    } 
                }
                return $this->json($briefs, Response::HTTP_OK,['groups'=>['briefbrouillons:read']]);
            }
            return $this->json("vous navez pas acces a cette ressource", Response::HTTP_NOT_FOUND);
        }  
    }
      /**
     * @Route(
     *     path="api/formateurs/{id}/briefs/valide",
     *     methods={"GET"},
     *     name=" briefvalide"
     * )
    */
    public function briefvalide( $id,BriefRepository $briefrepo,FormateurRepository $formateurrepo)
    {
        $formateur=$formateurrepo->findOneBy(['id'=>$id]);
        $brief=new Brief();
        $briefs=[];
        if($formateur){
            if($formateur==$this->get('security.token_storage')->getToken()->getUser()){
                foreach($formateur->getBriefs() as $brief){
                    if($brief->getStatut()=='valide'){
                        $briefs[]=$brief;  
                    }
                }
                return $this->json($briefs, Response::HTTP_OK,['groups'=>['briefbrouillons:read']]);
            }
            return $this->json("inexistante", Response::HTTP_NOT_FOUND);
        }  
    }
     /**
     * @Route(
     *     path="api/apprenants/promos/{id}/briefs",
     *     methods={"GET"},
     *     name="briefapprenant"
     * )
    */
    public function briefapprenant( $id,PromosRepository $promoRepo)
    {
        $promo=$promoRepo->findOneBy(['id'=>$id]);
        if($promo){
            $briefassigne=[];
            $briefs=$promo->getPromoBrief();
            foreach($briefs as$promobrief ){
                if($promobrief->getStatut()=='assigne'){

                    $brief=$promobrief ->getBrief();
                    $briefassigne[]=$brief;
                }                                        
            }
            return $this->json($briefassigne, Response::HTTP_OK,['groups'=>['promo_brief:read']]);
        } return $this->json("inexistante", Response::HTTP_NOT_FOUND);
    }

    
      /**
     * @Route(
     *     path="api/formateurs/promos/{idp}/briefs/{idb}",
     *     methods={"GET"},
     *     name=" brieformateur"
     * )
    */
    public function brieformateur ($idp,$idb,BriefRepository $briefrepo,PromosRepository $promorepo)
    {
        $promo=$promorepo->findOneBy(["id"=>$idp]);
        $brief=new Brief();
        if($promo){
            $brief=$briefrepo->findOneBy(["id"=>$idb]);
            if($brief){
                $promoBrief=$promo->getPromoBrief();
                foreach ($promoBrief as $pb) {

                    if($pb->getBrief()==$brief ){
                        return $this->json($brief, Response::HTTP_OK,[],['groups'=>['briefbrouillons:read']]);
                    }
                
                }
            }   
        }     return $this->json("vous navez pas acces a cette ressource", Response::HTTP_NOT_FOUND);

    }
          
}
      
    
    
