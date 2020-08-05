<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormateurController extends AbstractController
{
    /**
    * @Route(
    *   name="formateur_liste",
    *   path="api/formateurs",
    *   methods={"GET"},
    *   defaults={
    *       "_controller"="\app\FormateurController::getFormateurs",
    *       "_api_resource_class"=User::class,
    *       "_api_collection_operation_name"="get_formateurs"
    *   }
    * )
    */
    public function getFormateurs(UserRepository $repo)
    {
        $formateurs= $repo->findByProfil("formateur");
        return $this->json($formateurs, Response::HTTP_OK,);
    }

    /**
    * @Route(
    *   name="formateur",
    *   path="api/formateurs/{id}",
    *   methods={"GET"},
    *   defaults={
    *       "_controller"="\app\FormateurController::getFormateur",
    *       "_api_resource_class"=User::class,
    *       "_api_collection_operation_name"="get_formateur"
    *   }
    * )
    */
    public function getFormateur(UserRepository $repo, $id)
    {
        $formateur = $repo->findOneByProfil("formateur", $id);
        return $this->json($formateur, Response::HTTP_OK,);
    }
}
