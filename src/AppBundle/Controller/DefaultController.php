<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $subcats = $this->getSubcategories($request, $category);
                
        return $this->render('default/equipment_mieten.html.twig', array(
            'subcategories' => $subcats,
            'category' => $category
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
        $cat = $this->getCategoryBySlug($request, $content);
        
        if ($cat != null) {
            $subcats = $this->getSubcategories($request, $cat);
            
            return $this->render('default/equipment_mieten.html.twig', array(
                'subcategories' => $subcats,
                'category' => $cat
            ));
        }
        
        // Subcategory
        $subcat = $this->getSubcategoryBySlug($request, $content);
        
        if ($subcat != null) {
            $equipments = $this->getDoctrine()->getRepository('AppBundle:Equipment')->findAll($subcat);
            
            return $this->render('default/categorie.html.twig', array(
                'subcategory' => $subcat,
                'equipments' => $equipments
            ));
        }
        
        // Nothing was matched, URL is invalid
        throw $this->createNotFoundException();
    }

    /**
     * @Route("/test", name="test")
     */
    public function testAction(Request $request) {
        $cat = $this->getDoctrine()->getRepository('AppBundle:Category')->find(1);
        $subs = $cat->getSubcategories();
        $s = '';
        foreach($subs as $sub) {
            $s = $s . "Sub: {$sub->getName()} (id: {$sub->getId()}) ";
            $c = $sub->getCategory();
            $img = $c->getImage();
            $s = $s . "Cat: {$c->getName()} (id: {$c->getId()}), img: {$img->getUuid()} <br/>";
            $eqs = $sub->getEquipments();
            foreach($eqs as $eq) {
                $s = $s . "----> Equipment: {$eq->getName()} (id: {$eq->getId()}) <br/>";
            }
        }
        
        return new Response($s);
    }    
    
    /**
     * @Route("/bcrypt", name="bcrypt")
     */
    public function bcryptAction() {
        $h = password_hash("ziller/1793", PASSWORD_BCRYPT, array( 'cost' => 12));
        return new Response($h);
    }
}
