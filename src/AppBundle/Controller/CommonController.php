<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class CommonController extends BaseController {
    
    public function headerAction() {
        return $this->render('common/header.html.twig', array(
            //
        ));
    }

    public function categoryListAction(Request $request, $mobile = false) {       
        $categories = $this->getCategories($request);
        
        if ($mobile) {
            $tmpl = 'common/categoryListMob.html.twig';
        } else {
            $tmpl = 'common/categoryList.html.twig';
        }

        return $this->render($tmpl, array(
            'categories' => $categories
        ));
    }
}
