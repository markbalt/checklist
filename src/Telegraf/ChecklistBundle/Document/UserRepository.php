<?php

namespace Telegraf\ChecklistBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserRepository extends DocumentRepository implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
    		echo 'here';
    		exit;
        $q = $this->get('doctrine_mongodb')
			    ->getManager()
	    		->createQueryBuilder('TelegrafChecklistBundle:User')
	        ->field('username')->equals($username)
	        // TODO: add or email = $username
			    ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Can\'t find username "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getDocumentName() === $class
            || is_subclass_of($class, $this->getEntityName());
    }
}