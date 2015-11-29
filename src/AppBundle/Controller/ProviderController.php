<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;
use AppBundle\Entity\Equipment;
use AppBundle\Utils;


class ProviderController extends BaseController {
    
    /**
     * @Route("/provider", name="provider")
     * @Route("/provider/profil", name="profil")
     */
    public function profilAction(Request $request) {
        return $this->render('provider/profil.html.twig');
    }

    /**
     * @Route("/provider/einstellungen", name="einstellungen")
     */
    public function einstellungenAction(Request $request) {
        return $this->render('provider/einstellungen.html.twig');
    }
    /**
     * @Route("/provider/equipment-add-1", name="equipment-add-1")
     */
    public function equipmentAdd1Action(Request $request) {
        $form = $this->createFormBuilder()
                ->add('name', 'text')
                ->add('price', 'money')
                ->add('deposit', 'money')
                ->add('value', 'money')
                ->add('priceBuy', 'money')
                ->add('invoice', 'checkbox', array('required' => false))
                ->add('industrial', 'checkbox', array('required' => false))
                ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $data = $form->getData();
            // get subcategory
            $subcat = $this->getDoctrine()->getRepository('AppBundle:Subcategory')->find(3);
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
            
            //$this->get("logger:artur")->info("equipment id: {$eq->getId()})");
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
        
        $form = $this->createFormBuilder()
                ->add('description', 'textarea', array('max_length' => 500))
                ->add('make_sure', 'checkbox')
                ->add('street', 'text')
                ->add('number', 'text')
                ->add('postcode', 'text')
                ->add('place', 'text')
                ->add('accept', 'checkbox')
                ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            // update Equipment object
            $data = $form->getData();
            //$eq = $this->getDoctrine()->getRepository('AppBundle:Equipment')->find($session->get('EquipmentAddId'));
            $eq = $this->getDoctrine()->getRepository('AppBundle:Equipment')->find(110);
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
            foreach ($eqFiles as $file) {
                // copy file
                $fullPath = sprintf("%sequipment\\%s.%s",
                    $this->getParameter('image_storage_dir'),
                    $file[0],
                    $file[2]);
                copy($file[3], $fullPath);
                
                // create object
                $img = new \AppBundle\Entity\Image();
                $img->setUuid($file[0]);
                $img->setName($file[1]);
                $img->setExtension($file[2]);
                $img->setPath('equipment');
                              
                $em->persist($img);
                $em->flush();
                
                $eq->addImage($img);
                $em->flush();
            }
        }
        
        return $this->render('provider\equipment_add_step2.html.twig', array(
            'form' => $form->createView()
        ));
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
                $path = $this->getParameter('image_storage_dir');
                $fullPath = sprintf("%s.%s", $uuid, $file->getClientOriginalExtension());
                $fullPath = sprintf("%s.%s", $uuid, $file->getClientOriginalExtension());
                
                //$file->move(, $fullPath);
                
                $ef = array(
                    $uuid,
                    $file->getClientOriginalName(),
                    $file->getClientOriginalExtension(),
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
        return $this->render('provider\equipment_add_step3.html.twig');
    }
}
