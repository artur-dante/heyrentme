<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swift_Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends BaseController {
    
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {
        return $this->render('default/index.html.twig');
    }
    
     
    /**
     * @Route("/rentme/{token}", name="rentme")
     */
    public function rentmeAction(Request $request, $category = null, $token=null) {      
        $catId = null;
        if ($category != null) {
            $catId = $category->getId();
        }
        
        $subcats = $this->getSubcategories($request, $category);        
        
        $confirmed= null;
        $confParam = $request->query->get('confirmed');
        if ($confParam != null){
            $confirmed = true;
        }
        
        return $this->render('default/equipment_mieten.html.twig', array(
            'subcategories' => $subcats,
            'category' => $category,
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
            $subcats = $this->getSubcategories($request, $cat->getId());
            
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
     * @Route("/subcats/{id}", name="subcat")
     */
    public function subcategoriesAction(Request $request, $id) {
        $subcats = $this->getSubcategories($request, $id);
        $arr = array();
        foreach ($subcats as $s) {
            array_push($arr, array('id' => $s->getId(), 'name' => $s->getName()));
        }
        
        return new JsonResponse($arr);
    }
    
    /**
     * @Route("/test", name="test")
     */
    public function testAction(Request $request) {
        return new Response(phpinfo());
    }        
    
     /**
     * @Route("/send", name="sendEmail")
     */
    public function Send()
    {
        $name = "TestTESTtest";
        $message = Swift_Message::newInstance()
        ->setSubject('Hello Email')
        ->setFrom('yaspen@tlen.pl')
        ->setTo('yaspen@tlen.pl')
        ->setBody(
            $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                'Emails/registration.html.twig',
                array('name' => $name)
            ),
            'text/html'
        )
        /*
         * If you also want to include a plaintext version of the message
        ->addPart(
            $this->renderView(
                'Emails/registration.txt.twig',
                array('name' => $name)
            ),
            'text/plain'
        )
        */
        ;
        $this->get('mailer')->send($message);
    }
    
    /**
     * @Route("/vermietung", name="vermietung")
     */
    public function someTestAction(Request $request) {
        return $this->render('default/index.html.twig');
    }    
}
