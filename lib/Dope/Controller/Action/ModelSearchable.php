<?php 

namespace Dope\Controller\Action;

use \Dope\Controller\Action,
    \Dope\Config\Helper as Config;

abstract class ModelSearchable
extends Action\Model
{
	public function browseAction($returnCollection=false)
	{
		$mixed = parent::browseAction($returnCollection);
		
		$this->view->search_form = $this->getSearchForm()
			->populate((array) $this->view->data);

		return $mixed;
	}
	
	/**
	 * Helper method: go grab the form corresponding search form to this class
	 *
	 * @return \Dope\Form\Entity\Search
	 */
	protected function getSearchForm(array $options=array())
	{
		return $this->getEntityRepository()
			->getForm(
				$options,
				'\\' . Config::getOption('appnamespace') . '\Form\Entity\Search',
				null,
				'\Dope\Form\Entity\Search'
			)
			->setController($this)
			->configure();
	}
}
