<?php

namespace App\Controller;

use App\Entity\CommentaireGeneral;
use App\Repository\PromosRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentaireGeneralRepository;
use App\Repository\FilDeDiscussionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CommentaireGeneralController extends AbstractController
{
    /**
     * @Route(
     *      name="getCommentaire",
     *      path="api\users/promo/{idp}/apprenant/{ida}/chats/date",
     *      methods="GET",
     *      defaults={
     *           "_controller"="\app\CommentaireGeneralController::getCommentaire",
     *           "_api_resource_class"=CommentaireGeneral::class,
     *           "_api_collection_operation_name"="getCommentaire"
     *      }
     * )
     */
    public function getCommentaire(int $ida, int $idp,promosRepository $repopromo, CommentaireGeneralRepository $commentaireRep, FilDeDiscussionRepository $repofilD)
    {
                   $commentaire = $commentaireRep->findChatByApprenanAndPromo($idp,$ida);
       
       

        
    }
     /**
     * @Route(
     *      name="AddCommentaire",
     *      path="users/promo/{id}/apprenant/{ida}/chats",
     *      methods="POST",
     *      defaults={
     *           "_controller"="\app\CommentaireGeneralController::AddCommentaire",
     *           "_api_resource_class"=CommentaireGeneral::class,
     *           "_api_collection_operation_name"="AddCommentaire"
     *      }
     * )
     */
    public function AddCommentaire(int $ida,Request $req,promosRepository $repopromo)
    {
        if(!$promo = $repopromo->find($ida))
        { 
            return $this->json("Attention erreur !!!", Response::HTTP_NOT_FOUND);
        }

    }
}
