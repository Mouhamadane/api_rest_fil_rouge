<?php

namespace App\Controller;

use App\Entity\Competence;
use App\Entity\Niveau;
use App\Repository\CompetenceRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GroupeCompetenceRepository;
use App\Repository\NiveauRepository;
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
    private $denormalizer;
    private $em;
    private $validator;
    private $serializer;
    
    public function __construct(DenormalizerInterface $denormalizer, EntityManagerInterface $em, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->denormalizer = $denormalizer;
        $this->em = $em;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

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
    public function createCompetence(Request $req, GroupeCompetenceRepository $repo){
        $competence = new Competence;
        $competenceTab = json_decode($req->getContent(), true);
        if(!empty($competenceTab["niveaux"]) && count($competenceTab["niveaux"]) < 4){
            $niveaux = $this->denormalizer->denormalize($competenceTab["niveaux"], "App\Entity\Niveau[]");
            foreach($niveaux as $niveau){
                $competence->addNiveau($niveau);
            }
        }elseif(count($competenceTab["niveaux"]) >= 4){
            return $this->json(["message" => "Ajouter au max trois niveaux"], Response::HTTP_BAD_REQUEST);
        }else{
            return $this->json(["message" => "Ajouter au moins un niveau"], Response::HTTP_BAD_REQUEST);
        }
        $competence->setLibelle($competenceTab["libelle"]);
        $errors = $this->validator->validate($competence);
        if (count($errors)){
            $errors = $this->serializer->serialize($errors,"json");
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
                $this->em->persist($grpcompetence);
            }
        }else{
            return $this->json(["message" => "Associer au moins à un groupe de compétences"], Response::HTTP_BAD_REQUEST);
        }
        $this->em->persist($competence);
        $this->em->flush();
        return $this->json($competence, Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *      name="update_competence",
     *      path="api/admin/competences/{id}",
     *      methods="PUT",
     *      defaults={
     *          "_controller"="\app\CompetenceController::updateCompetence",
    *           "_api_resource_class"=Competence::class,
    *           "_api_item_operation_name"="update_competence"
     *      }
     * )
     */
    public function updateCompetence(Request $req, int $id, CompetenceRepository $repo, NiveauRepository $repoN){
        $competence = $repo->find($id);
        $comptTab = json_decode($req->getContent(), true);
        if(!empty($comptTab["updateNiveaux"])){
            foreach($comptTab["updateNiveaux"] as $niveaux){
                if(!empty($niveaux) && isset($niveaux["id"]) && isset($niveaux["libelle"]) && isset($niveaux["groupeAction"]) && isset($niveaux["critereEvaluation"])){
                    foreach($competence->getNiveaux() as $k => $comp){
                        if($comp->getId() == $niveaux["id"]){
                            $competence->getNiveaux()[$k]
                                ->setLibelle($niveaux["libelle"])
                                ->setGroupeAction($niveaux["groupeAction"])
                                ->setCritereEvaluation($niveaux["critereEvaluation"]);
                        }
                    }
                }elseif(!empty($niveaux) && !isset($niveaux["id"]) && isset($niveaux["groupeAction"]) && isset($niveaux["critereEvaluation"]) && count($competence->getNiveaux()) < 3){
                    $niveau = new Niveau;
                    $niveau
                        ->setLibelle($niveaux["libelle"])
                        ->setGroupeAction($niveaux["groupeAction"])
                        ->setCritereEvaluation($niveaux["critereEvaluation"]);
                    $competence->addNiveau($niveau);
                }elseif(!empty($niveaux) && isset($niveaux["id"]) && !isset($niveaux["libelle"])){
                    foreach($competence->getNiveaux() as $k => $niv){
                        if($niv->getId() == $niveaux["id"]){
                            $competence->getNiveaux()[$k]->setIsDeleted(true);
                        }
                    }
                }
            }
        }
        $errors = $this->validator->validate($competence);
        if (count($errors)){
            $errors = $this->serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $this->em->flush();
        return $this->json($competence, Response::HTTP_OK);
        // dd($competence);
    }
}