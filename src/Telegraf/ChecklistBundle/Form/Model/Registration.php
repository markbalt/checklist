<?php

namespace Telegraf\ChecklistBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Telegraf\ChecklistBundle\Document\User;

class Registration
{
    /**
     * @Assert\Type(type="Telegraf\ChecklistBundle\Document\User")
     */
    protected $user;

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}