<?php

namespace Telegraf\ChecklistBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Telegraf\ChecklistBundle\Document\Item;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Telegraf\ChecklistBundle\Form\Type\RegistrationType;
use Telegraf\ChecklistBundle\Form\Model\Registration;

class DefaultController extends Controller
{
  public function indexAction()
  {
  	// get the appropriate checklist items
  	$items = $this->get('doctrine_mongodb')
		    ->getManager()
    		->createQueryBuilder('TelegrafChecklistBundle:Item')
        //->field('user')->equals('foo')
        ->sort(array(
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
		// "make up" an item?
		$invented = $request->request->get('invented');
		if ($invented == 'true') {
			$text = $this->getInventedItem();
		} else {
		
			// get the item text
	  	$text = $request->request->get('text');
		}

		// create a new checklist item
    $item = new Item();
    $item->setText($text);
    // TODO: this should be handled by behaviors
    $item->setCreated(new \DateTime());
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

		// return json
    return new Response(json_encode($response)); 
	}

	public function updateItemAction($id)
	{
		// TODO:
    $dm = $this->get('doctrine_mongodb')->getManager();
    $item = $dm->getRepository('TelegrafChecklistBundle:Item')->find($id);

    if (!$item) {
      throw $this->createNotFoundException('No checklist item found for id '.$id);
    }

    $item->setName('New product name!');
    $dm->flush();

    return $this->redirect($this->generateUrl('homepage'));
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
	
	public function getInventedItem()
	{
		// TODO: move to yml
		$arr = array(
			'Match socks',
			'Paint a self portrait',
			'Build a house',
			'Take dog to the groomers',
			'Read War and Peace',
		);
		
		return $arr[array_rand($arr)];
	}
}
