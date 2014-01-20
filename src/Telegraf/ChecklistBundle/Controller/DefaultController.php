<?php

namespace Telegraf\ChecklistBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Telegraf\ChecklistBundle\Document\Item;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
  public function indexAction()
  {
  	// get the appropriate checklist items
  	$items = $this->get('doctrine_mongodb')
        ->getRepository('TelegrafChecklistBundle:Item')
        ->findAll();

      return $this->render('TelegrafChecklistBundle:Default:index.html.twig', array('items' => $items));
  }

  public function createItemAction(Request $request)
	{
		
		// check for "made up" item
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
    //$item->setCreated(new \DateTime());
    
    $dm = $this->get('doctrine_mongodb')->getManager();
    $dm->persist($item);
    $dm->flush();

    return $this->render('TelegrafChecklistBundle:Default:item.html.twig', array('item' => $item, 'ajax' => true));
	}

	public function tickItemAction(Request $request)
	{
		$id = $request->request->get('id');
	
    $dm = $this->get('doctrine_mongodb')->getManager();
    $item = $dm->getRepository('TelegrafChecklistBundle:Item')->find($id);

    if (!$item) {
      throw $this->createNotFoundException('No checklist item found for id '.$id);
    }
   
   	$isTicked = $request->request->get('is_ticked');
    $item->setIsTicked($isTicked == 'true');
    
    $dm = $this->get('doctrine_mongodb')->getManager();
    $dm->persist($item);
    $dm->flush();

		$response = array(
			"id" => $item->getId(),
			"is_ticked" => $item->getIsTicked(),
		);

		// return json
    return new Response(json_encode($response)); 
	}

	public function updateItemAction($id)
	{
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

    $dm = $this->get('doctrine_mongodb')->getManager();
    $item = $dm->getRepository('TelegrafChecklistBundle:Item')->find($id);

    if (!$item) {
        throw $this->createNotFoundException('No checklist item found for id '.$id);
    }

	  $dm->remove($item);
		$dm->flush();

		$response = array(
			"id" => $item->getId(),
			"text" => $item->getText(),
			"is_ticked" => $item->getIsTicked(),
		);

    return new Response(json_encode($response)); 
	}
	
	public function getInventedItem()
	{
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
