<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils;
use AppBundle\Entity\Blog;

class BlogController  extends BaseAdminController {
    /**
     * 
     * @Route("/admin/blog", name="admin_blog_list")
     */
    public function indexAction() {
        return $this->render('admin/blog/index.html.twig');
    }
    
     /**
     * 
     * @Route("/admin/blog/new", name="admin_blog_new")
     */
    public function newAction(Request $request) {
        $blog = new Blog();
        
        $form = $this->createFormBuilder($blog)
                ->add('title', 'text')
                ->add('content', 'textarea')
                ->add('position', 'text', array('required' => false))
                ->getForm();
        //when the form is posted this method prefills entity with data from form
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            //check if there is file
            $file = $request->files->get('upl');
            $date = new \DateTime();            
            $blog->setCreationDate($date);
            $blog->setModificationDate($date);
            
            $em = $this->getDoctrine()->getManager();
            
            if ($file != null && $file->isValid()) {
                
                // save file
                $uuid = Utils::getUuid();
                $image_storage_dir = $this->getParameter('image_storage_dir');
                
                $destDir = 
                    $image_storage_dir .
                    DIRECTORY_SEPARATOR .
                    'blog' .
                    DIRECTORY_SEPARATOR;
                $destFilename = sprintf("%s.%s", $uuid, $file->getClientOriginalExtension());
                
                $file->move($destDir, $destFilename);
                
                // create object
                $img = new \AppBundle\Entity\Image();
                $img->setUuid($uuid);
                $img->setName($destFilename);
                $img->setExtension($file->getClientOriginalExtension());
                $img->setOriginalPath($file->getClientOriginalName());
                $img->setPath('blog');
                              
                $em->persist($img);
                $em->flush();
                
                $blog->setImage($img);
            }
            
            // save to db
            
            $em->persist($blog);
            $em->flush();

            return $this->redirectToRoute("admin_blog_list");
        }
        
        
        return $this->render('admin/blog/new.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * 
     * @Route("/admin/blog/edit/{id}", name="admin_blog_edit")
     */
    public function editAction(Request $request, $id) {
        $blog = $this->getDoctrine()->getRepository('AppBundle:Blog')->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('No blog post found for id '.$id);
        }
        
        $form = $this->createFormBuilder($blog)
                ->add('id', 'hidden')
                ->add('title', 'text')
                ->add('content', 'textarea')                
                ->add('position', 'text', array('required' => false))
                ->getForm();

       
        //when the form is posted this method prefills entity with data from form
        $form->handleRequest($request);
        
        if ($form->isValid()) {
             
            $blog->setModificationDate(new \DateTime());
            //check if there is file
            $file = $request->files->get('upl');
            
            $em = $this->getDoctrine()->getManager();
            
            if ($file != null && $file->isValid()) {
                
                //remove old Image (both file from filesystem and entity from db)
                $this->getDoctrine()->getRepository('AppBundle:Blog')->removeImage($blog, $this->getParameter('image_storage_dir'));
                
                
                // save file
                $uuid = Utils::getUuid();
                $image_storage_dir = $this->getParameter('image_storage_dir');
                
                //$destDir = sprintf("%sblog\\",$image_storage_dir);                
                $destDir = 
                        $image_storage_dir .
                        DIRECTORY_SEPARATOR .
                        'blog' .
                        DIRECTORY_SEPARATOR;
                $destFilename = sprintf("%s.%s", $uuid, $file->getClientOriginalExtension());
                
                $file->move($destDir, $destFilename);
                
                // create object
                $img = new \AppBundle\Entity\Image();
                $img->setUuid($uuid);
                $img->setName($destFilename);
                $img->setExtension($file->getClientOriginalExtension());
                $img->setOriginalPath($file->getClientOriginalName());
                $img->setPath('blog');
                              
                $em->persist($img);
                $em->flush();
                
                $blog->setImage($img);
            }            
            
            // save to db

            $em->persist($blog);
            $em->flush();

            return $this->redirectToRoute("admin_blog_list");
        }
        
        
        return $this->render('admin/blog/edit.html.twig', array(
            'form' => $form->createView(),
            'blog' => $blog
        ));
    }
    
    /**
     * 
     * @Route("/admin/blog/delete/{id}", name="admin_blog_delete")
     */
    public function deleteAction(Request $request, $id) {
        $blog = $this->getDoctrine()->getRepository('AppBundle:Blog')->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('No blog post found for id '.$id);
        }
        
        //remove old Image (both file from filesystem and entity from db)
        $this->getDoctrine()->getRepository('AppBundle:Blog')->removeImage($blog, $this->getParameter('image_storage_dir'));
                
        $em = $this->getDoctrine()->getManager();
        $em->remove($blog);
        $em->flush();
        return $this->redirectToRoute("admin_blog_list");
    }
    
    
    /**
     * @Route("/admin/blog/jsondata", name="admin_blog_jsondata")
     */
    public function JsonData()
    {  
        $sortColumn = $_GET["sidx"];
        $sortDirection = $_GET["sord"];
        $pageSize = $_GET["rows"];
        $page = $_GET["page"];
        $method = $_GET["callback"];
        
        $rows = $this->GetData($sortColumn, $sortDirection, $pageSize, $page);
        $rowsCount = $this->getDoctrine()->getRepository('AppBundle:Blog')->countAll();
        $pagesCount =   ceil($rowsCount/$pageSize);
        
        $rowsStr = "";
        $rowsTemplate = '{ "id": %s, "cell": [null, "%s", "%s", "%s", "%s", "%s" ] }';
        $i = 0;
        foreach($rows as $row){
            if ($i > 0) {
                $rowsStr .= ", ";
            }
            $rowsStr .= sprintf($rowsTemplate, $row->getId(), $row->getId(), $row->getTitle(), $row->getCreationDate()->format('Y-m-d H:i:s'), $row->getModificationDate()->format('Y-m-d H:i:s'), $row->getPosition() );
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
        $posts = $this->getDoctrine()->getRepository('AppBundle:Blog')->getAll($sortColumn, $sortDirection, $pageSize, $page);
        return $posts;
    }
}
