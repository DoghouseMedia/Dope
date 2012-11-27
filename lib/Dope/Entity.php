<?php

namespace Dope;
use 
	Dope\Entity\Behavior,
	Doctrine\ORM\Mapping as ORM,
	Dope\Doctrine\ORM\Mapping as Dope;

abstract class Entity
extends Entity\_Base
{
	/**
	 * @var integer $id
	 * @ORM\Column(type="integer",nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 */
	protected $user;
	
	/**
	 * @var datetime $created;
	 * @ORM\Column(type="datetime",length=25)
	 */
	protected $created;
	
	/**
	 * @var datetime $updated;
	 * @ORM\Column(type="datetime",length=25)
	 */
	protected $updated;
	
	/**
	 * @var boolean $deleted
	 * @ORM\Column(type="boolean")
	 */
	protected $deleted = false;
	
	/**
	 * To string
	 * @return string
	 */
	public function __toString()
	{
		if (count($this->getDefinition()->getToStringColumnNames())) {
			$values = array();
			foreach ($this->getDefinition()->getToStringColumnNames() as $columnName) {
				$values[] = $this->{$columnName};
			}
			return (string) join(' ', $values);
		}
		else {
			return ucfirst($this->getEntityKey()) . ' ' . $this->id;
		}
	}
	
	public function prePersist()
	{
		/* Populate 'created' column */
		if (! $this->created) {
			$this->created = new \DateTime();
		}
		
		/* Populate 'updated' column */
		$this->updated = new \DateTime();
		
		/* Populate User column */
		if (! $this->user AND \Dope\Auth\Service::hasUser()) {
			$this->user = \Dope\Auth\Service::getUser();
		}
		
		return parent::prePersist();
	}
	
	public function preUpdate() {
		/* Populate 'updated' column */
		$this->updated = new \DateTime();
		
		return parent::preUpdate();
	}
}
