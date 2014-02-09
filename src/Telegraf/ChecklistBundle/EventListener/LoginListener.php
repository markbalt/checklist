<?php

namespace Telegraf\ChecklistBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginListener
{
    private $router;
    
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onSecurityInteractiveLogin',
        );
    }
    
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
    	$request = $event->getRequest();
    	$request->request->set('_target_path', 'import_items');
    }
}