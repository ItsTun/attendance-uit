<?php

return [

	'DB_TABLE' => [
		'ATTENDANCES' => 'attendances',
		'CLASSES' => 'classes',
		'OPEN_PERIODS' => 'open_periods',
		'PERIOD_ATTENDANCE' => 'period_attendance',
		'PERIODS' => 'periods',
		'STUDENTS' => 'students',
		'SUBJECT_TEACHER' => 'subject_teacher',
		'SUBJECTS' => 'subjects',
		'TEACHERS' => 'teachers',
		'YEARS' => 'years',
	],

	'ATTENDANCES' => [
		'TABLE_NAME' => 'attendances',
		'PRIMARY_KEY' => 'student_roll_no',
	],

	'CLASSES' => [
		'TABLE_NAME' => 'classes',
		'PRIMARY_KEY' => 'class_id',
	],

	'OPEN_PERIODS' => [
		'TABLE_NAME' => 'open_periods',
		'PRIMARY_KEY' => 'open_period_id',
	],

	'PERIOD_ATTENDANCE' => [
		'TABLE_NAME' => 'period_attendance',
		'PRIMARY_KEY' => 'period_attendance_id',
	],

	'PERIODS' => [
		'TABLE_NAME' => 'periods',
		'PRIMARY_KEY' => 'period_id',
	],

	'STUDENTS' => [
		'TABLE_NAME' => 'students',
		'PRIMARY_KEY' => 'student_id',
	],

	'SUBJECT_TEACHER' => [
		'TABLE_NAME' => 'subject_teacher',
	],

	'SUBJECTS' => [
		'TABLE_NAME' => 'subjects',
		'PRIMARY_KEY' => 'subject_id',
	],

	'TEACHERS' => [
		'TABLE_NAME' => 'teachers',
		'PRIMARY_KEY' => 'teacher_id',
	],

	'YEARS' => [
		'TABLE_NAME' => 'years',
		'PRIMARY_KEY' => 'year_id',
	],

];