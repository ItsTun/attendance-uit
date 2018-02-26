<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Feedback;

class FeedbackController extends Controller
{
    public function sendFeedback()
    {
    	$feedback = new Feedback;
    	$feedback->student_id = Input::post('student_id');
    	$feedback->device_type = Input::post('device_type');
    	$feedback->content = Input::post('content');
    	$feedback->save();
    }

    public function getFeedbacks() 
    {
    	$feedbacks = Feedback::orderby('created_at')->get();
    	return response($feedbacks, 200);
    }
}
