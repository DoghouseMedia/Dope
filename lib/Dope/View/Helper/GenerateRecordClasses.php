<?php

class Dope_View_Helper_GenerateRecordClasses
{
	public function generateRecordClasses(\Dope\Entity $record)
	{
		$classes = array();
		
		if ($record->deleted) {
			$classes[] = 'record-deleted';
		}
		
		return join(' ', $classes);
	}
}
