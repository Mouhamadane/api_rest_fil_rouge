<?php


namespace App\Tests;


use App\Entity\Commentaire;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class testUnitaire extends KernelTestCase
{
    public function getEntity()
    {
        $commentaire = (new Commentaire())
            ->setContent("")
            ->setDate(new \DateTime())
        ;
        return $commentaire;
    }
    public function assertHasErrors(Commentaire $com, $number=0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($com);
        $message = [];
        /** @var constraintViolation $error */
        foreach ($errors as $error) {
            $message[] = $error->getPropertyPath().'=>'.$error->getMessage();
        }
        $this->assertCount($number,$errors, implode(', ', $message));
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(),0);
    }

    public function testInvalidEntityCodeUnique()
    {
        //$this->loadFixtures([InvitationCodeRepository::class]);
        $this->assertHasErrors($this->getEntity()->setCode("12345"),0);
    }
}