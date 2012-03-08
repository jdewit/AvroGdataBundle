<?php

namespace Avro\GdataBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Area controller.
 *
 * @Route("/service")
 */
class ServiceController extends ContainerAware
{
     /**
     * Get areas.
     *
     * @Route("/testCredentials", name="avro_gdata_service_testCredentials")
     * @method("post")     
     */
    public function testCredentialsAction($filter)
    {
        $this->container->get('avro_gdata.calendar_service')->getService($user);
    }
}

