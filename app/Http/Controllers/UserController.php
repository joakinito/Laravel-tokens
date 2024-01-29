<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //informacion de usuario almacenado en la base de datos
    public function userInfo()
    {
        $user = Auth::user();
        return response()->json(['user' => $user]);
    }
}
