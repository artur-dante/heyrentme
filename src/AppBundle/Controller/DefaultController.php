<?php

namespace AppBundle\Controller;

use AppBundle\Utils\SearchState;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController {
    
    /**
     * @Route("/", name="start-page")
     */
    public function indexAction(Request $request) {
        return $this->render('default/index.html.twig');
    }
    
     
    /**
     * @Route("/rentme/{token}", name="rentme")
     */
    public function rentmeAction(Request $request, $token=null) {      
        $cats = $this->getCategories($request);
        
        $confirmed= null;
        $confParam = $request->query->get('confirmed');
        if ($confParam != null){
            $confirmed = true;
        }
        
        return $this->render('default/equipment_mieten.html.twig', array(
            'categories' => $cats,
            'token' => $token,
            'confirmed' => $confirmed
        ));
    }
    
        
    public function catchallAction(Request $request, $content) {
        /*
         * This controller tries to catch by url (in order):
         * - Category
         * - Subcategory
         * - Offered item
         * 
         * 2015-12-10: Subcategory gets suspended until further notice.
         */
        
        // Category
        $result = $this->processCategory($request, $content);
        if ($result != null) {
            return $result;
        }
        /* suspended
        // Subcategory
        $result = $this->processSubcategory($request, $content);
        if ($result != null) {
            return $result;
        }
        */      
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
        $ss = $this->getSearchState($request);
        $ss->getSearchParams()->setCategoryId($cat['id']);
        $request->getSession()->set('SearchState', $ss);
        
        if ($cat != null) {
            //$equipments = $this->getDoctrine()->getRepository('AppBundle:Equipment')->getAll($cat['id']);
            
            return $this->render('default/categorie.html.twig', array(
                'category' => $cat,
                'searchState' => $ss
                //'equipments' => $equipments
            ));
        }
        return null;
    }
    private function processSubcategory(Request $request, $content) {
        $subcat = $this->getSubcategoryBySlug($request, $content);
        
        if ($subcat != null) {            
            $equipments = $this->getDoctrine()->getRepository('AppBundle:Equipment')->getAllBySubcategory($subcat->getId());
            
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
                'category' => $eq->getSubcategory()->getCategory(),
                'categories' => $this->getCategories($request)
            ));
        }
        return null;
    }

    /**
     * @Route("/equipment-list", name="equipment-list")
     */ 
    public function equipmentListAction(Request $request) {
        $ss = $this->getSearchState($request);
        $ss->getSearchParams()->updateFromRequest($request);
        $request->getSession()->set('SearchState', $ss);
                
        //$equipments = $this->getDoctrine()->getRepository('AppBundle:Equipment')->findAll();
        $equipments = $this->getDoctrine()->getRepository('AppBundle:Equipment')->getAll($ss->getSearchParams());
        
        return $this->render('default/equipment-list.html.twig', array(
            'equipments' => $equipments
        ));
    }
    
    private function getSearchState(Request $request) {
        $session = $request->getSession();
        if ($session->has('SearchState')) {
            $ss = $session->get('SearchState');
        }
        else {
            $ss = new SearchState();
            $session->set('SearchStage', $ss);            
        }
        return $ss;
    }
    
    /**
     * @Route("/subcats/{id}", name="subcat")
     */
    public function subcategoriesAction(Request $request, $id) {
        return new JsonResponse($this->getSubcategories($request, $id));
    }
}
