<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return response()->json(['success' => true]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        if (count($users) <= 0) {
            return response(["message" => "Aucun utilisateur de retrouver"], 200);
        }else{
            return response(["data" => $users], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $findLivre = User::find($id);
        
        if (!$findLivre) {
            return response(["message" => "Aucun utilisateur de trouvé avec l'id $id"], 404);
        }else{
            return response()->json(["data" => $findLivre], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $userValidation = $request->validate([
                'name' => 'max:255',
                'password' => 'string|min:8|max:30|confirmed',
                'id_user' => 'required|exists:users,id',
            ]);
            
            $user = User::find($id);

            if (!$user) {
                return response(["message" => "Aucun utilisateur de trouvé avec l'id $id"], 404);
            }else{
                if (intval($user->id) === intval($userValidation['id_user'])) {
                    $user->update($userValidation);

                    return response(["data" => $user], 200);
                }else{
                    return response(["message" => "Access refusé"], 404);
                }
            }
        } catch (ValidationException $exception) {
            return response()->json([
                'errors' => $exception->validator->errors(),
                'message' => 'Une ou plusieurs erreurs se sont produites.',
            ], 422)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $userValidation = $request->validate([
                'id_user' => 'required|exists:users,id',
            ]);

            $user = User::find($id);

            if (!$user) {
                return response(["message" => "Aucun livre de trouvé avec l'id $id"], 404);
            }else{
                if (intval($user->id) === intval($userValidation['id_user'])) {
                    User::destroy($id);

                    return response(["message" => "L'utiliateur a été supprimé avec success"], 200);
                }else{
                    return response(["message" => "Access refusé"], 404);
                }
            }

            return response(["data" => $updateBook], 200);
        } catch (ValidationException $exception) {
            return response()->json([
                'errors' => $exception->validator->errors(),
                'message' => 'Une ou plusieurs erreurs se sont produites.',
            ], 422)->header('Content-Type', 'application/json');
        }
    }
}
