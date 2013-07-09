<?php

namespace Dope\Controller\Action;

use Dope\Entity;

use Dope\Controller\Action,
    Dope\Controller\Data,
    Dope\Entity\Search,
    Dope\Doctrine,
    Dope\Config\Helper as Config,
    Doctrine\ORM\Query as Query,
    Zend_Controller_Request_Abstract as Request,
    Zend_Controller_Response_Abstract as Response,
	Zend_Controller_Action_HelperBroker as HelperBroker;
        

abstract class Model
extends Action
{
	/**
	 * @var \Dope\Doctrine\ORM\EntityRepository
	 */
	protected $entityRepository;
	
	/**
	 * @var \Dope\Form\Entity
	 */
	protected $entityForm;
	
	/**
	 * 
	 * @param \Zend_Controller_Request_Abstract $request
	 * @param \Zend_Controller_Response_Abstract $response
	 * @param array $invokeArgs
	 */
	public function __construct(Request $request, Response $response, array $invokeArgs = array())
	{
		/* Dojo helpers */
		HelperBroker::addHelper(new Helper\AutoCompleteDojo());
		
		/* Not modified since */
		HelperBroker::addHelper(new Helper\NotModifiedSince());
		
		/* Saved Search */
		HelperBroker::addHelper(new Helper\SavedSearch());
		
		/* Call parent constructor */
		parent::__construct($request, $response, $invokeArgs);
	}
	
	public function __call($methodName, $args)
	{
		$action = $this->getRequest()->getActionName();
		
		if ($action == 'index') {
			return $this->_forward('rest');
		}
		
		elseif (ctype_digit($action)) {
			$this->getRequest()->setParam('id', $action);
			return $this->_forward('rest');
		}
		
		/* Default to parent implementation */
		else {
			return parent::__call($methodName, $args);
		}
	}
	
	/**
	 * BREAD Browse
	 */
	public function browseAction()
	{
		/* Display */
		if (! $this->_helper->contextSwitch()->currentContextAllowsRecordFetching()) {
			/* View params (Data) */
			$this->view->data = $this->getData();
			
			/* Load and configure new/add form */
			if ($this->getEntityDefinition()->getBrowseOptions()->getShowForm()) {
			    $this->view->form = $this->getEntityForm();
			}
			
			return;
		}
		
		/* Search */
		$search = $this->getEntitySearch(Search::MODE_WITH_PAGINATION);
		
		/* HTTP Content-Range */
		$this->_helper->http->setContentRange(
			$search->getRangeStart(),
			$search->getRangeEnd(),
			$search->getCount()
		);
		
		/* Respond */
		switch ($this->_helper->contextSwitch()->getCurrentContext()) {
			case 'dojo': $this->_helper->autoCompleteDojo($search->getRecords()); break;
			case 'rest':
			case 'json': $this->_helper->json($search->getRecords()); break;
			case 'xml': $this->_helper->xml($search->getRecords()); break;
			case 'csv': $this->_helper->csv($search->getRecords()); break;
	
			case 'profile':
				$this->view->debug = $search->getDebug();
				// do NOT break !

			case 'html':
				$this->view->records = $search->getRecords();
				$this->view->recordsTotalCount = $search->getCount();
				$this->view->paginatorIds = $search->getIds();
				break;
		}
	}
	
	/**
	 * Get an executed Search object
	 * 
	 * @param string $mode
	 * @return \Dope\Entity\Search
	 */
	protected function getEntitySearch($mode)
	{
		$search = new Search();
		$search->setData($this->getData());
		$search->setEntityRepository($this->getEntityRepository());
		
		if (! $this->_helper->savedSearch($search)) {
		    $search->setMode($mode);
		    $search->setType($this->getData()->query
	            ? new Search\Type\Query()
	            : new Search\Type\Plain()
		    );
		}
		
		$search->execute();
		$search->improve();
		
		return $search;
	}
	
	/**
	 * BREAD Read
	 */
	public function readAction()
	{
		/* Get record */
		$entity = $this->getEntityRepository()->find(
			(int) $this->getRequest()->getParam('id')
		);

		/* Assign to view */
		switch($this->getHelper('ContextSwitch')->getCurrentContext()) {
			case 'json': $this->_helper->json($entity->toArray()); break;
			case 'rest': $this->_helper->json($entity->toArray(false)); break;
			case 'xml': $this->_helper->xml($entity->toArray()); break;
			case 'csv': $this->_helper->csv($entity->toArray()); break;
			case 'pdf': $this->_helper->pdf($entity); break;
			case 'docx': $this->_helper->docx($entity); break;
			default:
			case 'html': $this->view->record = $entity; break;
		}
		
		$this->view->form = $this->getEntityForm(array(), $entity);
	}
	
	/**
	 * BREAD Add
	 */
	public function addAction()
	{
		/* Form */
		$form = $this->getEntityForm();
		
		/* Process */
		if ($this->getRequest()->isPost()) {
			/* Form was submitted. Get data */
			$data = $this->getData($form->getValues(true));
		
			if ($form->isValid($this->getRequest()->getParams())) {
				$className = $this->getEntityRepository()->getClassName();
				
				/** @var \Dope\Entity $entity */
				$entity = new $className();
				$entity->saveFromArray((array) $data->getParams());
		
				/* Send response or redirect, based on context */
				$this->respondOk($entity, $form);
			}
			else {
				/* Form has errors */
				$errors = $form->getMessages();
			}
		}
		else { 
			/* Form was not submitted */
			$data = $this->getData();
		}
	
		/* Errors */
		if (isset($errors) AND (is_array($errors) OR $errors instanceof \Traversable)) {
			$this->view->errors = $errors;
		}
	
		/* View */
		$this->view->data = $data;
		$this->view->form = $form;
	}
	
	/**
	 * BREAD Delete
	 *
	 * @todo In it's current state, this method is subject to CSRF
	 *
	 * @return void
	 */
	public function deleteAction()
	{
		$id = (int) $this->_getParam('id');
	
		$record = $this->getEntityRepository()->find($id);
		
		if ($record instanceof Entity) {
			$record->delete();
		}
		
		$this->respondOk($record);
	}
	
	/**
	 * BREAD Edit
	 *
	 * @return void
	 */
	public function editAction()
	{
		/* Entity */
		$entity = $this->getEntityRepository()->find(
			(int) $this->getRequest()->getParam('id', false)
		);
		
		/* Form */
		$form = $this->getEntityForm(array(), $entity);
		if ($this->getRequest()->getParam('subformname')) {
			$subformName = $this->getRequest()->getParam('subformname');
			$form = $form->getSubForm($subformName)->toForm();
			$form->addSubmitButton()->setLabel('Save');
		}

		/* Process */
		if ($this->getRequest()->isPost() OR $this->getRequest()->isPut()) {
			/* Form was submitted. Get data */
			$data = $this->getData($form->getValues(true));
	
			if ($form->isValid($this->getRequest()->getParams())) {
				/* Form is valid. Update entity with form values */
				
				$result = $entity->saveFromArray((array) $data->getParams());
	
				if ($result) {
					/* Entity saved */
					$this->respondOk($entity, $form);
				}
				else {
					/* Entity has errors */
					$errors = $entity->getErrorStack();
				}
			}
			else {
				/* Form has errors */
				$errors = $form->getMessages();
			}
		}
		else {
			/* Form was not submitted */
			$data = $this->getData();
		}
	
		/* Errors */
		if (isset($errors) AND (is_array($errors) || $errors instanceof \Traversable)) {
			// This is for passing the errors through json
			$this->view->errors = $errors;
		}
	
		/* View */
		$this->view->data = $data;
		$this->view->record = $entity;
		$this->view->form = $form;
	}
	
	public function countAction()
	{
		/* Search */
		$search = $this->getEntitySearch(Search::MODE_COUNT_ONLY);
	
		switch($this->_helper->contextSwitch()->getCurrentContext()) {
			case 'ajax':
			case 'dojo':
			case 'json': $this->_helper->json($search->getCount()); break;
			case 'xml': $this->_helper->xml($search->getCount()); break;
			case 'html': $this->view->recordsTotalCount = $search->getCount(); break;
		}
	}
	
	/**
	 * Get entity definition
	 * @return \Dope\Entity\Definition
	 */
	protected function getEntityDefinition()
	{
		return new \Dope\Entity\Definition(
			$this->getModelClassName((array) $this->getData()->getParams())
		);
	}
	
	/**
	 * autocomplete Action
	 *
	 * Used by dropdown Selects for populating them based on various criteria.
	 */
	public function autocompleteAction($returnMatches = false)
	{
		/* Try 304 if conditions are met (check date fields) */
		if ($this->getEntityRepository()->hasColumn('created') AND $this->getEntityRepository()->hasColumn('updated')) {
			/*
			 * Get max created/updated dates and try 304
			 */
			$select = \Dope\Doctrine::getEntityManager()->createQueryBuilder()
				->from($this->getEntityRepository()->getClassName(), 't')
				->select('MAX(t.created) AS max_created, MAX(t.updated) AS max_updated');
	
			$maxDates = $select->getQuery()->getArrayResult();
			$maxCreated = strtotime($maxDates[0]['max_created']);
			$maxUpdated = strtotime($maxDates[0]['max_updated']);
				
			/* This will exit with 304 if conditions are met */
			$this->_helper->notModifiedSince(max($maxCreated,$maxUpdated));
		}
		
		/*
		 * Define some vars
		 */
		
		$matches = array();
		
		/*
		 * Doctrine uses a lot of memory. With 128M, I couldn't load more than
		 * 5000 objects/rows and dojo autocomplete widgets (Combobox/FilteringSelect)
		 * do not seem to implement a start/count out of the box.
		 *
		 * To overcome that problem, models must implement a @Dope\Stringify annotation.
		 * Doing so, we can limit the fields Doctrine fetches from DB, and switch from
		 * object hydration to array hydration.
		 */
		
		$columns = $this->getEntityDefinition()->getToStringColumnNames();
		// Prepend 'id' to column list
		if (! in_array('id', $columns)) {
		    array_unshift($columns, 'id');
		}
	
		/*
		 * Search
		 */
		
		$search = new Search();
		$search->setEntityRepository($this->getEntityRepository());
		$search->setData($this->getData());
		$search->execute();
		$search->select($search->prefix($columns));
		
		
		$records = $search->getQueryBuilder()
			->getQuery()
			->getResult(Query::HYDRATE_ARRAY);
	
		/** @var array $record */
		foreach ($records as $record) {
			$values = array();
			$id = array_shift($record);

			// Flatten data
			$values = $this->getEntityRepository()->flatten($record, true);
			
			// Remove empty values
			$values = array_filter($values, 'strlen');
						
			$matches[$id] = count($values)
				? join(' ', $values)
				: $id;
		}
	
		if ($returnMatches) {
			return $matches;
		}
		
		switch ($this->_helper->contextSwitch()->getCurrentContext()) {
			case 'dojo': $this->_helper->autoCompleteDojo($matches); break;
			case 'json': $this->_helper->json($matches); break;
			case 'xml': $this->_helper->xml($matches); break;
			case 'html': $this->view->matches = $matches; break;
		}
	}
	
	/**
	 * Update Action (old Toggle action).
	 *
	 * This is mainly called ajaxily by toggle buttons.
	 *
	 * With some heavy refactoring, we should merge this in with the postAction()
	 * Then, we should move both to editAction() and use context and httpMethod switching to determine what to do
	 */
	public function updateAction()
	{
		/* Entity */
		$entity = $this->getEntityRepository()->find(
			(int) $this->getRequest()->getParam('id', false)
		);
	
		$entity->saveFromArray((array) $this->getData()->getParams());

		switch($this->_helper->contextSwitch()->getCurrentContext()) {
			case 'dojo':
			case 'json':
			case 'rest':
			case 'xml':
			case 'html': $this->view->status = true; break;
				
			default: $this->respondOk($entity); break;
		}
	}
	
	public function unlinkAction()
	{
		$this->view->status = false;
	
		if ($this->getRequest()->isPost()) {
			/* Entity */
			$entity = $this->getEntityRepository()->find(
				(int) $this->getRequest()->getParam('id', false)
			);
				
			$entity->unlinkFromArray((array) $this->getData()->getParams());
			$this->view->status = true;
		}
	}
	
	/**
	 * Replicate/Clone an entity
	 * @return \Dope\Entity
	 */
	public function replicateAction()
	{
		$replica = $this->getEntityRepository()
			->find((int) $this->getRequest()->getParam('id', false))
			->replicate()
			->save();
		$this->respondOk($replica);
		return $replica;
	}
	
	/**
	 * REST implementation
	 *
	 * We are NOT using the Zend_Rest_* classes for multiple reasons:
	 *  1. We are still refactoring (Feb 2012) and it would be too big a change for now
	 *  2. We prefer mapping the HTTP methods to BREAD actions in our controllers
	 * When the time comes, we will implement our own route by extending Zend_Rest_Route
	 * and Zend_Rest_Controller, and mapping the HTTP methods to BREAD controller methods.
	 *
	 * @throws Exception
	 */
	public function restAction()
	{
		/*
		 * This should be matching url parts (using the router)
		 * but since we're still refactoring, the old codebase sets the 'id'
		 * parameter in call() if it's present.
		 *
		 * This would be invoked by a URL like '/:controller/:id'
		 */
		if ($this->getRequest()->getParam('id')) {
			switch(strtoupper($this->getRequest()->getMethod())) {
			    default:
				case 'GET': $this->_forward('read'); break;
				case 'PUT': $this->_forward('edit'); break;
				case 'POST': $this->_forward('update'); break;
				case 'DELETE': $this->_forward('delete'); break;
	
				case 'HEAD':
				case 'TRACE':
				case 'OPTIONS':
					throw new \Exception("Not implemented");
					break;
			}
		}
		/* This would be invoked by a URL like '/:controller' */
		else {
			switch(strtoupper($this->getRequest()->getMethod())) {
			    default:
				case 'GET': $this->_forward('browse'); break;
				case 'POST': $this->_forward('add'); break;
				
				case 'PUT':
				case 'DELETE':
			    case 'HEAD':
			    case 'TRACE':
			    case 'OPTIONS':
					throw new \Exception("Not implemented");
					break;
			}
		}
	}
	
	/**
	 * Controller action for managing the drag and drop interface,
	 * used for assigning multiple items from a list to a record (many to many relationship)
	 *
	 * @return void
	 */
	public function dndAction()
	{
		foreach($this->getData()->getParams() as $key => $val) {
			$md = \Dope\Doctrine::getEntityManager()->getClassMetadata(
				$this->getModelClassName((array) $this->getData()->getParams())
			);
	
			if ($md->hasAssociation($key)) {
				$relatedAlias = $key;
				$relatedModelAlias = strtolower($key);
				$relatedId = (int) $val;
				break;
			}
			elseif ($md->hasAssociation($key . 's')) {
				$relatedAlias = $key . 's';
				$relatedModelAlias = strtolower($key);
				$relatedId = (int) $val;
				break;
			}
		}
	
		if (!isset($relatedAlias) OR !isset($relatedId) OR !isset($relatedModelAlias)) {
			throw new \Exception("No relationship info!");
		}
	
		$this->view->relatedAlias = $relatedAlias;
		$this->view->relatedModelAlias = $relatedModelAlias;
		$this->view->relatedId = $relatedId;
	
	
		$search = new Search();
		$search->setEntityRepository($this->getEntityRepository());
		$search->setData($this->getData());
		$search->execute();
		$search->select($search->prefix(array('id')));
		
		$_recordIdsUsed = $search->getQueryBuilder()
			->getQuery()
			->getResult(Query::HYDRATE_SCALAR);
		
		$recordIdsUsed = array();
	
		foreach ($_recordIdsUsed as $_recordIdUsed) {
			$recordIdsUsed[] = $_recordIdUsed['id'];
		}

		$this->view->recordsUsed = array();
		$this->view->recordsFree = array();
	
		$search = new Search();
		$search->setEntityRepository($this->getEntityRepository());
		$search->setData($this->getData());
		$search->execute();
		
		$recordsAll = $search->getQueryBuilder()
			->getQuery()
			->getResult();
		
		foreach ($recordsAll as $record) {
			if (in_array($record->id, $recordIdsUsed)) {
				$this->view->recordsUsed[] = $record;
			}
			else {
				$this->view->recordsFree[] = $record;
			}
		}
	}
	
	public function dndaddAction()
	{
		$this->updateAction();
	}
	
	public function dnddelAction()
	{
		$this->unlinkAction();
	}
	
	/* ---------------| HELPER methods from here on |--------------- */
	
	public function getModelAlias()
	{
		$classMetadata = \Dope\Doctrine::getEntityManager()->getClassMetadata(
			$this->getModelClassName()
		);
	
		return strtolower(str_replace(
			$classMetadata->namespace . '\\',
			'',
			$classMetadata->rootEntityName
		));
	}
	
	public function getModelClassName($params=array(), $forceUseOwnClassName=false)
	{
		if (! preg_match('|^(.*?)Controller$|', get_class($this), $matches)) {
			throw new \Exception('Could not parse controller class name from ' . get_class($this));
		}
	
		$className = Config::getOption('appnamespace') . '\\Entity\\' . $matches[1];
		$classMetadata = \Dope\Doctrine::getEntityManager()->getClassMetadata($className);
	
		$useOwnClassName = ($forceUseOwnClassName
			OR !isset($classMetadata->discriminatorMap)
			OR empty($classMetadata->discriminatorMap)
		);
	
		if ($useOwnClassName) {
			return $className;
		}
	
		/* Polymorphic entity choosing */
	
		$classesToTry = array_values($classMetadata->discriminatorMap);
		$results = array();
	
		foreach ($classesToTry as $classToTry) {
			$mappings = \Dope\Doctrine::getEntityManager()->getClassMetadata($classToTry);
				
			$properties = array_merge(
				$mappings->associationMappings,
				$mappings->fieldMappings
			);
				
			$propertiesInCommon = array_intersect_key($properties, $params);
				
			$countPropertiesInCommon = count($propertiesInCommon);
			$countProperties = count($properties);
				
			$score = round(1000 * ($countPropertiesInCommon / $countProperties)) * $countProperties;
				
			\Dope\Log::console("Class $classToTry scored $score ($countPropertiesInCommon/$countProperties)");
				
			if (!isset($results[$score])) {
				$results[$score] = array();
			}
				
			$results[$score][] = $classToTry;
		}
	
		krsort($results);
	
		\Dope\Log::console($results);
		\Dope\Log::console('We would choose class ' . current(current($results)));
	
		switch (count(current($results))) {
			case 1:
				return current(current($results));
				break;
			case 0:
				throw new \Exception("Could find any entity matching all parameters");
				break;
			default:
				throw new \Exception("Found more than one entity matching all parameters");
				break;
		}
	}
	
	/**
	 * Helper method: get model manager(doctrine calls the manager the table, duh...).
	 * @todo Not anymore. It's now called EntityRepository so we should rename this.
	 *
	 * @return \Dope\Doctrine\ORM\EntityRepository
	 */
	public function getEntityRepository()
	{
		if (! $this->entityRepository instanceof \Dope\Doctrine\ORM\EntityRepository) {
			if ($this->getRequest()->isPost() && $this->getData()->getParam($this->getFormUniqueId())) {
				$params = $this->getData()->getParam($this->getFormUniqueId());
			} else {
				$params = $this->getData()->getParams();
			}
	
			$this->entityRepository = \Dope\Doctrine::getRepository(
				$this->getModelClassName((array) $params)
			);
		}
	
		return $this->entityRepository;
	}
	
	/**
	 * Helper method: go grab the form corresponding to this class
	 *
	 * @return \Dope\Form\Entity
	 */
	protected function getEntityForm(array $options=array(), \Dope\Entity $entity=null)
	{
		if ($this->entityForm instanceof \Dope\Form\Entity) {
			$this->entityForm->setOptions($options);
		}
		else {
			$this->entityForm = $this->getEntityRepository()->getForm($options)
				->setController($this)
				->setEntity($entity)
				->configure();
		}
	
		return $this->entityForm;
	}
	
	protected function respondOk(\Dope\Entity $entity=null, \Dope\Form\Entity $form=null)
	{
		switch($this->_helper->contextSwitch()->getCurrentContext()) {
		    case 'rest':
			case 'json':
	
				switch(strtoupper($this->getRequest()->getMethod())) {
					case 'PUT':
					case 'POST':
					case 'DELETE':
					default:
	
						$this->view->status = true;
	
						if ($entity instanceof \Dope\Entity) {
							$this->view->id = $entity->id;
							$this->view->controller = $this->getModelAlias();
							$this->view->title = (string) $entity;
							$this->view->messages = array(); //$model->getMessages();
							
							if (isset($entity->updated)) {
							    $this->view->updated = $entity->updated;
							}
						}
	
// 						if ($this->getData()->getParam('tab')) {
// 							$this->view->tab = $this->getData()->getParam('tab');
								
// 							if ($this->getData()->getParam('parent_tab')) {
// 								$this->view->parent_tab = $this->getData()->getParam('parent_tab');
// 							}
// 						}
	
						break;
				}
	
				break;
			case 'ajax':
			case 'dojo':
			case 'xml':
	
				switch(strtoupper($this->getRequest()->getMethod())) {
					case 'PUT':
					case 'POST':
					case 'DELETE':
	
						$this->getResponse()->setBody( $entity instanceof \Dope\Entity ? $entity->id : true );
						break;
							
					case 'GET':
					default:
						throw new \Exception("Not implemented");
						break;
				}
	
				break;
					
			case 'html':
			default:
	
				$tryData = $form instanceof \Dope\Form\Entity ? $form->getValues(true) : array();
	
				$module = '';
				$params = array();
	
				$url = $this->_getParam('sender_url', false, $tryData);
				$controller = $this->_getParam('sender', false, $tryData);
				$id = $this->_getParam($controller, false, $tryData);
	
				if ($url) {
					/* Redirect */
					$this->_helper->redirector->setGotoUrl(urldecode($url));
				}
	
				if ($controller) {
					if ($id) {
						$action = $id;
					}
					elseif ($entity instanceof \Dope\Entity) {
						$action = $entity->id;
					}
					else {
						$action = 'browse';
					}
				}
				else {
					$controller = $this->getRequest()->getControllerName(); //'error';
					$action = $this->getRequest()->getActionName(); //'sender';
						
					if ($entity instanceof \Dope\Entity) {
						$action = $entity->id;
					}
				}
	
				/* Redirect */
				$this->_helper->redirector(
					$action,
					$controller,
					$module,
					$params
				);
	
				break;
		}
	}
}
