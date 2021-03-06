<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Booking;
use AppBundle\Entity\DiscountCode;
use AppBundle\Entity\Inquiry;
use AppBundle\Utils\Utils;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swift_Message;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ExecutionContextInterface;

class BookingController extends BaseController {

    /**
     * @Route("/booking/inquiry/{id}/{dateFrom}/{dateTo}", name="booking-inquiry")
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
        $url = $this->generateUrl('booking-inquiry', array(
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
            $inq->setEquipment($eq);
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
                    $this->generateUrl('booking-response', array('id' => $inq->getId()));
            $from = array($this->getParameter('mailer_fromemail') => $this->getParameter('mailer_fromname'));
            $emailHtml = $this->renderView('Emails/inquiry.html.twig', array(
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
     * @Route("booking/response/{id}", name="booking-response")
     */
    public function responseAction(Request $request, $id) {
        $inq = $this->getDoctrineRepo('AppBundle:Inquiry')->find($id);
        $eq = $inq->getEquipment();
        
        // security check
        if ($this->getUser()->getId() !== $eq->getUser()->getId()) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }
        // sanity check
        if ($inq->getAccepted() !== null) { // already responded
            return new Response('', Response::HTTP_FORBIDDEN);
        }
        
        if ($request->getMethod() === "POST") {
            $acc = intval($request->request->get('accept'));
            $msg = $request->request->get('message');
            
            $inq->setAccepted($acc);
            $inq->setResponse($msg);
            if ($acc > 0) {
                $inq->setUuid(Utils::getUuid());
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            // send email
            //<editor-fold>
            $provider = $eq->getUser();
            if ($inq->getUser() !== null) {
                $email = $inq->getUser()->getEmail();
            }
            else {
                $email = $inq->getEmail();
            }
            $url = $request->getSchemeAndHttpHost() .
                    $this->generateUrl('booking-confirmation', array('uuid' => $inq->getUuid()));
            $from = array($this->getParameter('mailer_fromemail') => $this->getParameter('mailer_fromname'));
            $emailHtml = $this->renderView('Emails/response.html.twig', array(
                'mailer_image_url_prefix' => $this->getParameter('mailer_image_url_prefix'),
                'provider' => $provider,
                'inquiry' => $inq,
                'equipment' => $eq,
                'url' => $url
            ));
            $message = Swift_Message::newInstance()
                ->setSubject('Du hast soeben eine Anfrage erhalten')
                ->setFrom($from)
                ->setTo($email)
                ->setBody($emailHtml, 'text/html');
            $this->get('mailer')->send($message);
            //</editor-fold>
            
            return $this->redirectToRoute('dashboard');
        }
        
        return $this->render('booking/response.html.twig', array(
            'equipment' => $eq,
            'inquiry' => $inq
        ));
    }
    
