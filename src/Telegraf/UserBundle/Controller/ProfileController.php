<?php

namespace Telegraf\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ProfileController as BaseController;

class ProfileController extends BaseController
{
    /**
     * Show the user
     */
    public function showAction()
    {
    	$response = $this->forward('FOSUserBundle:Profile:edit');
	
	    return $response;
	    
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Profile:show.html.'.$this->container->getParameter('fos_user.template.engine'), array('user' => $user));
    }
}
