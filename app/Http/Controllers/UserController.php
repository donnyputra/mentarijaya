<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