    /**
     * @Route("/booking/confirmation/{uuid}", name="booking-confirmation")
     */
    public function confirmationAction(Request $request, $uuid) {
        $inq = $this->getDoctrineRepo('AppBundle:Inquiry')->findOneByUuid($uuid);

        // sanity check
        if ($inq == null) {
            throw $this->createNotFoundException();
        }
        if ($inq->getBooking() !== null) { // booking already confirmed
            return new Response('', 403); // TODO: display a nice message to the user?
        }
        
        $data = array('uuid' => $uuid);
        
        $form = $this->createFormBuilder($data, array(
                    'constraints' => array(
                        new Callback(array($this, 'validateDiscountCode'))
                    )
                ))
                ->add('agree', 'checkbox', array(
                    'required' => false,
                    'constraints' => array(
                        new NotBlank()
                    )
                ))
                ->add('discountCode', 'text', array(
                    'required' => false
                ))
                ->add('uuid', 'hidden')
                ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {            
            $data = $form->getData();
            
            $bk = new Booking();
            $bk->setInquiry($inq);
            $bk->setStatus(Booking::STATUS_BOOKED);
            
            // save booking
            $em = $this->getDoctrine()->getManager();
            $em->persist($bk);
            $em->flush();
            
            // calcualte discount
            if (!empty($data['discountCode'])) {
                $dcode = $this->getDoctrineRepo('AppBundle:DiscountCode')->findOneByCode($data['discountCode']);
                $dcode->setStatus(DiscountCode::STATUS_USED);
                $dcode->setInquiry($inq);
                $p = $inq->getPrice() - 5;
                $inq->setPrice($p);
                $this->getDoctrine()->getManager()->flush();
            }
            
            
            // send email to provider & user
            //<editor-fold>
            $provider = $inq->getEquipment()->getUser();
            $from = array($this->getParameter('mailer_fromemail') => $this->getParameter('mailer_fromname'));
            
            $emailHtml = $this->renderView('Emails/booking_confirmation_provider.html.twig', array(
                'mailer_image_url_prefix' => $this->getParameter('mailer_image_url_prefix'),
                'provider' => $provider,
                'inquiry' => $inq,
                'equipment' => $inq->getEquipment()
            ));
            $message = Swift_Message::newInstance()
                ->setSubject('Du hast soeben eine Anfrage erhalten')
                ->setFrom($from)
                ->setTo($provider->getEmail())
                ->setBody($emailHtml, 'text/html');
            $this->get('mailer')->send($message);

            if ($inq->getUser() !== null) {
                $email = $inq->getUser()->getEmail();
            }
            else {
                $email = $inq->getEmail();
            }
            $emailHtml = $this->renderView('Emails/booking_confirmation_user.html.twig', array(
                'mailer_image_url_prefix' => $this->getParameter('mailer_image_url_prefix'),
                'provider' => $provider,
                'inquiry' => $inq,
                'equipment' => $inq->getEquipment()
            ));
            $message = Swift_Message::newInstance()
                ->setSubject('Du hast soeben eine Anfrage erhalten')
                ->setFrom($from)
                ->setTo($email)
                ->setBody($emailHtml, 'text/html');
            $this->get('mailer')->send($message);
            //</editor-fold>
        
            return $this->redirectToRoute('rentme');
        }
        
        return $this->render('booking/confirmation.html.twig', array(
            'inquiry' => $inq,
            'form' => $form->createView()
        ));
    }

    public function validateDiscountCode($data, ExecutionContextInterface $context) {
        if (empty($data['discountCode'])) {
            return;
        }
        
        $dcode = $this->getDoctrineRepo('AppBundle:DiscountCode')->findOneByCode($data['discountCode']);
        if ($dcode === null || $dcode->getStatus() != DiscountCode::STATUS_ASSIGNED) {
            $context->buildViolation('This is not a valid discount code')->atPath('discountCode')->addViolation();
            return;
        }
        
        $inq = $this->getDoctrineRepo('AppBundle:Inquiry')->findOneByUuid($data['uuid']);
        $user = $inq->getUser();
        if ($user === null || $user->getId() !== $dcode->getUser()->getId()) {
            $context->buildViolation('This is not a valid discount code')->atPath('discountCode')->addViolation();
        }
    }
    /**
     * @Route("/booking/check-code/{uuid}/{code}", name="booking-check-code")
     */
    public function checkCodeAction(Request $request, $uuid, $code) {
        /*
         *  We're checking combinaion of uuid and code, 
         * so it's not impossible to brute-force-hack discount codes
         */
        $dcode = $this->getDoctrineRepo('AppBundle:DiscountCode')->findOneByCode($code);
        if ($dcode === null || $dcode->getStatus() !== DiscountCode::STATUS_ASSIGNED) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }
        
        $inq = $this->getDoctrineRepo('AppBundle:Inquiry')->findOneByUuid($uuid);
        $user = $inq->getUser();
        
        // security
        if ($user === null || $user->getId() !== $dcode->getUser()->getId()) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }
        return new JsonResponse(array('result' => 'ok'));
    }
    
}
