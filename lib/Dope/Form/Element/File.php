<?php

class Dope_Form_Element_File
extends Zend_Form_Element_File
{
	public function init()
	{
		parent::init();
		
		$this
			->setDecorators(array('File'))
			//->addValidator('Count', false, 1) // ensure only 1 file
			->addValidator('Size', false, $this->getMaxUploadSize()) // limit
			->setDescription('Maximum file size: ' . round($this->getMaxUploadSize() / (1024*1024)) . ' MB');
	}
	
	public function getMaxUploadSize()
	{
		$maxBytesPost = \Dope\Config\Ini::returnBytes(ini_get('post_max_size'));
		$maxBytesUpload = \Dope\Config\Ini::returnBytes(ini_get('upload_max_filesize'));
	
		$maxSize = ($maxBytesPost >= $maxBytesUpload + 500) ? $maxBytesUpload : $maxBytesPost - 500;
	
		return $maxSize;
	}
}