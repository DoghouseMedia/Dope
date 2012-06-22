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
		$contexts = array(
			'ajax' => array(
				'suffix'	=> 'html',
				'headers'   => array('Content-Type' => 'text/html'),
			),
			'docx' => array(
				'suffix'	=> 'docx',
				'headers'   => array('Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
			),
			'dojo' => array(
				'suffix'	=> 'dojo',
				'headers'   => array('Content-Type' => 'application/json'),
				'callbacks' => array(
					'init' => 'initJsonContext',
					'post' => 'postJsonContext'
				)
			),
			'form' => array(
				'suffix'	=> 'form',
				'headers'   => array('Content-Type' => 'text/html'),
			),
			'grid' => array(
					'suffix'	=> 'html',
					'headers'   => array('Content-Type' => 'text/html'),
			),
			'html' => array(
				'suffix'	=> 'html',
				'headers'   => array('Content-Type' => 'text/html'),
			),
			'json' => array(
				'suffix'	=> 'json',
				'headers'   => array('Content-Type' => 'application/json'),
				'callbacks' => array(
					'init' => 'initJsonContext',
					'post' => 'postJsonContext'
				)
			),
			'pdf' => array(
				'suffix'	=> 'pdf',
				'headers'   => array('Content-Type' => 'application/pdf'),
			),
			'profile' => array(
				'suffix'	=> 'profile',
				'headers'   => array('Content-Type' => 'text/html'),
			),
			'xml' => array(
				'suffix'	=> 'xml',
				'headers'   => array('Content-Type' => 'application/xml'),
			)
		);
		
		parent::__construct(array(
			'contexts' => $contexts	
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
	 * an ActionContext using the current action and all defined contexts.
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
		$cExt = $this->getContextFromRequestExtension();
		$cHead = $this->getContextFromAcceptHeader();

		if ($cExt) return $cExt;
		elseif ($cInt) return $cInt;
		elseif ($cHead) return $cHead;
		
		/* Default to html */
		return 'html';
	}
	
	public function getContextFromRequestExtension()
	{
		if (strpos($this->getRequest()->getActionName(), '.') !== false) {
			$parts = explode('.', $this->getRequest()->getActionName());
			$context = array_pop($parts);
			$this->getRequest()->setActionName(join('.', $parts));
			return $context;
		}
		
		return false;
	}
	
	/**
	 * 
	 */
	public function getContextFromAcceptHeader()
	{
		/* Remove whitespace and explode on commas */
		$acceptParts = explode(',',
			str_replace(' ', '', $this->getRequest()->getHeader('Accept')
		));
	
		/* Test for json */
		if (in_array('application/json', $acceptParts)) {
			return 'json';
		}
	
		/* Test for dojo */
		if (in_array('application/x-dojo-json', $acceptParts)) {
			return 'dojo';
		}
		
		return false;
	}
	
	/**
	 * 
	 */
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
}