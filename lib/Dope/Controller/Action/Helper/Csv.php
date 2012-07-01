<?php

namespace Dope\Controller\Action\Helper;

class Csv extends \Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Suppress exit when sendCsv() called
	 * @var boolean
	 */
	public $suppressExit = false;

	/**
	 * Create CSV response
	 *
	 * Encodes and returns data to CSVs. Content-Type header set to
	 * 'text/csv', and disables layouts and viewRenderer (if being
	 * used).
	 *
	 * @param  mixed   $data
	 * @param  boolean $keepLayouts
	 * @param  boolean|array $keepLayouts
	 * @return string
	 */
	public function encodeCsv($data, $keepLayouts = false)
	{
		if (!$keepLayouts) {
			/**
			 * @see Zend_Controller_Action_HelperBroker
			 */
			require_once 'Zend/Controller/Action/HelperBroker.php';
			\Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
		}
		
		ob_start();
		$fp = fopen("php://output", "w");
		
		if (is_array(current($data))) {
			if (isset($data[0]) AND is_array($data[0])) {
				fputcsv($fp, array_keys($data[0]));
			}
			
			foreach ($data as $i => $values) {
				fputcsv($fp, $this->prepareValues($values));
			}
		}
		else {
			fputcsv($fp, array_keys($data));
			fputcsv($fp, $this->prepareValues(array_values($data)));
		}
		
		$csvString = ob_get_clean();
		fclose($fp);
		
		/*
		 * Excel SYLK-Bug
		 * @see http://support.microsoft.com/kb/323626
		 */
		$csvString = preg_replace('/^ID/', "'ID", $csvString);
		
		/* UTF-8 decode */
		$csvString = utf8_decode($csvString);
		
		return $csvString;
	}
	
	protected function prepareValues(array $values)
	{
		foreach ($values as $i => $value) {
			if ($value instanceof \DateTime) {
				$values[$i] = $value->format(\DATE_ISO8601);
			}
		}
		return $values;
	}

	public function sendCsv($data, $keepLayouts = false)
	{
		$data = $this->encodeCsv($data, $keepLayouts);
		
		$response = $this->getResponse();
		$response->setHeader('Content-Type', 'text/csv');
		$response->setHeader('Content-Length', mb_strlen($data, 'utf-8'));
		$response->setBody($data);

		if (!$this->suppressExit) {
			$response->sendResponse();
			exit;
		}

		return $data;
	}

	/**
	 * Strategy pattern: call helper as helper broker method
	 *
	 * Allows encoding XML. If $sendNow is true, immediately sends XML
	 * response.
	 *
	 * @param  mixed   $data
	 * @param  boolean $sendNow
	 * @param  boolean $keepLayouts
	 * @return string|void
	 */
	public function direct($data, $sendNow = true, $keepLayouts = false)
	{
		if ($sendNow) {
			return $this->sendCsv($data, $keepLayouts);
		}
		return $this->encodeCsv($data, $keepLayouts);
	}
}
