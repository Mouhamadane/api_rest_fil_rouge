<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route(
     *      name="add_user",
     *      path="/api/admin/users",
     *      methods="POST",
     *      defaults={
     *          "_controller"="\app\UserController::createUser",
     *           "_api_resource_class"=User::class,
     *           "_api_collection_operation_name"="add_user"
     *      }
     * )
     */
    public function createUser(Request $request){
        $user = $request->getContent();

        dd($user);
    }
}