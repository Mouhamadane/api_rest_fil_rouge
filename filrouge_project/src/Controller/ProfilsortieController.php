<?php

namespace App\Controller;

use App\Entity\Groupes;
use App\Entity\ProfilSortie;
use App\Entity\Promos;
use App\Repository\ProfilSortieRepository;
use App\Repository\PromosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProfilsortieController extends AbstractController
{
    private $normalizer;
    
    public function __construct(normalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }
    
    /**
     * @Route(
     *      name="add_profilsortie",
     *      path="api/admin/profilsorties",
     *      methods="POST",
     *      defaults={
     *          "_controller"="\app\ProfilsortieController::Addprofilsortie",
    *           "_api_resource_class"=ProfilSortie::class,
    *           "_api_collection_operation_name"="add_profilsortie"
     *      }
     * )
     */
    public function Addprofilsortie(Request $reqprofil,SerializerInterface $serializer, EntityManagerInterface $em)
    {

        $jsonRecu = $reqprofil->getContent();
        
        $profilsortie= $serializer->deserialize($jsonRecu, ProfilSortie::class, 'json');
        
        //dd(gettype($profilsortie));
        $em->persist($profilsortie);

        $em->flush();
        
        return new JsonResponse("success",Response::HTTP_CREATED,[],true);

    }

    /**
     * @Route(
     *      name="ShowpromoProfilsortie",
     *      path="api/admin/promos/{id}/profilsorties",
     *      methods="GET",
     *      defaults={
     *          "_controller"="app\ProfilsortieController::ShowpromoProfilsortie",
    *           "_api_resource_class"=ProfilSortie::class,
    *           "_api_collection_operation_name"="ShowpromoProfilsortie"
     *      }
     * )
     */
    public function ShowpromoProfilsortie(PromosRepository $repopromo,int $id, profilSortieRepository $profilsortierepo)
    {
        if(!$promo = $repopromo->find($id))
        { 
            return $this->json("error", Response::HTTP_NOT_FOUND);
        }
                 $profilsorties=$profilsortierepo->findAll();

        foreach ($profilsorties as $profilsortie) {

            foreach ($profilsortie -> getApprenants() as $key => $apprenant){
                if ( $apprenant->getGroupes()[0] ->getPromos()  !== $promo ){

                    $profilsortie->removeApprenant($apprenant);
                    
                }//on test ok

            }
            
        }
        return $this -> json($profilsorties,Response::HTTP_OK);
        
    }

    /**
     * @Route(
     *      name="get_profilsorties",
     *      path="api/admin/profilsorties",
     *      methods="GET",
     *      defaults={
     *          "_controller"="\app\ProfilsortieController::showprofilsortie",
    *           "_api_resource_class"=ProfilSortie::class,
    *           "_api_collection_operation_name"="show_profilsortie"
     *      }
     * )
     */
    public function showprofilsortie(ProfilSortieRepository $repo)
    {

        $profilsorties = $repo->findAll();
        return $this -> json($profilsorties, Response::HTTP_OK);

    }

 /**
     * @Route(
     *      name="showpromoid",
     *      path="api/admin/promos/{id}/profilsorties/{ida}",
     *      methods="GET",
     *      defaults={
     *          "_controller"="\app\ProfilsortieController::showpromoid",
    *           "_api_resource_class"=ProfilSortie::class,
    *           "_api_collection_operation_name"="showpromoid"
     *      }
     * )
     */
    public function showpromoid(PromosRepository $repopromo,int $id,int $ida, profilSortieRepository $profilsortierepo)
    {
        if(!$promo=$repopromo->find($id))
        { 
            return $this->json("error", Response::HTTP_NOT_FOUND);
        
        }
        if(! $profilsortie=$profilsortierepo->find($ida))
        { 
            return $this->json("error", Response::HTTP_NOT_FOUND);
        
        }
        foreach ($profilsortie -> getApprenants() as $key => $apprenant) {
            if ( $apprenant->getGroupes()[0] ->getPromos()  !== $promo ) {

                $profilsortie->removeApprenant($apprenant);


            }
        }
        return $profilsortie;
        
    }
}
