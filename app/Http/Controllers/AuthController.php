<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Show the App's login form
     * 
     * 
     * @return \Illuminate\View\View
     */

        public function showLoginForm(){
        return view('auth.login');
        }

    /**
     * Show the App's Registration form
     * 
     * @return \Illuminate\View\View
     */

    public function showRegistrationForm(){
        return view('auth.register');
    }


    /**
     * Handle login Request to the app
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request){

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        
        if($user && Hash::check($request->password, $user->password)){
            session(['user_id' => $user->id]);

            return redirect()->intended('/');
        }
        
        return Redirect::back()->withErrors(['email' => "Credentials don't match."]);

    }

    /**
     * Handle register request to the app
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function register(Request $request){
        $validatedData = $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $existingUser = User::where('email', $request->email)->first();

        if($existingUser){
            return Redirect::back()->withErrors(['email' => 'Email already exists.']);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => hash::make($request->password)
        ]);

        session(['user_id' => $user->id]);

        return redirect()->intended('/');
    }

    /**
     * Handle logout request to the app
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function logout(Request $request){
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->intended('login');
    }
}
