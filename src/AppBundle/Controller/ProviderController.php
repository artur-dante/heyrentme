<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Equipment;


class ProviderController extends BaseController {

    /**
     * @Route("/einstellungen", name="einstellungen")
     */
    public function einstellungenAction(Request $request) {
        return $this->render('provider/einstellungen.html.twig');
    }
}
