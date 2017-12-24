<?php

namespace App\Http\Controllers\Auth;

use Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

use App\Teacher;
use App\User;

class TeacherLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'teacher/timetable';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->with(['hd' => 'uit.edu.mm'])->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $google_user = Socialite::driver('google')->user();

        $teacher = Teacher::where('email', $google_user->email)->first();
        //Check if teacher's email is registered in the database
        if(!is_null($teacher)) {
            $google_user_id = $google_user->getId();
            $user = User::where('google_user_id', $google_user_id)->first();

            //if this user is not in the database
            if(!$user) {
                $user = new User;
                $user->email = $google_user->email;
                $user->name = $google_user->name;
                $user->google_user_id = $google_user_id;
                $user->role = 'teacher';
                //saving the user after putting his google_user_id
                $user->save();
            }

            Auth::loginUsingId($user->id);

            return redirect('teacher/timetable');
        } else {
            return "Your email is currently not in our database. If you are a teacher at UIT, you contact admin to register.";
        }
    }

    public function logout() {
        Auth::logout();
        return redirect('teacher/login');
    }
}
