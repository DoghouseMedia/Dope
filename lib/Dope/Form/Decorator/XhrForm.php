<?php

require_once 'Dope/Form/Decorator/Form.php';

class Dope_Form_Decorator_XhrForm
extends Dope_Form_Decorator_Form
{
	/**
	 * Default view helper
	 * @var string
	 */
	protected $_helper = 'xhrForm';
}