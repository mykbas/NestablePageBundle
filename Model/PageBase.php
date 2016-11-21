<?php

namespace Mykbas\NestablePageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

abstract class PageBase
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    protected $slug;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=true)
     */
    protected $isPublished;

    /**
     * @var int
     *
     * @ORM\Column(name="sequence", type="integer", nullable=true)
     */
    protected $sequence;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    protected $modified;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="Mykbas\NestablePageBundle\Model\PageBase", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")}
     * @ORM\OrderBy({"sequence" = "ASC"})
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Mykbas\NestablePageBundle\Model\PageBase", mappedBy="parent")
     * @ORM\OrderBy({"sequence" = "ASC"})
     */
    protected $children;

    /**
     * @ORM\OneToMany(targetEntity="Mykbas\NestablePageBundle\Model\PageMetaBase", mappedBy="page", cascade={"persist"})
     */
    protected $pageMetas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->pageMetas = new ArrayCollection();
    }

    /**
     * convert obj to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->slug;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        // update the modified time
        $this->setModified(new \DateTime());

        // for newly created entries
        if ($this->getCreated() == null) {
            $this->setCreated(new \DateTime('now'));
        }

        $this->created = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return PageBase
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     * @return PageBase
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return bool
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set sequence
     *
     * @param integer $sequence
     * @return PageBase
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence
     *
     * @return int
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return PageBase
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return PageBase
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set parent
     *
     * @param \Mykbas\NestablePageBundle\Model\PageBase $parent
     * @return PageBase
     */
    public function setParent(\Mykbas\NestablePageBundle\Model\PageBase $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Mykbas\NestablePageBundle\Model\PageBase
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param \Mykbas\NestablePageBundle\Model\PageBase $child
     * @return PageBase
     */
    public function addChild(\Mykbas\NestablePageBundle\Model\PageBase $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \Mykbas\NestablePageBundle\Model\PageBase $child
     */
    public function removeChild(\Mykbas\NestablePageBundle\Model\PageBase $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add pageMeta
     *
     * @param PageMetaBase $pageMeta
     *
     * @return PageBase
     */
    public function addPageMeta(PageMetaBase $pageMeta)
    {
        $this->pageMetas[] = $pageMeta;

        return $this;
    }

    /**
     * Remove pageMeta
     *
     * @param PageMetaBase $pageMeta
     */
    public function removePageMeta(PageMetaBase $pageMeta)
    {
        $this->pageMetas->removeElement($pageMeta);
    }

    /**
     * Get pageMetas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPageMetas()
    {
        return $this->pageMetas;
    }
}
