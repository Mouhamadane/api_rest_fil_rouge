<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Apprenant;
use App\Repository\ApprenantRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApprenantController extends AbstractController
{
    /**
    * @Route(
    *   name="apprenant_liste",
    *   path="api/apprenants",
    *   methods={"GET"},
    *   defaults={
    *       "_controller"="\app\ApprenantController::getApprenants",
    *       "_api_resource_class"=Apprenant::class,
    *       "_api_collection_operation_name"="get_apprenants"
    *   }
    * )
    */
    public function getApprenants(ApprenantRepository $repo)
    {
        $apprenants= $repo->findAll();
        return $this->json($apprenants,Response::HTTP_OK,);
    }

    /**
    * @Route(
    *   name="apprenant",
    *   path="api/apprenants/{id}",
    *   methods={"GET"},
    *   defaults={
    *       "_controller"="\app\ApprenantController::getApprenant",
    *       "_api_resource_class"=Apprenant::class,
    *       "_api_collection_operation_name"="get_apprenant"
    *   }
    * )
    */
    public function getApprenant(ApprenantRepository $repo, $id)
    {
        $apprenant = $repo->findOneByProfil("apprenant", $id);
        return $this->json($apprenant, Response::HTTP_OK,);
    }
}
