<?php

namespace App\Controller;

use App\Entity\Promos;
use ContainerQb8dLCW\getPromosRepositoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PromosController extends AbstractController
{
    
    /**
     * @Route(
     *      path="/api/admin/promo", 
     *      name="promos",
     *      methods="POST",
     *      defaults={
     *           "_controller"="\app\PromosController::addPromo",
     *           "_api_resource_class"=Promos::class,
     *           "_api_collection_operation_name"="add_Promos"
     *  }
     * )
     */
    public function addPromo(Request $req)
    {
        $promoTab=json_decode($req->getContent());
        $promos= new Promos();
        $promos
            ->setLangue($promoTab['langue'])
            ->setTitre($promoTab['titre'])
            ->setDateDebut($promoTab['new \DateTime("2018-12-31 13:05:21"))->format("YW")'])
            ->setDateProvisoire($promoTab['dateprovisoire'])
            ->setDateFin($promoTab['datefin'])
            ->setFabrique($promoTab['fabrique'])
            ->setLieu($promoTab['lieu'])
            ->setDescription($promoTab['description']);
            dd($promos);

            
        
    }
}
