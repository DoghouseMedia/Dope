<?php

namespace Dope\Entity\Index;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass */
class _Base
{
	/**
	 * @var string $keyword;
	 * @ORM\Column(type="string",length=200)
	 */
	public $keyword;
	
	/**
	 * @var string $field;
	 * @ORM\Column(type="string",length=50)
	 * @ORM\Id
	 */
	public $field;
	
	/**
	 * @var string $position;
	 * @ORM\Column(type="integer",length=10)
	 * @ORM\Id
	 */
	public $position;
	
	/**
	 * @var string $field;
	 * @ORM\Column(type="integer",length=20)
	 * @ORM\Id
	 */
	public $id;
}