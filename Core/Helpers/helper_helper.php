<?php
if (!function_exists('mysqldatetime'))
{
	function mysqldatetime($timzone = 'Asia/Jakarta')
	{
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone($timzone));
		return $date->format('Y-m-d H:i:s');
		//return date('Y-m-d H:i:s', $timestamp);
	}
}