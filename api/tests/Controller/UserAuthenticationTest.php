<?php


namespace App\Tests\Controller;

use App\Model\User;

class UserAuthenticationTest extends CommandTestBaseClass
{

    public function provider(){
        $admin = $this->createRandomAdmin();
        $user = $this->createRandomUser();
        return [
            [$admin, 200],
            [$user, 403]
        ];
    }

    /**
     * @dataProvider provider
     * @param User $user
     * @param $statusCode
     */
    public function testAuthentication($user,$statusCode){
        $client = static::createClient();
        $client->request('POST','/api/login_check',[],[],['CONTENT_TYPE'=>'application/json'],
            json_encode(["username"=>$user->getUsername(),"password"=> $user->getPassword()]));

        $this->assertEquals(200,$client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(),true);
        $token = $content['token'];

        $client->request('GET','users',[],[],['HTTP_Authorization' => sprintf('Bearer %s',$token)]);

        $this->assertEquals($statusCode,$client->getResponse()->getStatusCode());
    }
}
