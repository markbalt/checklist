<?php

namespace Telegraf\ChecklistBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Telegraf\ChecklistBundle\Document\Item;
use Telegraf\ChecklistBundle\Form\Type\RegistrationType;
use Telegraf\ChecklistBundle\Form\Model\Registration;

class DefaultController extends Controller
{
	public function indexAction(Request $request)
	{
		// get the appropriate checklist items
		$q = $this->get('doctrine_mongodb')
		    ->getManager()
			->createQueryBuilder('TelegrafChecklistBundle:Item');

		// get the user id if there is one
		$securityContext = $this->container->get('security.context');

		if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') )
		{
		    $q->field('user.id')->equals($this->getUser()->getId());
		}
		elseif ($token = $request->getSession()->get('anon_token'))
		{
			// utilize token
			$q->field('isAnon')->equals(true)
				->field('anonToken')->equals($token);
		}
		else
		{
			// nothing to show, just render html response
			return $this->render('TelegrafChecklistBundle:Default:index.html.twig', array('items' => array()));
		}
		    
	    $items = $q->sort(array(
		    'isTicked'  => 'ASC',
		    'ticked' => 'DESC',
		    'created' => 'DESC',
			))        
	    ->getQuery()
	    ->execute();

		// render html response
		return $this->render('TelegrafChecklistBundle:Default:index.html.twig', array('items' => $items));
	}

	public function createItemAction(Request $request)
	{
		// get the item text
  		$text = $request->request->get('text');

		// create a new checklist item
	    $item = new Item();
	    $item->setText($text);
	    $item->setCreated(new \DateTime());
	    
	    // set the user if there is one
		$securityContext = $this->container->get('security.context');
		if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ) {
		    $item->setUser($this->getUser());
		}
		else
		{
			// get an token if there is one
			if (!$token = $request->getSession()->get('anon_token'))
			{
				// create token
				$token = hash('md5', uniqid("yea", true).$this->container->get('request')->getClientIp());
				$request->getSession()->set('anon_token', $token);
			}

			$item->setIsAnon(1);
			$item->setAnonToken($token);
		}
	    $item->setIsTicked(false);
	    
	    // persist to the database
	    $dm = $this->get('doctrine_mongodb')->getManager();
	    $dm->persist($item);
	    $dm->flush();

		// render html response
    	return $this->render('TelegrafChecklistBundle:Default:item.html.twig', array('item' => $item, 'ajax' => true));
	}

	public function tickItemAction(Request $request)
	{
		$id = $request->request->get('id');
	
		// validate the item id
	    $dm = $this->get('doctrine_mongodb')->getManager();
	    $item = $dm->getRepository('TelegrafChecklistBundle:Item')->find($id);

		// check the item object
	    if (!$item) {
	      throw $this->createNotFoundException('No checklist item found for id '.$id);
	    }
   
		// check the ticked parameter
	   	$isTicked = $request->request->get('is_ticked');
	   	$item->setIsTicked($isTicked == 'true');
	    $item->setTicked( ($isTicked == 'true')?new \DateTime():null);
	    
	    // update the document
	    $dm = $this->get('doctrine_mongodb')->getManager();
	    $dm->persist($item);
	    $dm->flush();
	    
		// create a json response
		$response = array(
			"id" => $item->getId(),
			"ticked" => $item->getTicked(),
		);

		// render html response
    	return $this->render('TelegrafChecklistBundle:Default:item.html.twig', array('item' => $item, 'ajax' => true));
	}

	public function showItemAction($id)
	{
		// TODO: validate
	    $dm = $this->get('doctrine_mongodb')->getManager();
	    $item = $dm->getRepository('TelegrafChecklistBundle:Item')->find($id);

		// check the item object
	    if (!$item) {
	      throw $this->createNotFoundException('No checklist item found for id '.$id);
	    }

		// render html response
    	return $this->render('TelegrafChecklistBundle:Default:item.html.twig', array('item' => $item, 'ajax' => true));
	}

	public function editItemAction($id)
	{
		// TODO: validate
	    $dm = $this->get('doctrine_mongodb')->getManager();
	    $item = $dm->getRepository('TelegrafChecklistBundle:Item')->find($id);

		// check the item object
	    if (!$item) {
	      throw $this->createNotFoundException('No checklist item found for id '.$id);
	    }

		// render html response
    	return $this->render('TelegrafChecklistBundle:Default:edit_item.html.twig', array('item' => $item));
	}

	public function updateItemAction(Request $request)
	{
		$id = $request->request->get('id');
	
		// validate the item id
	    $dm = $this->get('doctrine_mongodb')->getManager();
	    $item = $dm->getRepository('TelegrafChecklistBundle:Item')->find($id);

	    if (!$item) {
	      throw $this->createNotFoundException('No checklist item found for id '.$id);
	    }

	    $id = $request->request->get('id');

	    // TODO: validate the text
	    $text = $request->request->get('text');

	    $item->setText($text);
	    $dm->flush();

		// render html response
    	return $this->render('TelegrafChecklistBundle:Default:item.html.twig', array('item' => $item, 'ajax' => true));
	}

	public function deleteItemAction(Request $request)
	{
		$id = $request->request->get('id');
	
		// validate the item id
	    $dm = $this->get('doctrine_mongodb')->getManager();
	    $item = $dm->getRepository('TelegrafChecklistBundle:Item')->find($id);

			// check the item object
	    if (!$item) {
	      throw $this->createNotFoundException('No checklist item found for id '.$id);
	    }

			// remove document TODO: soft delete
		  $dm->remove($item);
			$dm->flush();

			// create a json response
			$response = array(
				"id" => $item->getId(),
				"text" => $item->getText(),
				"is_ticked" => $item->getIsTicked(),
			);

			// return json
	    return new Response(json_encode($response)); 
	}
}
