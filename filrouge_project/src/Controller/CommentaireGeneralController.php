<?php

namespace App\Controller;

use DateTime;
use App\Entity\FilDeDiscussion;
use App\Entity\CommentaireGeneral;
use App\Repository\PromosRepository;
use App\Repository\ApprenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FilDeDiscussionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentaireGeneralRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CommentaireGeneralController extends AbstractController
{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route(
     *      name="getCommentaire",
     *      path="api/users/promo/{idp}/apprenant/{ida}/chats/{date}",
     *      methods="GET",
     *     
     * )
     */
    public function getCommentaire(int $ida, int $idp, $date, promosRepository $repopromo, CommentaireGeneralRepository $commentaireRep, FilDeDiscussionRepository $repofilD, serializerInterface $serialize)
    {
        $commentaire = $commentaireRep->findChatByApprenanAndPromo($idp, $ida);
        return $this->json($commentaire, Response::HTTP_OK, [], ['groups' => ['chats:read']]);
    }
    /**
     * @Route(
     *      name="AddCommentaire",
     *      path="api/users/promo/{idp}/apprenant/{ida}/chats",
     *      methods="POST",
     *      defaults={
     *           "__controller"="\App\CController\CommentaireGeneralController::AddCommentaire",
     *           "__api_resource_class"=CommentaireGeneral::class,
     *           "__api_collection_operation_name"="AddCommentaire"
     *      }
     * )
     */
    public function AddCommentaire(int $idp, int $ida, Request $req, promosRepository $repopromo, filDeDiscussionRepository $filDeDiscussionrepo, ApprenantRepository $repoapp, SerializerInterface $serialise)
    {
        $promo = null;
        if (!$promo = $repopromo->find($idp)) {
            return $this->json("Attention verifiez l'id:$idp de la promotion!!!", Response::HTTP_NOT_FOUND);
        }
        if (!$apprenant = $repoapp->find($ida)) {
            return $this->json("Attention verifiez l'id:$idp de lapprenant!!", Response::HTTP_NOT_FOUND);
        }
        $filDiscution = $promo->getFilDeDiscussion();
        if ($filDiscution->getId() == 0) {
            $filDiscution = new FilDeDiscussion();
            $filDiscution
                ->setTitre('Discution promo ' . $promo->getId())
                ->setDate(new \DateTime())
                ->setPromo($promo);
            $this->em->persist($filDiscution);
        }
        $commentaireData = $req->request->all();
        $pieceJointe = $req->files->get('pieceJointe');
        $commentObject = $serialise->denormalize($commentaireData, "App\Entity\CommentaireGeneral", true);
        $commentObject
            ->setDate(new \DateTime())
            ->setUser($apprenant);
        if ($pieceJointe) {
            $pieceJointe = fopen($pieceJointe->getRealPath(), 'rb');
            $commentObject->setPieceJointe($pieceJointe);
        }
        $filDiscution->addCommentaireGeneral($commentObject);
        $this->em->persist($commentObject);
        $this->em->flush();
        return $this->json("succes", 200);
    }
}
