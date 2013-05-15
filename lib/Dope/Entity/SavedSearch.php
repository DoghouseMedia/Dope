<?php

namespace Dope\Entity;

use Doctrine\ORM\Mapping as ORM;

class SavedSearch extends \Dope\Entity 
{
	/**
	 * @var \Dope\Controller\Data $data
	 * @ORM\Column(type="object", nullable=false)
	 */
	protected $data;
	
	/**
	 * @var array $results
	 * @ORM\Column(type="array", nullable=false)
	 */
	protected $results;
}


