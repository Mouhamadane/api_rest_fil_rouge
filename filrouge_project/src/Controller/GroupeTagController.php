<?php

namespace App\Controller;

use App\Entity\GroupeTag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class GroupeTagController extends AbstractController
{
    /**
     * @Route(
     *      name="create_grptag",
     *      path="api/admin/grptags",
     *      methods="POST",
     *      defaults={
     *          "_controller"="\app\GroupeTagController::createGrpTag",
    *           "_api_resource_class"=GroupeTag::class,
    *           "_api_collection_operation_name"="add_grptag"
     *      }
     * )
     */
    public function createGrpTag(Request $req, DenormalizerInterface $denormalizer, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em){
        $grptagsTab = json_decode($req->getContent(), true);
        $tags = $denormalizer->denormalize($grptagsTab["tags"], "App\Entity\Tag[]");
        $groupetag = new GroupeTag;
        $groupetag->setLibelle($grptagsTab['libelle']);
        foreach($tags as $tag){
            $groupetag->addTag($tag);
        }
        $errors = $validator->validate($groupetag);
        if (count($errors)){
            $errors = $serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $em->persist($groupetag);
        $em->flush();
        return $this->json($groupetag, Response::HTTP_CREATED);
    }
}