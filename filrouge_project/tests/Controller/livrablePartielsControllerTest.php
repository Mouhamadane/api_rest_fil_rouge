<?php


namespace App\Tests\Controller;


use App\Repository\LivrableRenduRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class livrablePartielsControllerTest extends WebTestCase
{
    protected function createAuthenticatedClient(string $username, string $password): KernelBrowser
    {
        $client = static ::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{
               "login":"corinne66@noos.fr",
               "password":"Test"
           }'
        );
        $data = json_decode($client->getResponse()->getContent(), true);
        $client->setServerParameter('HTTP_Authorization', \sprintf('Bearer %s', $data['token']));
        $client->setServerParameter('CONTENT_TYPE', 'application/json');

        return $client;
    }
    public function testShowProfil()
    {
        $client = $this->createAuthenticatedClient("corinne66@noos.fr","Test");
        $client->request('GET', 'admin/profils');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
    public function testShowComment()
    {
        $client = $this->createAuthenticatedClient("corinne66@noos.fr","Test");
        $client->request('GET','apprenants/livrablepartiels/2/commentaires');
        $assertEquals(Response::HTTP_OK,$client->getResponse()->getStatusCode());
    }
    public function testCreateComment()
    {
        $client = $this->createAuthenticatedClient("philippe36@louis.net","Test");
        $client->request(
            'POST',
            '/api/apprenants/livrablepartiels/1/commentaires',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{
                        "id":1,
                        "commentaire":{
                        "content": "test encore"
                        }
                    }'
        );
        $responseContent = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK,$responseContent->getStatusCode());

    }
}