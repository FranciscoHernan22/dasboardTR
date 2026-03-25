<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
 
class  RegisterController extends Controller
{
    //
    public function index() {
    return view('auth.register');
}

  public function store(Request $request) {
   

    // Modificar el Request
    $request->request->add(['username' => Str::slug($request->username)]);

    $this->validate($request, [
      'name' => ['required', 'min:5'],
      'username' => ['required', 'unique:users', 'min:3', 'max:20'],
      'email' => ['required','unique:users', 'email', 'max:60'],
      'password' => ['required', 'confirmed', 'min:6'],

    ]);

    User::create([
      'name' => $request-> name,
      'username' => $request->username ,
      'email' => $request->email,
      'password' => Hash::make( $request->password )
    ]);


    // Autenticar un usuario
    auth()->attempt( [
      'email' => $request->email,
      'password' => $request->password 
    ]);


     

    //Redireccionar
    return redirect()->route('posts.index');

  }



}
