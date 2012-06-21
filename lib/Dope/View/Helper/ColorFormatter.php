<?php

class Dope_View_Helper_ColorFormatter
{
	public function colorFormatter($value, $widthPixels=80, $heightPixels=18)
	{
		$styles = array(
			'background-color' => $value,
			'display' => 'inline-block',
			'width' => $widthPixels . 'px',
			'height' => $heightPixels . 'px',
			'vertical-align' => 'bottom',
			'border-radius' => $heightPixels/2 . 'px',
			'box-shadow' => '2px 2px 3px #ccc'
		);
		
		$html = $value . ' <span style="';
		foreach($styles as $key => $val) {
			$html .= $key . ': ' . $val . ';'; 
		}
		$html .= '"></span>';
		
		return $html;
	}
}