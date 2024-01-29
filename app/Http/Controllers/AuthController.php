<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function customLogin(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $Url = 'https://www.youtube.com/shorts/6h34DdkGFdU';
            $Url2 = 'https://www.youtube.com/shorts/jXVl3BIv1Fo';

            //Compruebo que el usuario tiene el token en la tabla personal_access_token
            $personalAccessTokenRecord = DB::table('personal_access_tokens')
                ->where('tokenable_id', $user->id)
                ->where('name', 'custom_token')
                ->latest()
                ->first();

            // Si no tiene un token  generarlo y guardarlo en la tabla personal_access_tokens solo una vez
            if (!$personalAccessTokenRecord) {
                $personalAccessToken = $user->createToken('custom_token')->plainTextToken;
                $user->remember_token = $personalAccessToken;
                $user->save();
                $personalAccessTokenRecord = (object) ['token' => $personalAccessToken];
            }
            // devuelvo la repuesta si esta todo correctamente
            return $this->sendResponse(
                true,
                'El usuario se ha logueado correctamente.',
                [
                    'user' => $user,
                    'remember_token' => $user->remember_token,
                    'personal_access_token' => $personalAccessTokenRecord ? $personalAccessTokenRecord->token : null,
                    'youtube_shorts_url' => $Url2
                ]
            );
        }
        // devuelvo la repuesta si esta todo no esta correcto uWu
        return $this->sendResponse(false, 'Credenciales incorrectas. El usuario no se ha encontrado.', []);
    }

    //Funcion para comprobar si estas logeado
    public function checkLoggedIn(Request $request)
    {
        $user = $request->user();

        if ($user && $user->remember_token !== null) {
            $Url = 'https://www.youtube.com/shorts/16uJ-jxcKHo';

            return $this->sendResponse(
                true,
                'El usuario está logueado.',
                ['user' => $user, 'youtube_shorts_url' => $Url]
            );
        }

        return $this->sendResponse(false, 'El usuario no está logueado.', []);
    }
    //Funcion para cargarte el token de personal_access_token
    public function customLogout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $Url = 'https://www.youtube.com/watch?v=Oh4HRdi2mvo&ab_channel=ELMEMETRICO';

            $user->tokens()->delete();

            return $this->sendResponse(
                true,
                'El usuario se ha deslogueado correctamente.',
                ['youtube_shorts_url' => $Url]
            );
        }

        return $this->sendResponse(false, 'El usuario no se ha encontrado o no está logueado.', []);
    }
    //funcion de modo de respuesta json
    private function sendResponse($success, $message, $data)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ]);
    }
}