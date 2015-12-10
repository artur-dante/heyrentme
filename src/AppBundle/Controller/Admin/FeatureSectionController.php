<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\FeatureSection;
use AppBundle\Entity\Image;
use AppBundle\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;



class FeatureSectionController extends BaseAdminController {
    
    /**
     * 
     * @Route("/admin/feature-section", name="admin_feature_section_list")
     */
    public function indexAction() {
        
        return $this->render('admin/featureSection/index.html.twig');
    }
    
    /**
     * 
     * @Route("/admin/feature-section/new/{subcategoryId}", name="admin_feature_section_new")
     */
    public function newAction(Request $request, $subcategoryId) {
        $featureSection = new FeatureSection();
        $subcategory = 
        
        $form = $this->createFormBuilder($featureSection)
                ->add('subcategory', 'entity', array(
                  'class' => 'AppBundle:Subcategory',
                  'property' => 'name',
                  'data' => $subcategory
                  ))
                ->add('name', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('max' => 256))
                    )
                ))
                ->add('slug', 'text', array(
                    'constraints' => array(
                        // TODO: check for uniqueness of slug (category + subcategory; copy from blog)
                        new NotBlank(),
                        new Length(array('max' => 256)),
                        new Regex(array(
                            'pattern' => '/^[a-z][-a-z0-9]*$/',
                            'htmlPattern' => '/^[a-z][-a-z0-9]*$/',
                            'message' => 'This is not a valid slug'
                        ))
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
            //check if there is file
            $file = $request->files->get('upl');
            
            $em = $this->getDoctrine()->getManager();
            
            if ($file != null && $file->isValid()) {
                
                // save file
                $uuid = Utils::getUuid();
                $image_storage_dir = $this->getParameter('image_storage_dir');
                
                $destDir = 
                    $image_storage_dir .
                    DIRECTORY_SEPARATOR .
                    'category' .
                    DIRECTORY_SEPARATOR;
                $destFilename = sprintf("%s.%s", $uuid, $file->getClientOriginalExtension());
                
                $file->move($destDir, $destFilename);
                
                // create object
                $img = new Image();
                $img->setUuid($uuid);
                $img->setName($destFilename);
                $img->setExtension($file->getClientOriginalExtension());
                $img->setOriginalPath($file->getClientOriginalName());
                $img->setPath('category');
                              
                $em->persist($img);
                $em->flush();
                
                $featureSection->setImage($img);
            }
            
            // save to db
            
            $em->persist($featureSection);
            $em->flush();

            return $this->redirectToRoute("admin_feature_section_list");
        }
        
        
        return $this->render('admin/featureSection/new.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * 
     * @Route("/admin/feature-section/edit/{id}", name="admin_feature_section_edit")
     */
    public function editAction(Request $request, $id) {
        $featureSection = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);

        if (!$featureSection) {
            throw $this->createNotFoundException('No category found for id '.$id);
        }
        
        $form = $this->createFormBuilder($featureSection)
                ->add('id', 'hidden')
                ->add('name', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('max' => 256))
                    )
                ))
                ->add('slug', 'text', array(
                    'constraints' => array(
                        // TODO: check for uniqueness of slug (category + subcategory; copy from blog)
                        new NotBlank(),
                        new Length(array('max' => 256)),
                        new Regex(array(
                            'pattern' => '/^[a-z][-a-z0-9]*$/',
                            'htmlPattern' => '/^[a-z][-a-z0-9]*$/',
                            'message' => 'This is not a valid slug'
                        ))
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
            
            //check if there is file
            $file = $request->files->get('upl');
            
            $em = $this->getDoctrine()->getManager();
            
            if ($file != null && $file->isValid()) {
                
                //remove old Image (both file from filesystem and entity from db)
                $this->getDoctrine()->getRepository('AppBundle:Category')->removeImage($featureSection, $this->getParameter('image_storage_dir'));
                
                
                // save file
                $uuid = Utils::getUuid();
                $image_storage_dir = $this->getParameter('image_storage_dir');
                
                //$destDir = sprintf("%scategory\\",$image_storage_dir);                
                $destDir = 
                        $image_storage_dir .
                        DIRECTORY_SEPARATOR .
                        'category' .
                        DIRECTORY_SEPARATOR;
                $destFilename = sprintf("%s.%s", $uuid, $file->getClientOriginalExtension());
                
                $file->move($destDir, $destFilename);
                
                // create object
                $img = new Image();
                $img->setUuid($uuid);
                $img->setName($destFilename);
                $img->setExtension($file->getClientOriginalExtension());
                $img->setOriginalPath($file->getClientOriginalName());
                $img->setPath('category');
                              
                $em->persist($img);
                $em->flush();
                
                $featureSection->setImage($img);
            }            
            
            // save to db

            $em->persist($featureSection);
            $em->flush();

            return $this->redirectToRoute("admin_feature_section_list");
        }
        
        
        return $this->render('admin/featureSection/edit.html.twig', array(
            'form' => $form->createView(),
            'category' => $featureSection
        ));
    }
    
    /**
     * 
     * @Route("/admin/feature-section/delete/{id}", name="admin_feature_section_delete")
     */
    public function deleteAction(Request $request, $id) {
        $featureSection = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);

        if (!$featureSection) {
            throw $this->createNotFoundException('No category found for id '.$id);
        }
        
        //remove old Image (both file from filesystem and entity from db)
        $this->getDoctrine()->getRepository('AppBundle:Category')->removeImage($featureSection, $this->getParameter('image_storage_dir'));
        
        //remove subcategories
        $this->getDoctrine()->getRepository('AppBundle:Category')->removeSubcategoriesFromCategory($featureSection);
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($featureSection);
        $em->flush();
        return $this->redirectToRoute("admin_feature_section_list");
    }
    
    
    /**
     * @Route("/admin/feature-section/jsondata", name="admin_feature_section_jsondata")
     */
    public function JsonData(Request $request) {  
        $sortColumn = $request->get('sidx');
        $sortDirection = $request->get('sord');
        $pageSize = $request->get('rows');
        $page = $request->get('page');
        $fSubcategory = $request->get('s_position');
        $fName = $request->get('fs_name');
        $callback = $request->get('callback');
        
        $repo = $this->getDoctrine()->getRepository('AppBundle:FeatureSection');
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
            $cell[$i++] = $dataRow->getSubcategory()->getName();
            $cell[$i++] = $dataRow->getName();
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
