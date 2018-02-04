<?php

namespace App;

class EmailUtils
{
    public static function getMailingList($class_id)
    {
        $emails = [];
        $teachers = Teacher::getAssociatedTeachers($class_id);
        $teachers_emails = array_column($teachers->toArray(), 'email');
        $emails['teachers'] = $teachers_emails;

        $admins = User::where('role', 'admin')->select('email')->get();
        $admins_emails = array_column($admins->toArray(), 'email');
        $emails['admins'] = $admins_emails;

        return $emails;
    }
}