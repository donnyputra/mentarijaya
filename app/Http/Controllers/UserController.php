<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\UserRole;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function profileUpdate(Request $request) {
    	try {
    		$validatedData = $request->validate([
				'name' => ['required', 'string', 'max:50'],
	            'username' => ['required', 'string', 'max:50'],
	            'email' => ['required', 'string', 'email', 'max:50'],
    		]);

    		\App\User::where('id', $request->input('id'))
			->update([
				'name' => $request->input('name'),
				'username' => $request->input('username'),
				'email' => $request->input('email')
			]);	
    	} catch(Exception $ex) {
    		return redirect()->back()->with(['error', $ex->getMessage()]);
    	}
    	
    	return redirect()->back()->with('success', __('Your profile has been updated.'));
	}
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'username' => 'required',
        ]);
        
        $user = new User([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'username' => $request->get('username'),
        ]);
        $user->save();

        $defaultRole = Role::where('name', '=', 'employee')->first();
        $defaultUserRole = new UserRole([
            'user_id' => $user->id,
            'role_id' => $defaultRole['id'],
        ]);
        $defaultUserRole->save();

        return redirect('/users')->with('success', __('User has been created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (Exception $ex) {
            return redirect()->route('users.index')->withError($ex->getMessage());
        }

        return view('users.edit')
            ->with('user', $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required',
				'email' => 'required|email',
				// 'password' => 'required',
				'username' => 'required',
            ]);

            $user = User::findOrFail($id);
            $user->name = $request->get("name");
            $user->email = $request->get("email");
            // $user->password = bcrypt($request->get("password"));
            $user->username = $request->get("username");
            $user->save();

        } catch (Exception $ex) {
            return redirect('/users')->with('error', $ex->getMessage());
        }

        return redirect('/users')->with('success', __('User has been updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
        } catch (Exception $ex) {
            return redirect('/users')->with('error', $ex->getMessage());
        }

        return redirect('/users')->with('success', __('User has been deleted.'));
    }

    public function changepassword($id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (Exception $ex) {
            return redirect()->route('users.index')->withError($ex->getMessage());
        }

        return view('users.changepassword')
            ->with('user', $user);
    }

    public function updatepassword(Request $request, $id)
    {
        try {
            $request->validate([
                'password' => 'required|confirmed',
            ]);

            $user = User::findOrFail($id);
            $user->password = bcrypt($request->get("password"));
            $user->save();

        } catch (Exception $ex) {
            return redirect('/users')->with('error', $ex->getMessage());
        }

        return redirect('/users')->with('success', __('User password has been updated.'));
    }
}
