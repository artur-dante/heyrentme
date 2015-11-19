<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\CategoryRepository;

class DefaultController extends BaseController {
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {
        return $this->render('default/index.html.twig');
    }
    
    /**
     * @Route("/rentme", name="rentme")
     */
    public function rentmeAction(Request $request, $category = null) {        
        
        $subcats = $this->getDoctrine()->getRepository('AppBundle:Subcategory')->getAllOrderedByPosition();
        
        return $this->render('default/equipment_mieten.html.twig', array(
            'subcats' => $subcats
        ));
    }
        
    public function catchallAction(Request $request, $content) {
        /*
         * This controller tries to catch by url (in order):
         * - Category
         * - Subcategory
         * - Offered item
         */
        
        // Category
        $rep = $this->getDoctrine()->getRepository("AppBundle:Category");
        $cat = $rep->getCategoryBySlug($content);
        
        if ($cat != null) {
            $subcats = $this->getDoctrine()->getRepository('AppBundle:Subcategory')->getAllOrderedByPosition($cat);
            
            return $this->render('default/equipment_mieten.html.twig', array(
                'subcats' => $subcats
            ));
        }
        
        // Subcategory
        $rep = $this->getDoctrine()->getRepository("AppBundle:Subcategory");
        $subcat = $rep->getSubcategoryBySlug($content);
        
        if ($subcat != null) {
            $equipments = $this->getDoctrine()->getRepository('AppBundle:Equipment')->getAll($subcat);
            
            return $this->render('default/categorie.html.twig', array(
                'equipments' => $equipments
            ));
        }
        
        
        // Nothing was matched, URL is invalid
        throw $this->createNotFoundException();
    }

}
