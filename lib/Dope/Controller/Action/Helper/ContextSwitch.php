<?php

namespace Dope\Controller\Action\Helper;

class ContextSwitch extends \Zend_Controller_Action_Helper_ContextSwitch
{
	public static $_cachedContext=null;
	
	/**
     * Constructor (overriden)
     * 
     * We've overridden the ContextSwitch helper
     * in order to encapsulate the definition of our contexts.
     * 
     * @todo Reduce the number of contexts used throughout the app.
     *
     * @param  array|\Zend_Config $options
     * @return void
     */
	public function __construct($options = null)
	{
		$this->_contextsRaw = array(
			'ajax' => array(
				'suffix'     => 'html',
				'headers'    => array('Content-Type' => 'text/html'),
				'allowFetch' => true
			),
			'docx' => array(
				'suffix'     => 'docx',
				'headers'    => array('Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
				'allowFetch' => true
			),
			'dojo' => array(
				'suffix'     => 'dojo',
				'headers'    => array('Content-Type' => 'application/json'),
				'allowFetch' => true,
				'callbacks'  => array(
					'init'   => 'initJsonContext',
					'post'   => 'postJsonContext'
				)
			),
			'form' => array(
				'suffix'     => 'form',
				'headers'    => array('Content-Type' => 'text/html'),
				'allowFetch' => true
			),
			'grid' => array(
				'suffix'     => 'html',
				'headers'    => array('Content-Type' => 'text/html'),
				'allowFetch' => false
			),
			'html' => array(
				'suffix'     => 'html',
				'headers'    => array('Content-Type' => 'text/html'),
				'allowFetch' => false
			),
			'json' => array(
				'suffix'     => 'json',
				'headers'    => array('Content-Type' => 'application/json'),
				'allowFetch' => true,
				'callbacks'  => array(
					'init'   => 'initJsonContext',
					'post'   => 'postJsonContext'
				)
			),
	        'rest' => array(
                'suffix'     => 'rest',
                'headers'    => array('Content-Type' => 'application/json'),
	        	'allowFetch' => true,
                'callbacks'  => array(
                    'init'   => 'initJsonContext',
                    'post'   => 'postJsonContext'
                )
	        ),
			'pdf' => array(
				'suffix'     => 'pdf',
				'headers'    => array('Content-Type' => 'application/pdf'),
				'allowFetch' => true
			),
			'profile' => array(
				'suffix'     => 'profile',
				'headers'    => array('Content-Type' => 'text/html'),
				'allowFetch' => true
			),
			'xml' => array(
				'suffix'     => 'xml',
				'headers'    => array('Content-Type' => 'application/xml'),
				'allowFetch' => true
			),
			'csv' => array(
				'suffix'     => 'csv',
				'headers'    => array('Content-Type' => 'text/csv'),
				'allowFetch' => true
			)
		);
		
		parent::__construct(array(
			'contexts' => $this->_contextsRaw
		));
	}
	
	/**
	 * Initialize at start of action controller (overriden)
	 * 
	 * Reset the view script suffix to the original state
	 * 
	 * @return void
	 */
	public function init()
	{
		$this->_viewSuffixOrig = null;
		return parent::init();
	}
	
	/**
	 * Hook into action controller preDispatch() workflow (overriden)
	 * 
	 * We've overriden init() in order to dynamically add
	 * an ActionContext using all defined contexts.
	 *
	 * @return void
	 */
	public function preDispatch()
	{
		$contexts = $this->getActionContexts();
		if (empty($contexts)) {
			$methodNames = get_class_methods($this->getActionController());
			foreach($methodNames as $methodName) {
				if (substr($methodName, -6) == 'Action') {
					$this->addActionContext(
						substr($methodName, 0, -6),
						array_keys($this->getContexts())
					);
				}
			}

			if (! $this->hasCurrentContext()) {
				$context = $this->determineContext();
				if ($context) {
					$this->initContext($context);
				}
			}
		}
		
		return parent::preDispatch();
	}
	
	protected function determineContext()
	{
		$cInt = static::$_cachedContext;
		$cReq = $this->getRequest()->getContextName();

		if ($cReq) return $cReq;
		elseif ($cInt) return $cInt;
		
		/* Default to html */
		return 'html';
	}
	
	protected function hasCurrentContext()
	{
		return (bool) $this->_currentContext;
	}
	
	public function initContext($format = null)
	{
		if ($format) {
			static::$_cachedContext = $format;
		}
		
		return parent::initContext($format);
	}
	
	public function currentContextAllowsRecordFetching()
	{
		if (! $this->hasCurrentContext()) {
			return false;
		}
		
		$currentContext = $this->getCurrentContext();

		return (bool) isset($this->_contextsRaw[$currentContext]['allowFetch'])
			? $this->_contextsRaw[$currentContext]['allowFetch']
			: false;
	}
}