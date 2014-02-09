<?php

namespace Telegraf\ChecklistBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Telegraf\ChecklistBundle\Document\Item;

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
		if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') )
		{
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
	    
	    // validate
	    $validator = $this->get('validator');
	    $errors = $validator->validate($item);
	    
	    if (count($errors) > 0) {
	    	return new JsonResponse(array('message' => 'Task must be 1 to 10 characters.'), 400);
		}
		
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
		
		// validate and get the item document
	    if (!$item = $this->getValidItem($id)) {
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
		// validate and get the item document
	    if (!$item = $this->getValidItem($id)) {
	      throw $this->createNotFoundException('No checklist item found for id '.$id);
	    }

		// render html response
    	return $this->render('TelegrafChecklistBundle:Default:item.html.twig', array('item' => $item, 'ajax' => true));
	}

	public function editItemAction($id)
	{
		$dm = $this->get('doctrine_mongodb')->getManager();
	    
	    // validate and get the item document
	    if (!$item = $this->getValidItem($id)) {
	      throw $this->createNotFoundException('No checklist item found for id '.$id);
	    }

		// render html response
    	return $this->render('TelegrafChecklistBundle:Default:edit_item.html.twig', array('item' => $item));
	}

	public function updateItemAction(Request $request)
	{
		$id = $request->request->get('id');
	
		// validate and get the item document
	    if (!$item = $this->getValidItem($id)) {
	      throw $this->createNotFoundException('No checklist item found for id '.$id);
	    }

		// get the new text
	    $text = $request->request->get('text');	    
	    $item->setText($text);
	    
	    // validate
	    $validator = $this->get('validator');
	    $errors = $validator->validate($item);
	    
	    if (count($errors) > 0) {
	    	return new JsonResponse(array('message' => 'Task must be 1 to 10 characters.'), 400);
		}
		
		$dm = $this->get('doctrine_mongodb')->getManager();
	    $dm->flush();

		// render html response
    	return $this->render('TelegrafChecklistBundle:Default:item.html.twig', array('item' => $item, 'ajax' => true));
	}

	public function deleteItemAction(Request $request)
	{
		$id = $request->request->get('id');
	
		// validate and get the item document
	    if (!$item = $this->getValidItem($id)) {
	      throw $this->createNotFoundException('No checklist item found for id '.$id);
	    }

		// remove document TODO: soft delete
		$dm = $this->get('doctrine_mongodb')->getManager();
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
	
	public function importItemsAction(Request $request)
	{
		// get an token if there is one
		if ($token = $request->getSession()->get('anon_token'))
		{
    		// add anonymous items to this user account if there are any
    		$numItems = $this->get('doctrine_mongodb')
    			->getManager()
				->createQueryBuilder('TelegrafChecklistBundle:Item')
			    // Find the item
			    ->field('isAnon')->equals(true)
			    ->field('anonToken')->equals($token)
			    ->getQuery()->execute()->count();
			
			// show message?
			if ($numItems > 0)
			{
				// update items
				$this->get('doctrine_mongodb')
	    			->getManager()
					->createQueryBuilder('TelegrafChecklistBundle:Item')
					->update()
					->multiple(true)
				    ->field('isAnon')->equals(true)
				    ->field('anonToken')->equals($token)
				    ->field('user.id')->set($this->getUser()->getId())
				    ->field('isAnon')->set(false)
				    ->field('anonToken')->set(null)
				    ->getQuery()
				    ->execute();
				    
				$this->get('session')->getFlashBag()->add(
		            'success',
		            'We noticed you had '.$numItems.' unsaved task'.( ($numItems > 1)?'s':'').'. We\'ve saved your task'.( ($numItems > 1)?'s':'').' to your account.'
		        );
	        }
		}
		
		return $this->redirect($this->generateUrl('homepage'));
	}
	
	private function getValidItem($id)
	{
		$dm = $this->get('doctrine_mongodb')->getManager();
	    
	    // validate the item id
	    $dm = $this->get('doctrine_mongodb')->getManager();
	    $securityContext = $this->container->get('security.context');
		if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') )
		{
		    $item = $dm->getRepository('TelegrafChecklistBundle:Item')->findOneBy(array('id' => $id, 'user.id' => $this->getUser()->getId()));
		}
		elseif ($token = $this->getRequest()->getSession()->get('anon_token'))
		{
			// get by token
		    $item = $dm->getRepository('TelegrafChecklistBundle:Item')->findOneBy(array('id' => $id, 'isAnon' => true, 'anonToken' => $token));
		}
		
		return $item;
	}
}
