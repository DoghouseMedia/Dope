<?php

namespace Dope\Entity\Helper;

class Date
{
	const FORMAT_DATE = 'd M Y';
	const FORMAT_DATETIME = 'd M Y, H:i';
	
	public static function isValidString($dateString)
	{
		if (substr($dateString, 0, 10) == '0000-00-00') {
			return false;
		}
		
		if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $dateString)) {
			return false;
		}
		
		return true;
	}
	
	public static function format($dateString, $format=self::FORMAT_DATE)
	{
		if ($dateString instanceof \DateTime) {
            // -62170012800 is "0000-00-00 00:00:00"
            if ($dateString->getTimestamp() == -62170012800) {
                return '';
            }

			return $dateString->format($format);
		}
		
		if (! static::isValidString($dateString)) {
			return '';
		}
		
		return date($format, strtotime($dateString));
	}
	
	public static function formatTime($dateString)
	{
		return static::format($dateString, static::FORMAT_DATETIME);
	}

    public static function getMonths()
    {
        return new \ArrayObject(array(
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        ));
    }

}