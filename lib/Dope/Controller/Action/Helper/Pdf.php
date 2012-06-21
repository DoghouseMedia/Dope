<?php

namespace Dope\Controller\Action\Helper;

use Dope\Entity,
	Zend_Controller_Action_HelperBroker as HelperBroker;


class Pdf extends \Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Suppress exit when sendPdf() called
	 * @var boolean
	 */
	public $suppressExit = false;

	/**
	 * Create PDF response
	 *
	 * @param  Dope\Entity $entity
     * @param  boolean $keepLayouts
     * @throws \Zend_Controller_Action_Helper_Xml
     * @return string
     */
	public function getPdf(Entity $entity, $keepLayouts = false)
	{
		if (!$keepLayouts) {
			HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
		}
		
		return $entity->getPrinterTemplate()->toPdf();
	}

	public function sendPdf(Entity $entity, $keepLayouts = false)
	{
		$response = $this->getResponse()
			->setHeader('Content-Type', 'application/pdf')
			->setHeader('Content-Disposition', 'inline')
//		 	; filename='
//		 	. $entity->getMailMergeDocumentTemplateName($this->getRequest()) . '.pdf'
//		 )
			->setBody($this->getPdf($entity, $keepLayouts));

		if (! $this->suppressExit) {
			$response->sendResponse();
			exit;
		}

		return $this;
	}

	/**
	 * Strategy pattern: call helper as helper broker method
	 *
	 * @param  \Dope\Entity $entity
	 * @param  boolean $sendNow
	 * @param  boolean $keepLayouts
	 * @return string|void
	 */
	public function direct(Entity $entity, $sendNow = true, $keepLayouts = false)
	{
		if ($sendNow) {
			return $this->sendPdf($entity, $keepLayouts);
		}
		
		return $this->getPdf($entity, $keepLayouts);
	}
}
