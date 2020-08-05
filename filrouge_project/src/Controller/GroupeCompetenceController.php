<?php

namespace App\Controller;

use App\Entity\GroupeCompetence;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class GroupeCompetenceController extends AbstractController
{
    /**
     * @Route(
     *      name="add_grpecompetence",
     *      path="/api/admin/grpecompetences",
     *      methods="POST",
     *      defaults={
     *          "_controller"="\app\GroupeCompetenceController::createGrpCompetence",
    *           "_api_resource_class"=GroupeCompetence::class,
    *           "_api_collection_operation_name"="add_grpecompetence"
     *      }
     * )
     */
    public function createGrpCompetence(Request $req){
        $grc = $req->getContent();
        dd($grc);
    }
}