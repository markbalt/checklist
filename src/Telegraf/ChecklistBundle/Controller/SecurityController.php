<?php

namespace Telegraf\ChecklistBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Telegraf\ChecklistBundle\Form\Type\RegistrationType;
use Telegraf\ChecklistBundle\Form\Model\Registration;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends Controller
{
	public function joinAction()
  {
  	// get sign up form
    $form = $this->createForm(new RegistrationType(), new Registration());

    return $this->render('TelegrafChecklistBundle:Security:register.html.twig', array('form' => $form->createView()));
  }
  
  public function createUserAction()
	{
    $dm = $this->get('doctrine_mongodb')->getManager();

		// validate user
    $form = $this->createForm(new RegistrationType(), new Registration());

    $form->bind($this->getRequest());

    if ($form->isValid()) {
      $registration = $form->getData();

			// save document
      $dm->persist($registration->getUser());
      $dm->flush();
      
      // get the UserInterface document
      $user = $registration->getUser();
      
			// main is the name of the firewall for this application, log this user in
      $token = new UsernamePasswordToken($user, null, 'main', array('ROLE_USER'));
			$this->get('security.context')->setToken($token);

      return $this->redirect($this->generateUrl('homepage'));
    }
    
    //return new Response(var_dump($form->getErrorsAsString()));

    return $this->render('TelegrafChecklistBundle:Security:register.html.twig', array('form' => $form->createView()));
	}
	
	public function loginAction(Request $request)
  {
    $session = $request->getSession();

    // get the login error if there is one
    if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR
        );
    } else {
        $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        $session->remove(SecurityContext::AUTHENTICATION_ERROR);
    }

    return $this->render(
        'TelegrafChecklistBundle:Security:login.html.twig',
        array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        )
    );
  }
}
