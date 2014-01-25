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
		$builder->add('username', null);
		$builder->add('email', 'email');
		$builder->add('password', 'repeated', array(
			'first_name' => 'password',
			'second_name' => 'confirm',
			'type' => 'password'
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