<?php

class Dope_View_Helper_StatRatio extends Dope_View_Helper_Divide
{
	public function statRatio($dividend, $divisor, $precision=2, $makePercentage=false)
	{
		$value = $this->divide($dividend, $divisor, $precision);
		
		if ($makePercentage) {
			$value *= 100;
		}
		
		$html  = '<span title="' . $dividend . ':' . $divisor . '">';
		$html .= $value;
		$html .= '</span>';
		
		return $html;
	}
}
