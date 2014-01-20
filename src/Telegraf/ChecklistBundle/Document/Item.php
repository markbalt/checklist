<?php

namespace Telegraf\ChecklistBundle\Document;

use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="Item")
 */
class Item
{
    /**
     * @ODM\Id
     */
    protected $id;

    /**
     * @ODM\Field(type="string")
     */
    protected $text;

    /**
     * @ODM\Field(type="boolean")
     */
    protected $isTicked;

    /**
     * @var date $created
     *
     * @ODM\Date
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @var date $updated
     *
     * @ODM\Date
     * @Gedmo\Timestampable
     */
    private $updated;
    
    /**
     * @var datetime $contentChanged
     *
     * @ODM\Date
     * @Gedmo\Timestampable(on="change", field={"text"})
     */
    private $contentChanged;
    

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

    /**
     * Set created
     *
     * @param date $created
     * @return self
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return date $created
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param date $updated
     * @return self
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * Get updated
     *
     * @return date $updated
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set contentChanged
     *
     * @param date $contentChanged
     * @return self
     */
    public function setContentChanged($contentChanged)
    {
        $this->contentChanged = $contentChanged;
        return $this;
    }

    /**
     * Get contentChanged
     *
     * @return date $contentChanged
     */
    public function getContentChanged()
    {
        return $this->contentChanged;
    }
}
