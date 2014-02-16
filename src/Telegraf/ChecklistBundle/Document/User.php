<?php

namespace Telegraf\ChecklistBundle\Document;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="users")
 */
class User extends BaseUser
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $hideCompleted = 1;
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set hideCompleted
     *
     * @param boolean $hideCompleted
     * @return self
     */
    public function setHideCompleted($hideCompleted)
    {
        $this->hideCompleted = $hideCompleted;
        return $this;
    }

    /**
     * Get hideCompleted
     *
     * @return boolean $hideCompleted
     */
    public function getHideCompleted()
    {
        return $this->hideCompleted;
    }
}
