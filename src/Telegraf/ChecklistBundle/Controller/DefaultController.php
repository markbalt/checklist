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
  	$items = $this->get('doctrine_mongodb')
        ->getRepository('TelegrafChecklistBundle:Item')
        ->findAll();

      return $this->render('TelegrafChecklistBundle:Default:index.html.twig', array('items' => $items));
  }

  public function createItemAction(Request $request)
	{
  	$text = $request->request->get('text');

    $item = new Item();
    $item->setText($text);
    
    $dm = $this->get('doctrine_mongodb')->getManager();
    $dm->persist($item);
    $dm->flush();

    // prepare the response
		$response = array(
			"id" => $item->getId(),
			"text" => $item->getText(),
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

		// prepare the response
		$response = array(
			"id" => $item->getId(),
			"msg" => "Deleted item."
		);

    return new Response(json_encode($response)); 
	}
}
