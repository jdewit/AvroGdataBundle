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
 * @Route("/service")
 */
class ServiceController extends ContainerAware
{
     /**
     * Test user credentials.
     *
     * @Route("/testCredentials/{id}", name="avro_gdata_service_testCredentials")
     * @method("post")     
     */
    public function testCredentialsAction($id)
    {
        //TODO
        $this->container->get('avro_gdata.calendar_service')->getService($user);
    }
}

