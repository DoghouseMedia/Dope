<?php

namespace Dope\Controller\Action\Helper;

use Dope\Entity,
	Zend_Controller_Action_HelperBroker as HelperBroker;

class Docx extends \Zend_Controller_Action_Helper_Abstract
{
    /**
     * Suppress exit when sendDocx() called
     * @var boolean
     */
    public $suppressExit = false;

    /**
     * Create Docx response
     *
     * @param  \Dope\Entity $entity
     * @throws \Zend_Controller_Action_Helper_Xml
     * @return string
     */
    public function getDocx(Entity $entity)
    {
        HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);

        $template = $entity->getPrinterTemplate(
            $this->getRequest()->getParam('mailMergeDocumentTemplatePath')
        );

        return $template->toDocx();
    }

    public function sendDocx(Entity $entity)
    {
        $response = $this->getResponse();
        $response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->setHeader('Content-Disposition', 'attachment');

        $response->setBody($this->getDocx($entity));

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
            return $this->sendDocx($entity, $keepLayouts);
        }
        
        return $this->getDocx($entity, $keepLayouts);
    }
}
