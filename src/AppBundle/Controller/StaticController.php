<?php

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticController extends BaseController {
    
    #FAQ 
    /**
     * @Route("/breadcrumb/{pageName}", name="breadcrumb")
     */
    public function breadcrumbAction(Request $request, $pageName) {               
        return $this->render('static/faq/breadcrumb.html.twig', array('pageName' => $pageName));
    }
    
    /**
     * @Route("/leftmenu/{pageName}", name="leftmenu")
     */
    public function leftmenuAction(Request $request, $pageName) {               
        return $this->render('static/faq/left_menu.html.twig', array('pageName' => $pageName));
    }
    
    
    /**
     * @Route("/hilfeFaqEuipmentMieten", name="hilfe_faq_euipment_mieten")
     */
    public function hilfeFaqEuipmentMietenAction(Request $request) {               
        return $this->render('static/faq/hilfe_faq_euipment_mieten.html.twig');
    }
    
    /**
     * @Route("/hilfeFaqEuipmentAnbieten", name="hilfe_faq_euipment_anbieten")
     */
    public function hilfeFaqEuipmentAnbietenAction(Request $request) {               
        return $this->render('static/faq/hilfe_faq_euipment_anbieten.html.twig');
    }
    
    /**
     * @Route("/hilfeFaqTalenteBuchen", name="hilfe_faq_talente_buchen")
     */
    public function hilfeFaqTalenteBuchenAction(Request $request) {               
        return $this->render('static/faq/hilfe_faq_talente_buchen.html.twig');
    }
    
    /**
     * @Route("/hilfeFaqTalenteAnbieten", name="hilfe_faq_talente_anbieten")
     */
    public function hilfeFaqTalenteAnbietenAction(Request $request) {               
        return $this->render('static/faq/hilfe_faq_talente_anbieten.html.twig');
    }
    
    
    /**
     * @Route("/hilfeFaqFaqAnbieter", name="hilfe_faq_faq_anbieter")
     */
    public function hilfeFaqFaqAnbieter(Request $request) {               
        return $this->render('static/faq/hilfe_faq_faq_anbieter.html.twig');
    }
    
    /**
     * @Route("/hilfeFaqFaqNutzer", name="hilfe_faq_faq_nutzer")
     */
    public function hilfeFaqFaqNutzer(Request $request) {               
        return $this->render('static/faq/hilfe_faq_faq_nutzer.html.twig');
    }

    #ClientInfo
    /**
     * @Route("/clientInfoBreadcrumb/{pageName}/{pageNameShort}", name="client_info_breadcrumb")
     */
    public function clientInfoBreadcrumbAction(Request $request, $pageName, $pageNameShort) {               
        return $this->render('static/clientInfo/client_info_breadcrumb.html.twig', array('pageName' => $pageName, 'pageNameShort' => $pageNameShort));
    }
    
    /**
     * @Route("/clientInfoLeftmenu/{pageName}", name="client_info_leftmenu")
     */
    public function clientInfoLeftmenuAction(Request $request, $pageName) {               
        return $this->render('static/clientInfo/client_info_leftmenu.html.twig', array('pageName' => $pageName));
    }
    
    
    /**
     * @Route("/kundeninfosAgb", name="kundeninfos_agb")
     */
    public function kundeninfosAgbAction(Request $request) {               
        return $this->render('static/clientInfo/kundeninfos_agb.html.twig');
    }
    
    /**
     * @Route("/kundeninfosDatenschutz", name="kundeninfos_datenschutz")
     */
    public function kundeninfosDatenschutzAction(Request $request) {               
        return $this->render('static/clientInfo/kundeninfos_datenschutz.html.twig');
    }
    
    /**
     * @Route("/kundeninfosPresse", name="kundeninfos_presse")
     */
    public function kundeninfosPresseAgbAction(Request $request) {               
        return $this->render('static/clientInfo/kundeninfos_presse.html.twig');
    }
    
    /**
     * @Route("/kundeninfosKontakt", name="kundeninfos_kontakt")
     */
    public function kundeninfosKontaktAction(Request $request) {               
        return $this->render('static/clientInfo/kundeninfos_kontakt.html.twig');
    }
    
    /**
     * @Route("/kundeninfosImpressum", name="kundeninfos_impressum")
     */
    public function kundeninfosImpressumAction(Request $request) {               
        return $this->render('static/clientInfo/kundeninfos_impressum.html.twig');
    }
    
}
