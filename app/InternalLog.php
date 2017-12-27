<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InternalLog extends Model
{
   	protected $table = 'internal_logs';
	protected $primaryKey = 'id';
	protected $fillable = ['id', 'action', 'message', 'new_value', 'old_value', 'created_at', 'by_user'];

	public $incrementing = true;
	public $timestamps = false;
	
	public function user() {
 		return $this->belongsTo(User::class, 'by_user');
 	}

 	public static function getLogs($date, $limit) {
 		$logs = InternalLog::whereDate('created_at', $date)
    		->orderby('created_at', 'DESC')
    		->limit($limit)
			->get();

		return $logs;
 	}

 	public static function getLogsFilterByUser($date, $limit, $userId) {
 		$logs = InternalLog::whereDate('created_at', $date)
 			->where('by_user', $userId)
    		->orderby('created_at', 'DESC')
    		->limit($limit)
			->get();

		return $logs();
 	}

 	public static function addLog($action, $message, $new_value, $old_value, $time, $userId) {
 		$log = new InternalLog();
 		$log->action = $action;
 		$log->message = $message;
 		$log->new_value = $new_value;
 		$log->old_value = $old_value;
 		$log->created_at = $time;
 		$log->by_user = $userId;
 		$log->save();
 	}
}
