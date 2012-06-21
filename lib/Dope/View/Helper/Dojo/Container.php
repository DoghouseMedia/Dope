<?php

/**
 * Container for Dojo View Helper
 *
 * ZF's Dojo Container does not account for using local modules as themes
 * when using the CDN to load Dojo. We've extended and overriden a few methods
 * in order to check whether a local module exists for each theme/stylesheet.
 */
class Dope_View_Helper_Dojo_Container
extends Zend_Dojo_View_Helper_Dojo_Container
{
	protected $baseUrl = false;
	
	public function getBaseUrl()
	{
		return $this->baseUrl;
	}
	
	public function setBaseUrl($baseUrl)
	{
		$this->baseUrl = $baseUrl;
		return $this;
	}
	
	protected function _getModuleBasePath($moduleName)
	{
		$baseCdn = $this->getCdnBase() . $this->getCdnVersion();
		$baseLocal = $this->getBaseUrl();
		
		if (! $this->useCdn()) {
			return $baseLocal; 
		}
		
		if (in_array($moduleName, $this->getModulePaths())) {
			return $baseLocal;
		} else {
			return $baseCdn;
		}
	}
	
	protected function _renderStylesheets()
	{
		$registeredStylesheets = $this->getStylesheetModules();
		foreach ($registeredStylesheets as $stylesheet) {
			$moduleName    = substr($stylesheet, 0, strpos($stylesheet, '.'));
			$themeName     = substr($stylesheet, strrpos($stylesheet, '.') + 1);
			$stylesheet    = str_replace('.', '/', $stylesheet);
			$stylesheets[] = $this->_getModuleBasePath($moduleName) 
				. '/' . $stylesheet . '/' . $themeName . '.css';
		}

		foreach ($this->getStylesheets() as $stylesheet) {
			$stylesheets[] = $stylesheet;
		}
	
		if ($this->_registerDojoStylesheet) {
			$stylesheets[] = $this->_getModuleBasePath('dojo')
				. '/dojo/resources/dojo.css';
		}
	
		if (empty($stylesheets)) {
			return '';
		}
	
		array_reverse($stylesheets);
		$style = '<style type="text/css">' . PHP_EOL
		. (($this->_isXhtml) ? '<!--' : '<!--') . PHP_EOL;
		foreach ($stylesheets as $stylesheet) {
			$style .= '    @import "' . $stylesheet . '";' . PHP_EOL;
		}
		$style .= (($this->_isXhtml) ? '-->' : '-->') . PHP_EOL
		. '</style>';
	
		return $style;
	}
}