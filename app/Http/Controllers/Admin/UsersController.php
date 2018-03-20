<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\User;

class UsersController extends Controller
{
    // Index page
    public function index(Request $request){

    	// Get page from $_GET
	    $page = ( isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) )? $_GET['page'] : 1;

	    // Get limit on results to fetch
	    $limit = \Config::get('pss.items_per_page', 15 );

	    // Fetch media files
	    $users = User::orderBy('name', 'asc')
	    	->where(function ($query) use ($request){

	            $query->orWhere(function ($query) use ($request){
	                $query->where([[ 'name', 'LIKE', "%{$request->search}%" ]]);
	                $query->whereNotNull('name');
	            });

	            $query->orWhere(function ($query) use ($request){
	                $query->where([[ 'username', 'LIKE', "%{$request->search}%" ]]);
	                $query->whereNotNull('username');
	            });

	            $query->orWhere(function ($query) use ($request){
	                $query->where([[ 'email', 'LIKE', "%{$request->search}%" ]]);
	                $query->whereNotNull('email');
            	});
	        })
	        ->where([[ 'id', '!=', \Auth::id() ]])
	        ->paginate($limit);

	    // Count the media files
	    $total_users = User::count();
        $pages = (ceil($total_users/$limit) > 1)? ceil($total_users/$limit) : 1;

	    // If we're on an invalid page number, redirect
	    if( $page > $pages )
	        return redirect()->action('Admin\UsersController@index', [ 'page' => $pages ] );

	    // Index
    	return view('admin.users.index', [
			'page_title' => 'Users',
			'users' => $users,
			'limit' => $limit,
        ]);
    }

    // Edit user
	public function edit(Request $request, User $user){
		return view('admin.users.form', [
			'user' => $user,
            'page_title' => '<a href="' . action('Admin\UsersController@index') . '">Users</a> <span class=\'fa fa-angle-right\'></span> Editing &quot;' . $user->name . '&quot;',
            'is_new' => false,
            'hide_search' => true,
		]);
	}

	// New User
	public function new(Request $request){
		
		$user = new User;

		return view('admin.users.form', [
			'user' => $user,
            'page_title' => '<a href="' . action('Admin\UsersController@index') . '">Users</a> <span class=\'fa fa-angle-right\'></span> New user',
            'is_new' => true,
            'hide_search' => true,
		]);
	}

	// Update existing user
	public function update(Request $request, User $user){
		// If doesn't exist, redirect back to index
		if( is_null( $user ) )
			return redirect()->action('Admin\UsersController@index');
		
		// Else, continue
		$this->processData($request, [
			'user.username' => [
				'string', 'max:20', Rule::unique('users', 'username')->ignore($user->id)
			]
		]);

		// Updatable fields
		$updated_field = [
			'name' => (empty($request->user['name']))? null : $request->user['name'],
			'username' => (empty($request->user['username']))? null : $request->user['username'],
			'email' => (empty($request->user['email']))? null : $request->user['email'],
			'password' => (empty($request->user['password']))? null : bcrypt($request->user['password']),
		];


		// Update fields
		if( $user->update(array_filter($updated_field)) )
		{
			// Log the action
             \App\ActivityLog::create([
                'user_id'   => \Auth::id(),
                'record_id' => $user->id,
                'before'    => json_encode($user->getOriginal()),
                'after'     => json_encode(array_filter(array_diff_key(['deleted_at'=>null], $user->getDirty()))),
                'action'    => 'update',
                'model'     => 'User',
            ]);

			// Set flash
			\Session::flash('success', "Updated user: {$user->name}");

			// Redirect back to index
			return redirect()->back();
		}
		else{
			// Set flash
			\Session::flash('warning', "Could not save the user, try again later");

			// Redirect back to index
			return redirect()->back();
		}

	}

	// Create new user
	public function create(Request $request){

		// Validate data
		$this->processData($request, [
			'user.username' => 'string|max:20|unique:users,username',
			'user.password' => 'required|string',
		]);

		$fields = [
			'name' => $request->user['name'],
			'username' => $request->user['username'],
			'email' => $request->user['email'],
			'password' => bcrypt($request->user['password']),
			'reset_token' => bin2hex(random_bytes(64)),
		];

		// If all good, save to DB
		$user = new User($fields);
		$user->save();


		// Log the action
         \App\ActivityLog::create([
            'user_id'   => \Auth::id(),
            'record_id' => $user->id,
            'before'    => json_encode($user->getOriginal()),
            'after'     => json_encode(array_filter(array_diff_key(['deleted_at'=>null], $user->getDirty()))),
            'action'    => 'create',
            'model'     => 'User',
        ]);

		// Set flash
		\Session::flash('success', "Created new user: {$user->name}");

		// Redirect back to index
		return redirect()->action('Admin\UsersController@index');

	}

	/**
	* Validates data for properties
	*/
	private function processData($request, $custom_rules=[]){

		// Start validation
		return $this->validate($request, array_merge([
			'user.name' => 'required|string',
			'user.email' => 'required|max:255|email',
		], $custom_rules));
	}

	public function delete(Request $request, User $user){

		// If not exist, redirect to index
		if( is_null( $user ) )
			return redirect()->action('Admin\UsersController@index');

		// Delete it!
		$user_name = $user->name;
		$user->delete();

		\App\ActivityLog::create([
            'user_id'   => \Auth::id(),
            'record_id' => $user->id,
            'before'    => json_encode($user->getOriginal()),
            'after'     => json_encode(array_filter(array_diff_key(['deleted_at'=>null], $user->getDirty()))),
            'action'    => 'delete',
            'model'     => 'User'
        ]);

		// Delete flash message
		\Session::flash('deleted', "Deleted user: {$user_name}");

		// Redirect back to index
		return redirect()->action('Admin\UsersController@index');

	}
}
