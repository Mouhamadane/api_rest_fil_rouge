<?php

namespace App\Controller;

use App\Entity\Competence;
use App\Entity\GroupeCompetence;
use App\Repository\CompetenceRepository;
use App\Repository\GroupeCompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class GroupeCompetenceController extends AbstractController
{
    private $denormalizer;
    private $em;
    private $validator;
    private $serializer;
    private $normalizer;
    
    public function __construct(DenormalizerInterface $denormalizer, EntityManagerInterface $em, ValidatorInterface $validator, SerializerInterface $serializer, NormalizerInterface $normalizer)
    {
        $this->denormalizer = $denormalizer;
        $this->em = $em;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->normalizer = $normalizer;
    }

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
        $grpeCompetence = new GroupeCompetence;

        if(!$this->isGranted('CREATE', $grpeCompetence)){
            return $this->json([
                "message" => "Vous n'avez pas accès à cette ressource"
            ], Response::HTTP_FORBIDDEN);
        }
        
        $grc = json_decode($req->getContent(), true);
        if(!empty($grc["competences"])){
            $competences = $this->denormalizer->denormalize($grc["competences"], "App\Entity\Competence[]");
            foreach($competences as $cmpt){
                $grpeCompetence->addCompetence($cmpt);
            }
        }else{
            return $this->json(["message" => "Ajouter au moins une compétence"], Response::HTTP_BAD_REQUEST);
        }
        $grpeCompetence
            ->setLibelle($grc["libelle"])
            ->setDescriptif($grc["descriptif"]);

        $errors = $this->validator->validate($grpeCompetence);
        if (count($errors)){
            $errors = $this->serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }

        $this->em->persist($grpeCompetence);
        $this->em->flush();
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
    *           "_api_item_operation_name"="update_grpecompetence"
     *      }
     * )
     */
    public function updateGrpCompetence(Request $req, int $id, GroupeCompetenceRepository $repo, CompetenceRepository $repoC){
        $grpc = $repo->find($id);
        $grcTab = json_decode($req->getContent(), true);
        if(!empty($grcTab["updateCompetences"])){
            foreach($grcTab["updateCompetences"] as $cmpt){
                if(!empty($cmpt) && isset($cmpt["id"]) && isset($cmpt["libelle"])){
                    foreach($grpc->getCompetences() as $k => $comp){
                        if($comp->getId() == $cmpt["id"]){
                            $grpc->getCompetences()[$k]->setLibelle($cmpt["libelle"]);
                        }
                    }
                }elseif(!empty($cmpt) && !isset($cmpt["id"]) && isset($cmpt["libelle"])){
                    $competence = new Competence;
                    $competence->setLibelle($cmpt["libelle"]);
                    $grpc->addCompetence($competence);
                }elseif(!empty($cmpt) && isset($cmpt["id"]) && !isset($cmpt["libelle"])){
                    foreach($grpc->getCompetences() as $k => $comp){
                        if($comp->getId() == $cmpt["id"]){
                            $grpc->removeCompetence($cmpt);
                        }
                    }
                }
            }
        }
        $errors = $this->validator->validate($grpc);
        if (count($errors)){
            $errors = $this->serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $this->em->flush();
        return $this->json($grpc, Response::HTTP_OK);
        // dd($grpc);
    }
}