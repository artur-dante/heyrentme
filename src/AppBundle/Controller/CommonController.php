<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\CategoryRepository;

class CommonController extends BaseController {
    
    public function headerAction() {
        return $this->render('common/header.html.twig', array(
            //
        ));
    }

    public function categoryListAction(Request $request, $mobile = false) {       
        $session = $request->getSession();
        if (!$session->has('CategoryList')) {
            $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->getAllOrderedByPosition();
        }
        else {
            $categories = $session->get('CategoryList');
        }
        
        if ($mobile)
            $tmpl = 'common/categoryListMob.html.twig';
        else
            $tmpl = 'common/categoryList.html.twig';
                
        return $this->render($tmpl, array(
            'categories' => $categories
        ));
    }
}
