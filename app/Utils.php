<?php

namespace App;

class Utils {
	public static function validateDate($date, $format = 'Y-m-d')
	{
	    $d = \DateTime::createFromFormat($format, $date);
	    return $d && $d->format($format) == $date;
	}

	public static function periodIsInDate($period, $date) {
		$timestamp = strtotime($date);
		$day = date('w', $timestamp);
		$period = Period::where([['day','=',$day],['period_id','=',$period]])->first();
		return !(is_null($period));
	}

	public static function getDayFromDate($date) {
		$timestamp = strtotime($date);
		return date('w', $timestamp);
	}

	public static function checkDateIsEligible($date) {
		$date = strtotime($date);
		$today = strtotime(date("Y-m-d"));
		$thisMonday = strtotime('monday this week');
		return ($today - $date >= 0 && $thisMonday - $date <= 0);
	}

	public static function getDatesInThisWeek() {
		$dates = [];
		for($i = 0; $i < 5; $i++) {
			$date = strtotime($i.' day', strtotime('monday this week'));
			$date = date('Y-m-d', $date);
			array_push($dates, $date);
		}
		return $dates;
	}
}