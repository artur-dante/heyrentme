<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Category;

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
    public function newAction(Request $request) {
        $category = new Category();
        
        $form = $this->createFormBuilder($category)
                ->add('name', 'text')
                ->add('slug', 'text')
                ->add('position', 'text', array('required' => false))
                ->getForm();
        //when the form is posted this method prefills entity with data from form
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            // save to db
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute("admin_category_list");
        }
        
        
        return $this->render('admin/category/new.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * 
     * @Route("/admin/category/edit/{id}", name="admin_category_edit")
     */
    public function editAction(Request $request, $id) {
        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);

        if (!$category) {
            throw $this->createNotFoundException('No category found for id '.$id);
        }
        
        $form = $this->createFormBuilder($category)
                ->add('id', 'hidden')
                ->add('name', 'text')
                ->add('slug', 'text')
                ->add('position', 'text', array('required' => false))
                ->getForm();

       
        //when the form is posted this method prefills entity with data from form
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            // save to db
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute("admin_category_list");
        }
        
        
        return $this->render('admin/category/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * 
     * @Route("/admin/category/delete/{id}", name="admin_category_delete")
     */
    public function deleteAction(Request $request, $id) {
        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);

        if (!$category) {
            throw $this->createNotFoundException('No category found for id '.$id);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
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
