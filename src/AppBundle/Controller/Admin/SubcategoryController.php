<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils;
use AppBundle\Entity\Category;
use AppBundle\Entity\Subcategory;

class SubcategoryController extends BaseAdminController {
    
    /**
     * 
     * @Route("/admin/subcategory/{categoryID}", name="admin_subcategory_list")
     */
    public function indexAction($categoryID) {
        return $this->render('admin/subcategory/index.html.twig', array(
            'category' => $this->getDoctrine()->getManager()->getReference('AppBundle:Category', $categoryID)
        ));
    }
    
    /**
     * 
     * @Route("/admin/subcategory/new/{categoryID}", name="admin_subcategory_new")
     */
    public function newAction(Request $request, $categoryID) {
        $subcategory = new Subcategory();
        $category = $this->getDoctrine()->getManager()->getReference('AppBundle:Category', $categoryID);
        $form = $this->createFormBuilder($subcategory)
                ->add('category', 'entity', array(
                  'class' => 'AppBundle:Category',
                  'property' => 'name',
                  'data' => $category
                  ))
                ->add('name', 'text')
                ->add('slug', 'text')
                ->add('position', 'text', array('required' => false))
                ->getForm();
        //when the form is posted this method prefills entity with data from form
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            //check if there is file
            $file = $request->files->get('upl');
            
            $em = $this->getDoctrine()->getManager();
            
            if ($file != null && $file->isValid()) {
                
                // save file
                $uuid = Utils::getUuid();
                $image_storage_dir = $this->getParameter('image_storage_dir');
                
                $destDir = sprintf("%ssubcategory\\",$image_storage_dir);
                $destFilename = sprintf("%s.%s", $uuid, $file->getClientOriginalExtension());
                
                $file->move($destDir, $destFilename);
                
                // create object
                $img = new \AppBundle\Entity\Image();
                $img->setUuid($uuid);
                $img->setName($destFilename);
                $img->setExtension($file->getClientOriginalExtension());
                $img->setOriginalPath($file->getClientOriginalName());
                $img->setPath('subcategory');
                              
                $em->persist($img);
                $em->flush();
                
                $subcategory->setImage($img);
            }
            
            // save to db
            $em->persist($subcategory);
            $em->flush();

            return $this->redirectToRoute("admin_subcategory_list", array( 'categoryID' => $categoryID ));
        }
        
        
        return $this->render('admin/subcategory/new.html.twig', array(
            'form' => $form->createView(),
            'category' => $category
        ));
    }
    
    /**
     * 
     * @Route("/admin/subcategory/edit/{id}", name="admin_subcategory_edit")
     */
    public function editAction(Request $request, $id) {
        $subcategory = $this->getDoctrine()->getRepository('AppBundle:Subcategory')->find($id);
        if (!$subcategory) {
            throw $this->createNotFoundException('No subcategory found for id '.$id);
        }
        $category = $subcategory->getCategory();
        
        $form = $this->createFormBuilder($subcategory)
                ->add('id', 'hidden')
                ->add('category', 'entity', array(
                  'class' => 'AppBundle:Category',
                  'property' => 'name',
                  'data' => $category
                  ))
                ->add('name', 'text')
                ->add('slug', 'text')
                ->add('position', 'text', array('required' => false))
                ->getForm();

       
        //when the form is posted this method prefills entity with data from form
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            //check if there is file
            $file = $request->files->get('upl');
            
            $em = $this->getDoctrine()->getManager();
            
            if ($file != null && $file->isValid()) {
                
                //remove old Image (both file from filesystem and entity from db)
                $this->getDoctrine()->getRepository('AppBundle:Subcategory')->removeImage($subcategory, $this->getParameter('image_storage_dir'));
                
                
                // save file
                $uuid = Utils::getUuid();
                $image_storage_dir = $this->getParameter('image_storage_dir');
                
                $destDir = sprintf("%scategory\\",$image_storage_dir);
                $destFilename = sprintf("%s.%s", $uuid, $file->getClientOriginalExtension());
                
                $file->move($destDir, $destFilename);
                
                // create object
                $img = new \AppBundle\Entity\Image();
                $img->setUuid($uuid);
                $img->setName($destFilename);
                $img->setExtension($file->getClientOriginalExtension());
                $img->setOriginalPath($file->getClientOriginalName());
                $img->setPath('category');
                              
                $em->persist($img);
                $em->flush();
                
                $subcategory->setImage($img);
            }            
            
            
            // save to db
            $em->persist($subcategory);
            $em->flush();

            return $this->redirectToRoute("admin_subcategory_list", array( 'categoryID' => $category->getId() ));
        }
        
        
        return $this->render('admin/subcategory/edit.html.twig', array(
            'form' => $form->createView(),
            'category' => $category,
            'subcategory' => $subcategory
        ));
    }
    
    /**
     * 
     * @Route("/admin/subcategory/delete/{id}", name="admin_subcategory_delete")
     */
    public function deleteAction(Request $request, $id) {
        $subcategory = $this->getDoctrine()->getRepository('AppBundle:Subcategory')->find($id);

        if (!$subcategory) {
            throw $this->createNotFoundException('No subcategory found for id '.$id);
        
        }
        //remove old Image (both file from filesystem and entity from db)
        $this->getDoctrine()->getRepository('AppBundle:Subcategory')->removeImage($subcategory, $this->getParameter('image_storage_dir'));
        
        
        $category = $subcategory->getCategory();
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($subcategory);
        $em->flush();
        return $this->redirectToRoute("admin_subcategory_list", array( 'categoryID' => $category->getId() ));
    }
    
    
    /**
     * @Route("/admin/subcategory/jsondata/{categoryID}", name="admin_subcategory_jsondata")
     */
    public function JsonData($categoryID)
    {  
        $sortColumn = $_GET["sidx"];
        $sortDirection = $_GET["sord"];
        $pageSize = $_GET["rows"];
        $page = $_GET["page"];
        $method = $_GET["callback"];
        
        $rows = $this->GetData($categoryID, $sortColumn, $sortDirection, $pageSize, $page);
        $rowsCount = $this->getDoctrine()->getRepository('AppBundle:Subcategory')->countAllByCategoryId($categoryID);
        $pagesCount =   ceil($rowsCount/$pageSize);
        
        $rowsStr = "";
        $rowsTemplate = '{ "id": %s, "cell": [null, "%s", "%s", "%s", "%s", "%s" ] }';
        $i = 0;
        foreach($rows as $row){
            if ($i > 0) {
                $rowsStr .= ", ";
            }
            $rowsStr .= sprintf($rowsTemplate, $row->getId(), $row->getId(), $row->getCategory()->getName(), $row->getName(), $row->getSlug(), $row->getPosition() );
            $i .=1;
        }
        
        $json = sprintf('{ "records":%s,"page":%s ,"total":%s ,"rows": [ %s ] }', $rowsCount, $page, $pagesCount, $rowsStr );
        
        $response = new Response();
        $response->setContent('/**/'.$method.'('. $json .')');
        $response->headers->set('Content-Type', 'text/javascript');
        return $response;
       
    }
    
    public function GetData($categoryID, $sortColumn, $sortDirection, $pageSize, $page)
    {
        $cats = $this->getDoctrine()->getRepository('AppBundle:Subcategory')->getAllByCategoryId($categoryID, $sortColumn, $sortDirection, $pageSize, $page);
        return $cats;
    }


}