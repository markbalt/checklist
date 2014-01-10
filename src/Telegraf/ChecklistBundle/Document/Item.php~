<?php

namespace Telegraf\ChecklistBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Item
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $text;

    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $isTicked;

    /**
     * @MongoDB\Field(type="timestamp")
     */
    protected $createdAt;

    /**
     * @MongoDB\Field(type="timestamp")
     */
    protected $updatedAt;

    /**
     * @MongoDB\Field(type="timestamp")
     */
    protected $deletedAt;

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
     * Set text
     *
     * @param string $text
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text
     *
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set isTicked
     *
     * @param boolean $isTicked
     * @return self
     */
    public function setIsTicked($isTicked)
    {
        $this->isTicked = $isTicked;
        return $this;
    }

    /**
     * Get isTicked
     *
     * @return boolean $isTicked
     */
    public function getIsTicked()
    {
        return $this->isTicked;
    }

    /**
     * Set createdAt
     *
     * @param timestamp $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return timestamp $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param timestamp $updatedAt
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return timestamp $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param timestamp $deletedAt
     * @return self
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return timestamp $deletedAt
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}
