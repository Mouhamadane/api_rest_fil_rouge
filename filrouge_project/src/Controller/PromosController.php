<?php

namespace App\Controller;

use App\Entity\Groupes;
use DateTime;
use App\Entity\Promos;
use App\Entity\Referentiel;
use App\Repository\FormateurRepository;
use App\Repository\ReferentielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use ContainerQb8dLCW\getPromosRepositoryService;
use PhpParser\Node\Stmt\Foreach_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function addPromo(Request $req, ReferentielRepository $reporef, FormateurRepository $repoformateurs)
    {
        
        $promoTab=json_decode($req->getContent(),true);
        $promos= new Promos();
        $promos
            ->setLangue($promoTab['langue'])
            ->setTitre($promoTab['titre'])
            ->setDateProvisoire(new \DateTime($promoTab['dateProvisoire']))
            ->setDateDebut(new \DateTime($promoTab['dateDebut']))
            ->setFabrique($promoTab['fabrique'])
            ->setLieu($promoTab['lieu'])
            ->setDescription($promoTab['description']);
        $referentiel= $reporef->find($promoTab['referentiel']['id']); 
        if($referentiel){
            $promos->setReferentiel($referentiel);
        }else{
            return $this->json(['message'=>'Le referentiel est obligatoire']);
        }
        $groupes= new Groupes();

        if(!empty($promoTab["groupes"])){
            $groupe=$promoTab["groupes"][0];
            $groupes
                ->setNom($groupe['nom'])
                ->setType($groupe['type'])
                ->setStatut(true)
                ->setDateCreation(new \DateTime());
            $promos->addGroupe($groupes);

        }else{
            return $this->json(["message"=>"l'ajout de  groupe est obligatoire principale"]);
        }
        if(!empty($promoTab["formateurs"])){
            foreach ($promoTab["formateurs"] as $key) {
                $formateur=$repoformateurs->find($key["id"]);
                if($formateur){
                        $promos->addFormateur($formateur);
                        
                 }else{
                    return $this->json(["message"=>"l'id n'est pas celui d'un formateur"]);
                 }
            }
        }else{
            return $this->json(["message"=>"l'ajout d'un formateur est obligatoire"]);
        }
             dd($promos);

    }
}
