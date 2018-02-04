<?php

namespace App;

class EmailUtils
{
    public static function getTeacherMailingList($class_id)
    {
        $teachers = Teacher::getAssociatedTeachers($class_id);
        return array_column($teachers->toArray(), 'email');
    }

    public static function getAdminMailingList()
    {
        $admins = User::where('role', 'admin')->select('email')->get();
        return array_column($admins->toArray(), 'email');
    }
}