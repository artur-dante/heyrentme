<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends BaseAdminController {
    
    /**
     * 
     * @Route("/admin", name="admin")
     */
    public function indexAction() {
        return new Response("Hello Admin!");
    }
}
