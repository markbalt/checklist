<?php

namespace Telegraf\ChecklistBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('username', 'text', array(
			'attr' => array('class' => 'form-control')
		));
		$builder->add('email', 'email', array(
			'attr' => array('class' => 'form-control')
		));
		$builder->add('password', 'repeated', array(
			'options' => array('attr' => array('class' => 'form-control')),
			'first_name' => 'password',
			'second_name' => 'confirm',
			'type' => 'password',
			'first_options'  => array('label' => 'Password'),
    	'second_options' => array('label' => 'Password again'),
		));
		$builder->add('submit', 'submit', array(
		  'attr' => array('class' => 'btn btn-default'),
		));
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
    $resolver->setDefaults(array(
	  	'data_class' => 'Telegraf\ChecklistBundle\Document\User',
    ));
	}
	
	public function getName()
	{
	  return 'user';
	}
}