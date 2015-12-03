<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Equipment;
use AppBundle\Entity\Image;
use AppBundle\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Form\EinstellungenType;

class ProviderController extends BaseController {
    
    
    public function userNav2Action(Request $request, $page) {
        $cats = $this->getCategories($request);
        return $this->render('provider/user_nav2.html.twig', array(
            'categories' => $cats,
            'page' => $page
        ));
    }
    
    /**
     * @Route("/provider/subcats/{id}", name="subcat")
     */
    public function subcategoriesAction(Request $request, $id) {
        $subcats = $this->getSubcategories($request, $id);
        $arr = array();
        foreach ($subcats as $s) {
            array_push($arr, array('id' => $s->getId(), 'name' => $s->getName()));
        }
        
        return new JsonResponse($arr);
    }
    
    /**
     * @Route("/provider", name="provider")
     * @Route("/provider/profil", name="profil")
     */
    public function profilAction(Request $request) {
        return $this->render('provider/profil.html.twig');
    }

    /**
     * @Route("user-image", name="user-image")
     */
    public function userImage(Request $request) {                
        $file = $request->files->get('upl');
        if ($file->isValid()) {            
            $session = $request->getSession();
            $eqFiles = $session->get('EquipmentAddFileArray');
            if (count($eqFiles) < 3) {
                $uuid = Utils::getUuid();
                $path = sprintf("%s%s", $this->getParameter('image_storage_dir'), $this->getParameter('user_image_url'));
                $name = sprintf("%s.%s", $uuid, $file->getClientOriginalExtension());
                $fullPath = sprintf("%s%s", $path, $name);
                
                $f = $file->move($path, $name);
                
                $user = $this->getUser();
                $user->setProfileImage($name);
                
            }
        }
        return new Response($status = 200);
    }
    
