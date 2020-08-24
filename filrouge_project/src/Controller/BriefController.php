<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Entity\Promos;
use App\Entity\Groupes;
use App\Entity\Apprenant;
use App\Entity\Referentiel;
use App\Repository\ApprenantRepository;
use App\Repository\BriefRepository;
use App\Repository\FormateurRepository;
use App\Repository\PromosRepository;
use App\Repository\ReferentielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class BriefController extends AbstractController
{
/**
* @Route(
*       name="get_brief_formateur_promos",
*       path="formateurs/promos/{id}/briefs",
*       methods={"GET"},
*            defaults={
*                   "_controller"="\App\Controller\BriefController::promo_briefs",
*                   "_api_resource_class"=Brief::class,
*                   "_api_collection_operation_name"="get_brief_formateur_promos"
*                       }  
* )  
* 
*/    
public function promo_briefs(PromosRepository $repo,$id,$brf){
    if(!$promos=$repo->find($id)){
        return $this->json("promo introuvable", Response::HTTP_NOT_FOUND);
    }
    foreach($promos->getGroupes()as $key=>$groupe){
        if($groupe->gettype()=='principal'){
            foreach($groupe->getFormateur()as $formateur){
                if($formateur==$this->get('security.token_storage')->getToken()->getUser()){
                    foreach($promos->getPromoBrief() as $key=>$sembene){
                        if($sembene->getBrief()->getFormateur()==$formateur){
                            $briefs[]=$sembene ->getBrief();
                        }
                    }
                }

                return $this->json($briefs, Response::HTTP_OK );

            }

        }
    }
}

}
