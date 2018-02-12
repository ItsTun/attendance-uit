<?php

namespace App;

use DateTime;

class Utils
{
    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function periodIsInDate($period, $date)
    {
        $timestamp = strtotime($date);
        $day = date('w', $timestamp);
        $period = Period::where([['day', '=', $day], ['period_id', '=', $period]])->first();
        return !(is_null($period));
    }

    public static function getDayFromDate($date)
    {
        $timestamp = strtotime($date);
        return date('w', $timestamp);
    }

    public static function checkDateIsEligible($date)
    {
        $date = strtotime($date);
        $today = strtotime(date("Y-m-d"));
        $thisMonday = strtotime('monday this week');
        return ($today - $date >= 0 && $thisMonday - $date <= 0);
    }

    public static function getDatesInThisWeek()
    {
        $dates = [];
        for ($i = 0; $i < 5; $i++) {
            $day = date('N');
            $date = strtotime($i . ' day', ($day != 6 && $day != 7) ? strtotime('monday this week') : strtotime('next monday'));
            $date = date('Y-m-d', $date);
            array_push($dates, $date);
        }

        return $dates;
    }

    public static function getDefaultDate()
    {
        $day = date('N');
        $date = '';
        if ($day != 6 && $day != 7) {
            $date = date('Y-m-d');
        } else {
            $date = strtotime('next monday');
            $date = date('Y-m-d', $date);
        }

        return $date;
    }

    public static function knatsort(&$arr)
    {
        return uksort($arr, function ($a, $b) {
            return strnatcmp($a, $b);
        });
    }

    public static function getDate($date)
    {
        if ($date != null) {
            $date = strtotime($date);
            $date = date('Y-m-d', $date);
        } else {
            $date = date('Y-m-d');
        }
        return $date;
    }

    public static function getCurrentDateTime()
    {
        return date('Y-m-d h:m:s');
    }

    public static function getAssociatedPeriod($period_ary, $date_str)
    {
        $period = $period_ary[0];
        $date = new DateTime($date_str);
        for ($i = 1; $i < count($period_ary); $i++) {
            $deleted_date = new DateTime($period_ary[$i]->deleted_at);
            if ($date < $deleted_date) {
                $period = $period_ary[$i];
            }
        }
        return $period;
    }

    public static function getPrettyDateFormat($date)
    {
        $months = ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $date_ary = explode('-', $date);
        $pretty_date = $months[$date_ary[1] - 1] . ' ' . $date_ary[2] . ', ' . $date_ary[0];
        return $pretty_date;
    }

    public static function getMonthString($month)
    {
        $months = ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return $months[$month - 1];
    }

    public static function getLastDateOfMonth($month, $year)
    {
        $date = new DateTime($year . '-' . $month . '-01');
        $date->modify('+1 month');
        $date->modify('-1 day');
        return $date;
    }

    public static function getCorrectPrefix($subject_class) {
        if (is_null($subject_class->custom_prefix)) {
            return $subject_class->klass->short_form . '-';       
        } else {
            if ($subject_class->custom_prefix == "") {
                return "";
            }
            return $subject_class->custom_prefix . '-';
        }
    }
}