    /**
     * @Route("/provider/einstellungen", name="einstellungen")
     */
    public function einstellungenAction(Request $request) {
        
        $user = $this->getUser();
        
        //$form = $this->createForm(EinstellungenType::class, $user);
        $form = $this->createForm($this->get('form_einstellungen'), $user);
        
        /*
        $form = $this->createFormBuilder()
                ->add('currentPassword', 'password')
                ->add('newPassword', 'password')
                ->add('repeatedPassword', 'password')
                ->add('name', 'text')
                ->add('surname', 'text')
                
                ->add('phone', 'integer')
                ->add('phonePrefix', 'integer')
                
                ->add('iban', 'text')
                ->add('bic', 'text')             
                ->getForm();*/
        
        return $this->render('provider/einstellungen.html.twig', array(  
            'form' => $form->createView()
        ));
    }
    /**
     * @Route("/provider/equipment-add-1/{subcategoryId}", name="equipment-add-1")
     */
    public function equipmentAdd1Action(Request $request, $subcategoryId) {
        
        // build form
        //<editor-fold>
        $form = $this->createFormBuilder()
                ->add('name', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('max' => 256))
                    )
                ))
                ->add('price', 'money', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Range(array('min' => 0))
                    )
                ))
                ->add('deposit', 'money', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Range(array('min' => 0))
                    )
                ))
                ->add('value', 'money', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Range(array('min' => 0))
                    )
                ))
                ->add('priceBuy', 'money', array(
                    'required' => false,
                    'constraints' => array(
                        new Range(array('min' => 0))
                    )
                ))
                ->add('invoice', 'checkbox', array('required' => false))
                ->add('industrial', 'checkbox', array('required' => false))
                ->getForm();
        //</editor-fold>
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $data = $form->getData();
            // get subcategory
            $subcat = $this->getDoctrine()->getRepository('AppBundle:Subcategory')->find($subcategoryId);
            $user = $this->getUser();
            // map fields, TODO: move to Equipment's method
            //<editor-fold> map fields            
            $eq = new Equipment();
            $eq->setName($data['name']);
            $eq->setUser($user);
            $eq->setSubcategory($subcat);
            $eq->setPrice($data['price']);
            $eq->setValue($data['value']);
            $eq->setDeposit($data['deposit']);
            $eq->setPriceBuy($data['priceBuy']);
            $eq->setInvoice($data['invoice']);
            $eq->setIndustrial($data['industrial']);
            //</editor-fold>
            // save to db
            $em = $this->getDoctrine()->getManager();
            $em->persist($eq);
            $em->flush();
            
            $session = $request->getSession();
            $session->set('EquipmentAddId', $eq->getId());
            return $this->redirectToRoute('equipment-add-2');
        }
        
        return $this->render('provider\equipment_add_step1.html.twig', array(
            'form' => $form->createView()
        ));
    }
    /**
     * @Route("/provider/equipment-add-2", name="equipment-add-2")
     */
    public function equipmentAdd2Action(Request $request) {
        $session = $request->getSession();
        if ($request->getMethod() == "GET") {
            $session->set('EquipmentAddFileArray', array());
        }
        else {
            $this->fileCount = count($session->get('EquipmentAddFileArray'));
        }
        
        // validation form
        //<editor-fold>
        $form = $this->createFormBuilder(null, array(
                'constraints' => array(
                    new Callback(array($this, 'validateImages'))
                )
            ))
            ->add('description', 'textarea', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('max' => 500))
                )
            ))
            ->add('make_sure', 'checkbox', array(
                'required' => false,
                'constraints' => array(
                    new Callback(array($this, 'validateMakeSure'))                    
                )
            ))
            ->add('street', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('max' => 128))
                )
            ))
            ->add('number', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('max' => 16))
                )
            ))
            ->add('postcode', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('max' => 4)),
                    new Regex(array('pattern' => '/^\d{4}$/', 'message' => 'Please fill in a valid postal code'))
                )
            ))
            ->add('place', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('max' => 128))
                )
            ))
            ->add('accept', 'checkbox', array(
                'required' => false,
                'constraints' => array(
                    new Callback(array($this, 'validateAccept'))
                )
            ))
            ->getForm();
        //</editor-fold>
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            // update Equipment object
            $data = $form->getData();
            $eq = $this->getDoctrine()->getRepository('AppBundle:Equipment')->find($session->get('EquipmentAddId'));
            // map fields
            //<editor-fold>
            $eq->setDescription($data['description']);
            $eq->setAddrStreet($data['street']);
            $eq->setAddrNumber($data['number']);
            $eq->setAddrPostcode($data['postcode']);
            $eq->setAddrPlace($data['place']);            
            //</editor-fold>
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            // store images
            $eqFiles = $session->get('EquipmentAddFileArray');
            $this->handleImages($eqFiles, $eq, $em);
            
            // clean up
            $session->remove('EquipmentAddFileArray');            
            $this->fileCount = null;
            
            return $this->redirectToRoute('equipment-add-3');
        }
        
        return $this->render('provider\equipment_add_step2.html.twig', array(
            'form' => $form->createView()
        ));
    }
    private $fileCount = null;
    public function validateAccept($value, ExecutionContextInterface $context) {
        if (!$value) {
            $context->buildViolation('You must check this box')->atPath('accept')->addViolation();
        }            
    }
    public function validateMakeSure($value, ExecutionContextInterface $context) {
        if (!$value) {
            $context->buildViolation('You must check this box')->atPath('make_sure')->addViolation();
        }            
    }
    public function validateImages($data, ExecutionContextInterface $context) {
        if ($this->fileCount == null || $this->fileCount == 0) {
            $context->buildViolation('Please upload at least one image')->addViolation();
        }
    }
    private function handleImages($eqFiles, $eq, $em) {
        foreach ($eqFiles as $file) {
            // store the original, and image itself
            $origFullPath = sprintf("%sequipment\\original\\%s.%s",
                $this->getParameter('image_storage_dir'),
                $file[0],
                $file[2]);
            $imgFullPath = sprintf("%sequipment\\%s.%s",
                $this->getParameter('image_storage_dir'),
                $file[0],
                $file[2]);
            rename($file[3], $origFullPath);
            
            // check image size
            $imgInfo = getimagesize($origFullPath);
            $ow = $imgInfo[0]; // original width
            $oh = $imgInfo[1]; // original height
            $r = $ow / $oh; // ratio
            $nw = $ow; // new width
            $nh = $oh; // new height
            $scale = False;
            
            if ($r > 1) {
                if ($ow > 1024) {
                    $nw = 1024;
                    $m = $nw / $ow; // multiplier
                    $nh = $oh * $m;
                    $scale = True;
                }
            }
            else {
                if ($oh > 768) {
                    $nh = 768;
                    $m = $nh / $oh; // multiplier
                    $nw = $ow * $m;
                    $scale = True;
                }
            }
            
            // scale the image
            if ($scale) {
                if ($file[2] == 'png') {
                    $img = imagecreatefrompng($origFullPath);
                }
                else {
                    $img = imagecreatefromjpeg($origFullPath);
                }
                $sc = imagescale($img, intval(round($nw)), intval(round($nh)), IMG_BICUBIC_FIXED);
                if ($file[2] == 'png') {
                    imagepng($sc, $imgFullPath);
                }
                else {
                    imagejpeg($sc, $imgFullPath);
                }
            }
            else {
                copy($origFullPath, $imgFullPath);
            }        

            // store entry in database
            $img = new Image();
            $img->setUuid($file[0]);
            $img->setName($file[1]);
            $img->setExtension($file[2]);
            $img->setPath('equipment');
            $img->setOriginalPath('equipment\\original');

            $em->persist($img);
            $em->flush();

            $eq->addImage($img);
            $em->flush();
        }
        
    }
    
    /**
     * @Route("equipment-image", name="equipment-image")
     */
    public function equipmentImage(Request $request) {        
        $log = $this->get('monolog.logger.artur');
        $file = $request->files->get('upl');
        if ($file->isValid()) {
            $log->info("received a file: {$file->getClientOriginalName()} ({$file->getClientSize()} bytes)");    
            $session = $request->getSession();
            $eqFiles = $session->get('EquipmentAddFileArray');
            if (count($eqFiles) < 3) {
                $uuid = Utils::getUuid();
                $path = sprintf("%stemp\\", $this->getParameter('image_storage_dir'));
                $name = sprintf("%s.%s", $uuid, $file->getClientOriginalExtension());
                $fullPath = sprintf("%s%s", $path, $name);
                
                $f = $file->move($path, $name);
                
                $ef = array(
                    $uuid,
                    $file->getClientOriginalName(),
                    strtolower($file->getClientOriginalExtension()),
                    $fullPath
                );
                
                array_push($eqFiles, $ef);
                $log->info("\taccepted");
                $session->set('EquipmentAddFileArray', $eqFiles);
            }
        }
        return new Response($status = 200);
    }
    
    /**
     * @Route("/provider/equipment-add-3", name="equipment-add-3")
     */
    public function equipmentAdd3Action(Request $request) {
        $session = $request->getSession();
        //$id = $ $session->get('EquipmentAddId');
        $id = 118; // CRITICAL: remove this
        $eq = $this->getDoctrine()->getRepository('AppBundle:Equipment')->find($id);
        
        return $this->render('provider\equipment_add_step3.html.twig', array(
            'subcategory' => $eq->getSubcategory(),
            'featureSectionRepo' => $this->getDoctrine()->getRepository('AppBundle:FeatureSection')
        ));
    }
}