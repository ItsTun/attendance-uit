<?php

namespace App\Console\Commands;

use App\EmailUtils;
use App\Klass;

use App\Mail\AbsentStudentList;
use App\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending emails to associated teachers and admins about students with three or more days of absence.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $klasses = Klass::all();
        $mail = null;
        $emails = [];
        foreach ($klasses as $klass) {
            $students = Student::getAllStudentAbsentsForThreeDaysOrAbove($klass->class_id);
            if (!is_null($students) && sizeof($students) > 0) {
                $mail = new AbsentStudentList($klass, $students);
                array_push($emails, $mail);
                $teachers_emails = EmailUtils::getTeacherMailingList($klass->class_id);
                print_r($teachers_emails);
                foreach ($teachers_emails as $teacher_email) {
                    Mail::send('mail_templates.absent_list', ['klass' => $klass, 'students' => $students], function ($message) use ($teacher_email) {
                        $message->to($teacher_email, "")->subject("Students Absent Report");
                    });
                }
            }
        }
    }
}
