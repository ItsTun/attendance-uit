<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\InternalLog;
use App\User;
use App\Utils;

class InternalLogController extends Controller
{
    public function index(Request $request) {
    	$date = $request->date;
    	$date = Utils::getDate($date);
    	$limit = $request->limit;
    	$logs = InternalLog::getLogs($date, $limit);
    	return response($logs);
    }

    public function filterByUser(Request $request, User $user) {
    	$date = $request->date;
    	$date = Utils::getDate($date);
    	$limit = $request->limit;
    	$logs = InternalLog::getLogsFilterByUser($date, $limit, $user->id);
    	return response($logs);
    }
}
