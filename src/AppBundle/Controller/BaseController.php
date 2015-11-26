<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends Controller
{
    /*
     * We cach categories and subcategories in user session,
     * since they won't change often.
     */
    protected function initCategories($session) {
        if (!$session->has('CategoryList')) {
            $cats = $this->getDoctrine()->getRepository('AppBundle:Category')->getAllOrderedByPosition();
            $categories = array();
            foreach ($cats as $cat) {
                $categories[$cat->getSlug()] = $cat;
            }
            $session->set('CategoryList', $categories);
        }
    }
    protected function getCategories(Request $request) {
        $session = $request->getSession();
        $this->initCategories($session);
        return $session->get('CategoryList');
    }
    protected function getCategoryBySlug(Request $request, $slug) {
        $session = $request->getSession();
        $this->initCategories($session);
        $cats = $session->get('CategoryList');
        if (array_key_exists($slug, $cats)) {
            return $cats[$slug];
        } else {
            return null;
        }
    }
    protected function initSubcategories($session) {
        if (!$session->has('SubcategoryList')) {
            $subcats = $this->getDoctrine()->getRepository('AppBundle:Subcategory')->getAllOrderedByPosition();
            $subcategories  = array();
            $subcategoriesDict = array();
            foreach ($subcats as $s) {
                $subcategories[$s->getSlug()] = $s;
                $cat = $s->getCategory();
                if (!array_key_exists($cat->getId(), $subcategoriesDict)) {
                    $subcategoriesDict[$cat->getId()] = array();
                }
                array_push($subcategoriesDict[$cat->getId()], $s);
            }
            $session->set('SubcategoryList', $subcategories);
            $session->set('SubcategoryDict', $subcategoriesDict);
        }
    }
    protected function getSubcategories(Request $request, $category = null) {
        $session = $request->getSession();
        $this->initSubcategories($session);
        if ($category != null) {
            return $session->get('SubcategoryDict')[$category->getId()];
        }
        else {
            return $session->get('SubcategoryList');
        }
    }
    protected function getSubcategoryBySlug(Request $request, $slug) {
        $session = $request->getSession();
        $this->initSubcategories($session);
        $cats = $session->get('SubcategoryList');
        if (array_key_exists($slug, $cats)) {
            return $cats[$slug];
        } else {
            return null;
        }
    }
    
    
}
