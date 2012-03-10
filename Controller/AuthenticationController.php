<?php

namespace Avro\GdataBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Service controller.
 *
 * @Route("/authentication")
 */
class AuthenticationController extends ContainerAware
{
     /**
     * Test user credentials.
     *
     * @Route("/test", name="avro_gdata_authentication_test")
     * @method("post")     
     */
    public function testAction()
    {
        $taskManager = $this->container->get('application_crm.task_manager');
        $clientManager = $this->container->get('application_crm.client_manager');
        
        $username = $this->container->get('request')->get('googleUsername');
        $password = $this->container->get('request')->get('googlePassword');

        try {
            $client = $this->container->get('avro_gdata.authenticator')->getClient($username, $password, false);

            if ($client instanceof \Zend_Gdata_HttpClient) {
                $response = new Response('{"status": "OK", "notice": "Connected!"}');
            }
        } catch (\Exception $e) {
            $response = new Response('{"status": "FAIL", "notice": '.json_encode($e->getMessage()).'}');
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}

