<?php

namespace App\Controller;

use App\Entity\GroupeCompetence;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

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
    public function createGrpCompetence(Request $req, DenormalizerInterface $denormalizer, EntityManagerInterface $em){
        $grpeCompetence = new GroupeCompetence;

        if(!$this->isGranted('CREATE', $grpeCompetence)){
            return $this->json([
                "message" => "Vous n'avez pas accès à cette ressource"
            ], Response::HTTP_FORBIDDEN);
        }
        
        $grc = json_decode($req->getContent(), true);
        if(empty($grc["competences"])){
            return $this->json([
                "message" => "Ajouter au moins une competence"
            ], Response::HTTP_UNAUTHORIZED);
        }
        $competences = $denormalizer->denormalize($grc["competences"], "App\Entity\Competence[]");
        foreach($competences as $cmpt){
            $grpeCompetence->addCompetence($cmpt);
        }
        $grpeCompetence
            ->setLibelle($grc["libelle"])
            ->setDescriptif($grc["descriptif"]);

        $em->persist($grpeCompetence);
        $em->flush();
        return $this->json($grpeCompetence, Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *      name="update_grpecompetence",
     *      path="/api/admin/grpecompetences/{id}",
     *      methods="PUT",
     *      defaults={
     *          "_controller"="\app\GroupeCompetenceController::updateGrpCompetence",
    *           "_api_resource_class"=GroupeCompetence::class,
    *           "_api_collection_operation_name"="update_grpecompetence"
     *      }
     * )
     */
    public function updateGrpCompetence(){
        
    }
}