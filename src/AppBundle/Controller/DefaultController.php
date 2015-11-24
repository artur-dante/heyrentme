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
        $result = $this->processCategory($request, $content);
        if ($result != null) {
            return $result;
        }
        // Subcategory
        $result = $this->processSubcategory($request, $content);
        if ($result != null) {
            return $result;
        }        
        // Equipment
        $result = $this->processEquipment($request, $content);
        if ($result != null) {
            return $result;
        }
        // Nothing was matched, URL is invalid
        throw $this->createNotFoundException();
    }
    
    private function processCategory(Request $request, $content) {
        $cat = $this->getCategoryBySlug($request, $content);
        
        if ($cat != null) {
            $subcats = $this->getSubcategories($request, $cat);
            
            return $this->render('default/equipment_mieten.html.twig', array(
                'subcategories' => $subcats,
                'category' => $cat
            ));
        }
        return null;
    }
    private function processSubcategory(Request $request, $content) {
        $subcat = $this->getSubcategoryBySlug($request, $content);
        
        if ($subcat != null) {
            $equipments = $this->getDoctrine()->getRepository('AppBundle:Equipment')->findAll($subcat);
            
            return $this->render('default/categorie.html.twig', array(
                'subcategory' => $subcat,
                'equipments' => $equipments
            ));
        }
        return null;
    }
    private function processEquipment(Request $request, $content) {
        $eq = null;
        $pat = '^[[:digit:]]+/.+$';
        if (ereg($pat, $content)) {
            $arr = split('/', $content);
            $eq = $this->getDoctrine()->getRepository('AppBundle:Equipment')->find(intval($arr[0]));
        }
        
        if ($eq != null) {
            return $this->render('default/equipment.html.twig', array(
                'equipment' => $eq,
                'category' => $eq->getSubcategory()->getCategory()
            ));
        }
        return null;
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
