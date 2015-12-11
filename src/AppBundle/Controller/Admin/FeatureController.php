<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Feature;
use AppBundle\Entity\Image;
use AppBundle\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;



class FeatureController extends BaseAdminController {
    
    /**
     * 
     * @Route("/admin/feature/{featureSectionId}", name="admin_feature_list")
     */
    public function indexAction($featureSectionId) {
        
        return $this->render('admin/feature/index.html.twig');
    }
    
    /**
     * 
     * @Route("/admin/feature/new/{featureSectionId}", name="admin_feature_new")
     */
    public function newAction(Request $request, $featureSectionId) {
        $feature = new Feature();
        $featureSection = $this->getDoctrine()->getRepository('AppBundle:FeatureSection')->find($featureSectionId);
        
        $form = $this->createFormBuilder($feature)
                ->add('featureSection', 'hidden', array(
                  'class' => 'AppBundle:FeatureSection',
                  'property' => 'name',
                  'data' => $featureSection
                  ))
                ->add('name', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('max' => 128))
                    )
                ))
                ->add('shortName', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('max' => 128))
                    )
                ))
                ->add('freetext', 'checkbox', array(
                    'required' => false
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
            //check if there is file
            $em = $this->getDoctrine()->getManager();
            
            // save to db
            $em->persist($feature);
            $em->flush();

            return $this->redirectToRoute("admin_feature_list");
        }
        
        
        return $this->render('admin/feature/new.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * 
     * @Route("/admin/feature/edit/{id}", name="admin_feature_edit")
     */
    public function editAction(Request $request, $id) {
        $feature = $this->getDoctrine()->getRepository('AppBundle:Feature')->find($id);

        if (!$feature) {
            throw $this->createNotFoundException('No feature found for id '.$id);
        }        
        
        $form = $this->createFormBuilder($feature)
                ->add('id', 'hidden')
                ->add('featureSection', 'hidden', array(
                  'class' => 'AppBundle:FeatureSection',
                  'property' => 'name',
                  'data' => $feature->getFeatureSection()
                  ))
                ->add('name', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('max' => 128))
                    )
                ))
                ->add('shortName', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('max' => 128))
                    )
                ))
                ->add('exclusive', 'checkbox', array(
                    'required' => false
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
            $em->persist($feature);
            $em->flush();

            return $this->redirectToRoute("admin_feature_list");
        }
        
        
        return $this->render('admin/feature/edit.html.twig', array(
            'form' => $form->createView(),
            'feature' => $feature
        ));
    }
    
    /**
     * 
     * @Route("/admin/feature/delete/{id}", name="admin_feature_delete")
     */
    public function deleteAction(Request $request, $id) {
        $feature = $this->getDoctrine()->getRepository('AppBundle:Feature')->find($id);

        if (!$feature) {
            throw $this->createNotFoundException('No feature found for id '.$id);
        }
               
        $em = $this->getDoctrine()->getManager();
        $em->remove($feature);
        $em->flush();
        return $this->redirectToRoute("admin_feature_list");
    }
    
    
    /**
     * @Route("/admin/feature/jsondata", name="admin_feature_jsondata")
     */
    public function JsonData(Request $request) {  
        $sortColumn = $request->get('sidx');
        $sortDirection = $request->get('sord');
        $pageSize = $request->get('rows');
        $page = $request->get('page');
        $fSubcategory = $request->get('s_position');
        $fName = $request->get('fs_name');
        $callback = $request->get('callback');
        
        $repo = $this->getDoctrine()->getRepository('AppBundle:Feature');
        $dataRows = $repo->getGridOverview($sortColumn, $sortDirection, $pageSize, $page, $fSubcategory, $fName);
        $rowsCount = $repo->countAll();
        $pagesCount = ceil($rowsCount / $pageSize);
        
        $rows = array(); // rows as json result
        
        foreach ($dataRows as $dataRow) { // build single row
            $row = array();
            $row['id'] = $dataRow->getId();
            $cell = array();
            $i = 0;
            $cell[$i++] = '';
            $cell[$i++] = $dataRow->getFeatureSection()->getName();
            $cell[$i++] = $dataRow->getName();
            $cell[$i++] = $dataRow->getExclusive();
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
}
