<?php

namespace Telegraf\ChecklistBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Telegraf\ChecklistBundle\Document\Item;

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

    public function createItemAction($text)
	{
	    $item = new Item();
	    $item->setText($text);
	    
	    $dm = $this->get('doctrine_mongodb')->getManager();
	    $dm->persist($item);
	    $dm->flush();

	    // return new Response('Created item id '.$item->getText());
	    return $this->redirect($this->generateUrl('homepage'));
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

	public function deleteItemAction($id)
	{
	    $dm = $this->get('doctrine_mongodb')->getManager();
	    $item = $dm->getRepository('TelegrafChecklistBundle:Item')->find($id);

	    if (!$item) {
	        throw $this->createNotFoundException('No checklist item found for id '.$id);
	    }

	    $dm->remove($product);
		$dm->flush();

	    return $this->redirect($this->generateUrl('homepage'));
	}
}
