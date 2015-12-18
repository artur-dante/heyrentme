<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Testimonials;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ExecutionContextInterface;

class TestimonialsController extends BaseAdminController {
     /**
     * 
     * @Route("/admin/testimonials", name="admin_testimonials_list")
     */
    public function indexAction() {
        return $this->render('admin/testimonials/index.html.twig');
    }
    
    /**
     * @Route("/admin/testimonials/jsondata", name="admin_testimonials_jsondata")
     */
    public function JsonData(Request $request)
    {  
        $sortColumn = $request->get('sidx');
        $sortDirection = $request->get('sord');
        $pageSize = $request->get('rows');
        $page = $request->get('page');
        $callback = $request->get('callback');
        
        
        $repo = $this->getDoctrine()->getRepository('AppBundle:Testimonials');        
        $dataRows = $repo->getGridOverview($sortColumn, $sortDirection, $pageSize, $page);
        $rowsCount = $repo->countAll();
        $pagesCount = ceil($rowsCount / $pageSize);
        
        $rows = array(); // rows as json result        
        
        foreach ($dataRows as $dataRow) { // build single row
            $row = array();
            $row['id'] = $dataRow->getId();
            $cell = array();
            $i = 0;
            $cell[$i++] = '';
            $cell[$i++] = $dataRow->getId();
            $cell[$i++] = $dataRow->getName();
            $cell[$i++] = $dataRow->getAge();
            $cell[$i++] = $dataRow->getPlace();            
            $cell[$i++] = $dataRow->getPosition(); 
            
            $row['cell'] = $cell;
            array_push($rows, $row);
        }
        
        $result = array( // main result object as json
            'records' => $rowsCount,
            'page' => $page,
            'total' => $pagesCount,
            'rows' => $rows
        );        
        
        $resp = new JsonResponse($result, JsonResponse::HTTP_OK);
        $resp->setCallback($callback);
        return $resp;
        
    }
    
    /**
     * 
     * @Route("/admin/testimonials/new", name="admin_testimonials_new")
     */
    public function newAction(Request $request) {
        $testimonial = new Testimonials();
        
        $form = $this->createFormBuilder($testimonial)
                ->add('name', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('max' => 100))
                    )
                ))
                ->add('description', 'textarea', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('max' => 500))
                    )
                ))
                ->add('place', 'text', array(                    
                    'constraints' => array(
                        new NotBlank()
                    )
                ))
                ->add('age', 'integer', array(
                    'required' => true,
                    'constraints' => array(
                        new Type(array('type' => 'integer'))
                    )
                ))
                ->add('position', 'integer', array(
                    'required' => false,
                    'constraints' => array(
                        new Type(array('type' => 'integer'))
                    )
                ))
                ->getForm();
        //when the form is posted this method prefills entity with data from form
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($testimonial);
            $em->flush();

            return $this->redirectToRoute("admin_testimonials_list");
        }
        
        
        return $this->render('admin/testimonials/new.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * 
     * @Route("/admin/testimonials/edit/{id}", name="admin_testimonials_edit")
     */
    public function editAction(Request $request, $id) {
        $testimonial = $this->getDoctrine()->getRepository('AppBundle:Testimonials')->find($id);

        if (!$testimonial) {
            throw $this->createNotFoundException('No $testimonial found for id '.$id);
        }
        
        $form = $this->createFormBuilder($testimonial)
                ->add('name', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('max' => 100))
                    )
                ))
                ->add('id', 'hidden')
                ->add('description', 'textarea', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('max' => 500))
                    )
                ))
                ->add('place', 'text', array(                    
                    'constraints' => array(
                        new NotBlank()
                    )
                ))
                ->add('age', 'integer', array(
                    'required' => true,
                    'constraints' => array(
                        new Type(array('type' => 'integer'))
                    )
                ))
                ->add('position', 'integer', array(
                    'required' => false,
                    'constraints' => array(
                        new Type(array('type' => 'integer'))
                    )
                ))
                ->getForm();

       
        //when the form is posted this method prefills entity with data from form
        $form->handleRequest($request);
        
        if ($form->isValid()) {
             
           
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($testimonial);
            $em->flush();

            return $this->redirectToRoute("admin_testimonials_list");
        }
        
        
        return $this->render('admin/testimonials/edit.html.twig', array(
            'form' => $form->createView(),
            'testimonial' => $testimonial
        ));
    }
    
    /**
     * 
     * @Route("/admin/testimonials/delete/{id}", name="admin_testimonials_delete")
     */
    public function deleteAction(Request $request, $id) {
        $testimonial = $this->getDoctrine()->getRepository('AppBundle:Testimonials')->find($id);

        if (!$testimonial) {
            throw $this->createNotFoundException('No testimonial found for id '.$id);
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($testimonial);
        $em->flush();
        return $this->redirectToRoute("admin_testimonials_list");
    }
    
}
