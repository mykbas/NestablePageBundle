<?php

namespace Mykbas\NestablePageBundle\PageTestBundle\Entity;

use Mykbas\NestablePageBundle\Model\PageBase;
use Doctrine\ORM\Mapping as ORM;

/**
 * PageMeta
 *
 * @ORM\Table(name="pagetest")
 * @ORM\Entity(repositoryClass="Mykbas\NestablePageBundle\PageTestBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Page extends PageBase
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="test_hidden", type="string", length=255, nullable=true)
     */
    protected $testHidden;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set test_field
     *
     * @param string $slug
     * @return Page
     */
    public function setTestHidden($testHidden)
    {
        $this->testHidden = $testHidden;

        return $this;
    }

    /**
     * Get test_field
     *
     * @return string
     */
    public function getTestHidden()
    {
        return $this->testHidden;
    }
}
