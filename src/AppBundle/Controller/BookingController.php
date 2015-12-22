<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Inquiry;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swift_Message;
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
        $inquiry = array(
            'from' => $from,
            'to' => $to,
            'days' => $days,
            'price' => $price,
            'whereabouts' => $eq->getWhereaboutsAsString()
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
            // map fields & save
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
            // prepare params
            $provider = $eq->getUser();
            $url = $request->getSchemeAndHttpHost() .
                    $this->generateUrl('inquiry-response', array('id' => $inq->getId()));
            $from = array($this->getParameter('mailer_fromemail') => $this->getParameter('mailer_fromname'));
            $emailHtml = $this->renderView('emails/inquiry.html.twig', array(
                'mailer_image_url_prefix' => $this->getParameter('mailer_image_url_prefix'),
                'provider' => $provider,
                'inquiry' => $inq,
                'equipment' => $eq,
                'url' => $url
            ));
            $message = Swift_Message::newInstance()
                ->setSubject('Du hast soeben eine Anfrage erhalten')
                ->setFrom($from)
                ->setTo($provider->getEmail())
                ->setBody($emailHtml, 'text/html');
            $this->get('mailer')->send($message);
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

    /**
     * @Route("inquiry-response/{id}", name="inquiry-response")
     */
    public function inquiryResponseAction(Request $request, $id) {
        
    }
    
}
