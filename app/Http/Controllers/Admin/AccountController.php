<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class AccountController extends Controller
{
    // Login form
    public function login(){
    	return view( 'admin.account.login' );
    }

    // Do login action
    public function doLogin(Request $request){

    	// Autenticate user
    	if ( \Auth::attempt( $request->only( [ 'username', 'password' ]), true ) ) {
            
            // Authentication passed...

            // Log it
             \App\ActivityLog::create([
                'user_id'   => \Auth::id(),
                'record_id' => \Auth::id(),
                'before'    => "[]",
                'after'     => "[]",
                'action'    => 'login',
                'model'     => 'User',
            ]);

            // If request URL provided, go to that
            if( !empty( $request->redirect_uri ) )
                return redirect()->to($request->redirect_uri);
            else
                return redirect()->action('Admin\DashboardController@index');
        }
        else {
            
            // Send a message
            $request->session()->flash('failed_login', 'We could not log you in, ensure your username and password is correct.');

            // Redirect back
            return back();
        }
    }

    // Logout
    public function logout(){

        // Log the logout procedure
         \App\ActivityLog::create([
            'user_id'   => \Auth::id(),
            'record_id' => \Auth::id(),
            'before'    => "[]",
            'after'     => "[]",
            'action'    => 'logout',
            'model'     => 'User',
        ]);

        // Log user out
        \Auth::logout();
    	
        // Redirect to login page
        return redirect()->action('Admin\AccountController@login');
    }

    // Reset password
    public function resetPassword(){
        return view('admin.account.reset-password');
    }

    // Reset password action
    public function requestResetPassword(Request $request){

        // Validation messages
        $validation_messages = [
            'required' => ":attribute is required to reset the account.",
            'exists' => "No account was found with that :attribute.",
            'email' => 'Please provide a valid email address.'
        ];

        // Validate input
        $this->validate($request, [
            'email' => [ 'required', 'exists:users,email', 'email' ]
        ], $validation_messages);

        // Log the action
         \App\ActivityLog::create([
            'user_id'   => \Auth::id(),
            'record_id' => \Auth::id(),
            'before'    => "[]",
            'after'     => "[]",
            'action'    => 'request password reset',
            'model'     => 'User',
        ]);

        // Send email
        \Mail::to("stefan@prismproduction.co.uk")->send(new \App\Mail\ResetPassword( User::where( [ 'email' => $request->email ] )->first()->reset_token ));

        // Send flash message
        $request->session()->flash('reset_success', 'We have sent an email to the email address, please follow the link inside the email to reset your password');

        return redirect()->action('Admin\AccountController@resetPassword');

    }

    // Show new password form
    public function resetPasswordByToken(Request $request, $token){
        // Get user from token
        $user = User::where([ 'reset_token' => $token ])->first();

        // Show reset form
        return view('admin.account.new-password', [ 'user' => $user ]);
    }

    // Do the reset password action
    public function doReset(Request $request){

        $validate_message = [
            'same' => 'Ensure both passwords match',
        ];

        // Validate input
        $this->validate( $request, [
            'password' => [ 'same:password_confirm' ],
        ], $validate_message );

        // Get user
        $user = User::where( [ 'reset_token' => $request->only( ['reset_token'] ) ] )->first();

        // If user found
        if( !is_null( $user ) )
        {
            // Create new encrypted password
            $encrypted_password = bcrypt( $request->password );

            // Update user
            $user->password = $encrypted_password;

            // Save
            if( $user->save() )
            {
                // Log the action
                 \App\ActivityLog::create([
                    'user_id'   => \Auth::id(),
                    'record_id' => \Auth::id(),
                    'before'    => "[]",
                    'after'     => "[]",
                    'action'    => 'reset password',
                    'model'     => 'User',
                ]);

                // Set flash
                $request->session()->flash('do_reset_success', 'Successfully changed password, you can now log in using your new password');
            }
            else
            {
                // Log the action
                 \App\ActivityLog::create([
                    'user_id'   => \Auth::id(),
                    'record_id' => \Auth::id(),
                    'before'    => "[]",
                    'after'     => "[]",
                    'action'    => 'failed to reset password',
                    'model'     => 'User',
                ]);
                // Set error flash
                $request->session()->flash('do_reset_error', 'Could not update your password, please try again later. Your reset token may have changed');
            }

            return redirect()->action('Admin\AccountController@login');
        }
        else {
            // Reset error
            $request->session()->flash('do_reset_error', 'We could not find a user from the reset URL you\'ve attempted to load. Could be the that the reset token has expired');

            // Redirect back to login
            return redirect()->action('Admin\AccountController@requestResetPassword', [ 'token' => $request->reset_token ]);
        }
    }

    // Account's settings
    public function settings(Request $request, User $user){
        return view('admin.account.settings', [
            'user' => \Auth::user(),
            'hide_search' => true,
        ]);
    }

    // Do Account's update
    public function updateSettings(Request $request){

        $user = \Auth::user();

        // If doesn't exist, redirect back to index
        if( is_null( $user ) )
            return redirect()->action('Admin\DashboardController@index');

        $this->validate($request, [
            'user.name' => 'required|string',
            'user.email' => 'required|max:255|email',
        ]);

        // Updatable fields
        $updated_field = [
            'name' => (empty($request->user['name']))? null : $request->user['name'],
            'email' => (empty($request->user['email']))? null : $request->user['email'],
            'password' => (empty($request->user['password']))? null : bcrypt($request->user['password']),
        ];

        // Update fields
        if( $user->update(array_filter($updated_field)) )
        {

            // Log the action
             \App\ActivityLog::create([
                'user_id'   => \Auth::id(),
                'record_id' => \Auth::id(),
                'before'    => '',
                'after'     => json_encode(array_filter(array_diff_key(['deleted_at'=>null], \Auth::user()->getDirty()))),
                'action'    => 'updated',
                'model'     => 'User Settings',
            ]);

            // Set flash
            \Session::flash('success', "Updated account settings");

            // Redirect back to index
            return redirect()->back();
        }
        else{
            // Set flash
            \Session::flash('warning', "Could not save settings, try again later");

            // Redirect back to index
            return redirect()->back();
        }
    }
}
