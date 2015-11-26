<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends BaseAdminController {
    
    /**
     * 
     * @Route("/admin/category", name="admin_category_list")
     */
    public function indexAction() {
        return $this->render('admin/category/index.html.twig');
    }
    
    /**
     * 
     * @Route("/admin/category/new", name="admin_category_new")
     */
    public function newAction(Request $request, $id) {
        return $this->render('admin/category/new.html.twig');
    }
    
    /**
     * 
     * @Route("/admin/category/edit/{id}", name="admin_category_edit")
     */
    public function editAction(Request $request, $id) {
        return $this->render('admin/category/edit.html.twig');
    }
    
    /**
     * 
     * @Route("/admin/category/delete/{id}", name="admin_category_delete")
     */
    public function deleteAction(Request $request, $id) {
        
        return $this->redirectToRoute("admin_category_list");
    }
    
    
    /**
     * @Route("/admin/category/jsondata", name="admin_category_jsondata")
     */
    public function JsonData()
    {  
        $sortColumn = $_GET["sidx"];
        $sortDirection = $_GET["sord"];
        $pageSize = $_GET["rows"];
        $page = $_GET["page"];
        $method = $_GET["callback"];
        
        $rows = $this->GetData($sortColumn, $sortDirection, $pageSize, $page);
        $rowsCount = $this->getDoctrine()->getRepository('AppBundle:Category')->countAll();
        $pagesCount =   ceil($rowsCount/$pageSize);
        
        $rowsStr = "";
        $rowsTemplate = '{ "id": %s, "cell": [null, "%s", "%s", "%s", "%s" ] }';
        $i = 0;
        foreach($rows as $row){
            if ($i > 0) {
                $rowsStr .= ", ";
            }
            $rowsStr .= sprintf($rowsTemplate, $row->getId(), $row->getId(), $row->getName(), $row->getSlug(), $row->getPosition() );
            $i .=1;
        }
        
        $json = sprintf('{ "records":%s,"page":%s ,"total":%s ,"rows": [ %s ] }', $rowsCount, $page, $pagesCount, $rowsStr );
        
        $response = new Response();
        $response->setContent('/**/'.$method.'('. $json .')');
        $response->headers->set('Content-Type', 'text/javascript');
        return $response;
       
    }
    
    public function GetData($sortColumn, $sortDirection, $pageSize, $page)
    {
        $cats = $this->getDoctrine()->getRepository('AppBundle:Category')->getAll($sortColumn, $sortDirection, $pageSize, $page);
        return $cats;
    }


}
