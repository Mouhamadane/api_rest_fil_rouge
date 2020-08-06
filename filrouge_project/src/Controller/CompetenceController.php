<?php

namespace App\Controller;

use App\Entity\Competence;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GroupeCompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CompetenceController extends AbstractController
{
    /**
     * @Route(
     *      name="add_competence",
     *      path="api/admin/competences",
     *      methods="POST",
     *      defaults={
     *          "_controller"="\app\CompetenceController::createCompetence",
    *           "_api_resource_class"=Competence::class,
    *           "_api_collection_operation_name"="add_competence"
     *      }
     * )
     */
    public function createCompetence(Request $req, DenormalizerInterface $denormalizer, SerializerInterface $serializer, ValidatorInterface $validator, GroupeCompetenceRepository $repo, EntityManagerInterface $em){
        $competence = new Competence;
        $competenceTab = json_decode($req->getContent(), true);
        if(!empty($competenceTab["niveaux"]) && count($competenceTab["niveaux"]) < 4){
            $niveaux = $denormalizer->denormalize($competenceTab["niveaux"], "App\Entity\Niveau[]");
            foreach($niveaux as $niveau){
                $competence->addNiveau($niveau);
            }
        }elseif(count($competenceTab["niveaux"]) >= 4){
            return $this->json(["message" => "Ajouter au max trois niveaux"], Response::HTTP_BAD_REQUEST);
        }else{
            return $this->json(["message" => "Ajouter au moins un niveau"], Response::HTTP_BAD_REQUEST);
        }
        $competence->setLibelle($competenceTab["libelle"]);
        $errors = $validator->validate($competence);
        if (count($errors)){
            $errors = $serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }

        if(!empty($competenceTab["groupeCompetences"])){
            foreach($competenceTab["groupeCompetences"] as $libelleGroupecmpt){
                $grpcompetence = $repo->findOneBy(["libelle" => $libelleGroupecmpt]);
                if($grpcompetence){
                    $grpcompetence->addCompetence($competence);
                }else{
                    return $this->json(["message" => $libelleGroupecmpt." n'existe pas!"], Response::HTTP_BAD_REQUEST);
                }
                $em->persist($grpcompetence);
            }
        }else{
            return $this->json(["message" => "Associer au moins à un groupe de compétences"], Response::HTTP_BAD_REQUEST);
        }
        $em->persist($competence);
        $em->flush();
        return $this->json($competence, Response::HTTP_CREATED);
    }
}