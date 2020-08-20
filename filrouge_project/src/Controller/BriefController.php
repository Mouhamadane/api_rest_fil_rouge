<?php

namespace App\Controller;

use App\Entity\Brief;
use App\Entity\LivrablesAttendus;
use App\Entity\PromoBrief;
use App\Repository\BriefRepository;
use App\Repository\TagRepository;
use App\Repository\NiveauRepository;
use App\Repository\GroupesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReferentielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class BriefController extends AbstractController
{
    private $serializer;
    private $em;
    private $security;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em, Security $security)
    {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @Route(
     *      name="dupliquer_brief",
     *      path="api/formateurs/briefs/{id}",
     *      methods="POST",
     *      defaults={
     *          "_controller"="\app\BriefController::dupliquerBrief",
     *          "_api_resource_class"=Brief::class,
     *          "_api_collection_operation"="dupliquer_brief"
     *      }
     * )
     */
    public function dupliquerBrief(BriefRepository $repo, int $id, ValidatorInterface $validator) {
        $newBrief = new Brief;
        $brief = $repo->find($id);
        if($brief){
            $newBrief
                ->setLangue($brief->getLangue())
                ->setTitre($brief->getTitre())
                ->setDescription($brief->getDescription())
                ->setContexte($brief->getContexte())
                ->setLivrablesAttendus($brief->getLivrablesAttendus())
                ->setModaliteEvaluation($brief->getModaliteEvaluation())
                ->setCriterePerformance($brief->getCriterePerformance())
                ->setmodalitePedagogique($brief->getmodalitePedagogique())
                ->setReferentiel($brief->getReferentiel())
                ->setFormateur($this->security->getUser())
                ->setStatut("valide")
                ->setDateCreation(new \DateTime());

            foreach($brief->getTags() as $tag){
                $newBrief->addTag($tag);
            }

            foreach($brief->getLivrablesAttenduses() as $livrableAtt){
                $newBrief->addLivrablesAttendus($livrableAtt);
            }
            
            foreach($brief->getNiveaux() as $niveau){
                $newBrief->addNiveau($niveau);
            }
            
            foreach($brief->getRessources() as $res){
                $newBrief->addRessource($res);
            }
            $errors = $validator->validate($brief);
            if (count($errors)){
                $errors = $this->serializer->serialize($errors,"json");
                return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
            }
            $this->em->persist($brief);
            $this->em->flush();
            return $this->json(["message" => "Brief dupliqué"], Response::HTTP_CREATED);
        }else{
            return $this->json(["message" => "Resource Not found"], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route(
     *      name="ajouter_brief",
     *      path="api/formateurs/briefs",
     *      methods="POST",
     *      defaults={
     *          "_controller"="\app\BriefController::ajouterBrief",
     *          "_api_resource_class"=Brief::class,
     *          "_api_collection_operation"="ajouter_brief"
     *      }
     * )
     */
    public function ajouterBrief(
        Request $req,
        TagRepository $repoTag,
        NiveauRepository $repoNiveau,
        ReferentielRepository $repoRef,
        GroupesRepository $repoGrpe,
        ValidatorInterface $validator,
        \Swift_Mailer $mailer
    ) {
        $brief = new Brief;
        $briefTab = json_decode($req->getContent(), true);

        $brief
            ->setLangue($briefTab["langue"])
            ->setTitre($briefTab["titre"])
            ->setDescription($briefTab["description"])
            ->setContexte($briefTab["contexte"])
            ->setLivrablesAttendus($briefTab["livrablesAttendus"])
            ->setModalitePedagogique($briefTab["modalitePedagogique"])
            ->setCriterePerformance($briefTab["criterePerformance"])
            ->setModaliteEvaluation($briefTab["modaliteEvaluation"])
            ->setDateCreation(new \DateTime());

        if(isset($briefTab["tags"]) && !empty($briefTab["tags"])){
            foreach($briefTab["tags"] as $idTag){
                $tag = $repoTag->find($idTag);
                if($tag){
                    $brief->addTag($tag);
                }
            }
        }

        if(isset($briefTab["niveaux"]) && !empty($briefTab["niveaux"])){
            foreach($briefTab["niveaux"] as $idNiveau){
                $niveau = $repoNiveau->find($idNiveau);
                if($niveau){
                    $brief->addNiveau($niveau);
                }
            }
        }
        
        if(isset($briefTab["livrablesAttenduses"]) && !empty($briefTab["livrablesAttenduses"])){
            foreach($briefTab["livrablesAttenduses"] as $lvb){
                $livrableAtt = new LivrablesAttendus();
                $livrableAtt->setLibelle($lvb["libelle"]);
                $brief->addLivrablesAttendus($livrableAtt);
            }
        }

        if(isset($briefTab["referentiel"]) && !empty($briefTab["referentiel"])){
            $ref = $repoRef->find($briefTab["referentiel"]);
            if($ref){
                $brief->setReferentiel($ref);
            }else{
                return $this->json(["message" => "Le referentiel est obligatoire"], Response::HTTP_BAD_REQUEST);
            }
        }else{
            return $this->json(["message" => "Le referentiel est obligatoire"], Response::HTTP_BAD_REQUEST);
        }

        if(!empty($briefTab["groupes"])){
            foreach($briefTab["groupes"] as $grpe){
                $groupe = $repoGrpe->find($grpe);
                if($groupe){
                    $brief->addGroupe($groupe);
                    foreach($groupe->getApprenant() as $appren){
                        $message = (new \Swift_Message("Admission Sonatel Academy"))
                            ->setFrom("damanyelegrand@gmail.com")
                            ->setTo($appren->getEmail())
                            ->setBody("Bonjour ".$appren->getPrenom()." ".$appren->getNom()."\nLe brief ". $brief->getTitre() ." vous a été assigné.\nMerci.");
                        $mailer->send($message);
                    }
                    $promoBrief = new PromoBrief();
                    $promoBrief
                        ->setStatut("en_cours")
                        ->setPromos($groupe->getPromos())
                        ->setBrief($brief);
                    $this->em->persist($promoBrief);
                }
            }
            $brief->setStatut("assigne");
        }else{
            $brief->setStatut("brouillon");
        }
        $brief->setFormateur($this->security->getUser());
        $errors = $validator->validate($brief);
        if (count($errors)){
            $errors = $this->serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $this->em->flush();
        return $this->json(["message" => "Brief créé"], Response::HTTP_CREATED);
    }
}