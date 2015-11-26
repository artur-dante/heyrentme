<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Equipment;


class ProviderController extends BaseController {
    
    /**
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
            // map fields, TODO: move to Equipment's method
            //<editor-fold> map fields
            $eq = new Equipment();
            $eq->setName('New item');
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
        
        
        return $this->render('provider\equipment_add_step2.html.twig');
    }
    /**
     * @Route("/provider/equipment-add-3", name="equipment-add-3")
     */
    public function equipmentAdd3Action(Request $request) {
        return $this->render('provider\equipment_add_step3.html.twig');
    }
}
