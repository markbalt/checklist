<?php

namespace Telegraf\ChecklistBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Telegraf\ChecklistBundle\Form\Type\RegistrationType;
use Telegraf\ChecklistBundle\Form\Model\Registration;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
	public function joinAction()
  {
    $form = $this->createForm(new RegistrationType(), new Registration());

    return $this->render('TelegrafChecklistBundle:Security:register.html.twig', array('form' => $form->createView()));
  }
  
  public function createUserAction()
	{
    $dm = $this->get('doctrine_mongodb')->getManager();

    $form = $this->createForm(new RegistrationType(), new Registration());

    $form->bind($this->getRequest());

    if ($form->isValid()) {
      $registration = $form->getData();

      $dm->persist($registration->getUser());
      $dm->flush();

      return $this->redirect($this->generateUrl('homepage'));
    }

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
