<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Inquiry;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class BookingController extends BaseController {

    /**
     * @Route("inquiry/{id}/{dateFrom}/{dateTo}", name="inquiry")
     */
    public function inquiryAction(Request $request, $id, $dateFrom, $dateTo) {
        $loggedIn = $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'); // user logged in
        $eq = $this->getDoctrineRepo('AppBundle:Equipment')->find($id);
        
        // init/calculate inquiry details
        //<editor-fold>
        $from = DateTime::createFromFormat('Y-m-d\TH:i+', $dateFrom);
        $to = DateTime::createFromFormat('Y-m-d\TH:i+', $dateTo);
        $days = $to->diff($from)->days;
        $price = ($days + 1) * $eq->getPrice();
        $addr = sprintf('%s, %s %s', $eq->getAddrStreet(), $eq->getAddrPostcode(), $eq->getAddrPlace());
        $inquiry = array(
            'from' => $from,
            'to' => $to,
            'days' => $days,
            'price' => $price,
            'whereabouts' => $addr
        );
        //</editor-fold>
        
        // build form
        //<editor-fold>
        $url = $this->generateUrl('inquiry', array(
            'id' => $id,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ));
        
        $builder = $this->createFormBuilder()
            ->setAction($url);
        if (!$loggedIn) {
            $builder->add('name', 'text', array(
                'attr' => array (
                    'max-length' => 128
                ),
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('max' => 128))
                )
            ))
            ->add('email', 'email', array(
                'attr' => array (
                    'max-length' => 128
                ),
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('max' => 128)),
                    new Email(array('checkHost' => true))
                )
            ));
        }
        $builder->add('message', 'textarea', array(
                'constraints' => array(
                    new NotBlank(),
                 )
            ));
        $form = $builder->getForm();
        //</editor-fold>
       
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $data = $form->getData();
            $inq = new Inquiry();
            // map fields
            //<editor-fold>
            if (!$loggedIn) {
                $inq->setName($data['name']);
                $inq->setEmail($data['email']);
            }
            else {
                $inq->setUser($this->getUser());                
            }
            $inq->setMessage($data['message']);
            $inq->setFromAt($inquiry['from']);
            $inq->setToAt($inquiry['to']);
            $inq->setPrice($inquiry['price']);
            $inq->setDeposit($eq->getDeposit());
            $inq->setPriceBuy($eq->getPriceBuy());
            
            // TODO: filter out any contact data from the message (phone, email)
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($inq);
            $em->flush();
            //</editor-fold>
            
            // send email
            //<editor-fold>
            
            //</editor-fold>
            
            return new JsonResponse(array('status' => 'ok'));
        }
        
        return $this->render('booking/inquiry.html.twig', array(
            'loggedIn' => $loggedIn,
            'inquiry' => $inquiry,
            'form' => $form->createView(),
            'equipment' => $eq           
        ));
    }
}
