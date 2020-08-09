<?php

namespace App\Controller;

use App\Entity\Referentiel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GroupeCompetenceRepository;
use App\Repository\ReferentielRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReferentielController extends AbstractController
{

    private $em;
    private $validator;
    private $serializer;
    
    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }
    
    /**
     * @Route(
     *      name="add_referentiel",
     *      path="api/admin/referentiels",
     *      methods="POST",
     *      defaults={
     *          "_controller"="\app\ReferentielController::createReferentiel",
    *           "_api_resource_class"=Referentiel::class,
    *           "_api_collection_operation_name"="add_referentiels"
     *      }
     * )
     */
    public function createReferentiel(Request $req, GroupeCompetenceRepository $repoGrc){
        $referentiel = new Referentiel;
        $refTab = json_decode($req->getContent(), true);
        
        if(!empty($refTab["grpeCompetences"])){
            foreach($refTab["grpeCompetences"] as $id){
                $grc = $repoGrc->find($id);
                if($grc){
                    $referentiel->addGroupeCompetence($grc);
                }
            }
        }else{
            return $this->json(["message" => "Ajouter au moins un groupe compétence"], Response::HTTP_BAD_REQUEST);
        }

        $referentiel
            ->setLibelle($refTab['libelle'])
            ->setPresentation($refTab['presentation'])
            ->setCritereEvaluation($refTab['critereEveluation'])
            ->setCritereAdmission($refTab['critereAdmission']);

        $errors = $this->validator->validate($referentiel);
        if (count($errors)){
            $errors = $this->serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }

        $this->em->persist($referentiel);
        $this->em->flush();
        return $this->json($referentiel, Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *      name="update_referentiel",
     *      path="api/admin/referentiels/{id}",
     *      methods="PUT",
     *      defaults={
     *          "_controller"="\app\ReferentielController::updateReferentiel",
    *           "_api_resource_class"=Referentiel::class,
    *           "_api_item_operation_name"="update_referentiel"
     *      }
     * )
     */
    public function updateReferentiel(Request $req, int $id, GroupeCompetenceRepository $repoGrc, ReferentielRepository $repoRef){
        $referentiel = $repoRef->find($id);
        $refTab = json_decode($req->getContent(), true);

        if(!empty($refTab["grpeCompetences"])){
            foreach($refTab["grpeCompetences"] as $id){
                $trouve = false;
                foreach($referentiel->getGroupeCompetences() as $grpc){
                    if($grpc->getId() == $id){
                        $trouve = true;
                        $referentiel->removeGroupeCompetence($grpc);
                    }
                }
                if(!$trouve){
                    $grpc = $repoGrc->find($id);
                    if($grpc){
                        $referentiel->addGroupeCompetence($grpc);
                    }
                }
            }
            $this->em->flush();
        }
        return $this->json(["success"], Response::HTTP_OK);
        dd($referentiel);
    }

}