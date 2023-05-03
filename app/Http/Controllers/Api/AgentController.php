<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pointage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Ramsey\Uuid\Uuid;
use App\Models\CodeQr;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

function valid_donnees($donnees){
    $donnees = trim($donnees);
    $donnees = stripslashes($donnees);
    $donnees = htmlspecialchars($donnees);

    return $donnees;
}

function firstLetterUpper($mot) {
	$firstLetter = strtoupper(substr($mot, 0, 1));
	$restLetters = strtolower(substr($mot, 1));

	return $firstLetter.''.$restLetters;
}

function breakPhrase($phrase) {
    $remplacer = array(" ");
	
	$recupPhrase = $phrase;
	
	$breakStrPhrase = trim(str_replace($remplacer, " ", $recupPhrase));

	$separateur = "#[ ]+#";
    $mots = preg_split($separateur, $breakStrPhrase);

    $nbrMots = count($mots);

    $return = "";

    for ($i=0; $i < $nbrMots; $i++) { 
		$word = firstLetterUpper($mots[$i]);
        $return = $return.' '.$word;
    }

    return $return;
}

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agents = User::with(['service', 'fonction'])->get();

        $agents = $agents->map(function ($agent) {
            date_default_timezone_set('UTC');
            $now = time();
            
            $date = date('Y-m-d', $now);
            $heure = date('H:i', $now);

            $verifExistence = Pointage::where('date_actuelle', $date);

            $verification = Pointage::where('date_actuelle', $date)->where('id_agent', $agent?->id)->first();

            if ($verification?->heure_arrivee === null) {
                $status = false;
            }else{
                $status = true;
            }

            $agent->status = $status;

            return $agent;
        });

        if (count($agents) <= 0) {
            return response([
                "success" => false,
                "message" => "Aucun agent de trouvé",
                "status" => 401,
            ], 401);
        }else{
            return response([
                "success" => true,
                "data" => $agents, 
                "status" => 200
            ], 200);
        }
    }

    /**
     * Account login.
     */
    public function login(Request $request){
        try {
            $request->validate([
                'username' => 'required',
                'password' => 'required|string|min:8',
            ]);

            $user = User::where('username', $request['username'])->first();

            if (!$user) return response()->json([
                "success" => false,
                'errors' => ['Veuillez vérifier vos nom d\'utilisateur'],
                'status' => 401
            ], 401);

            if(!Hash::check($request['password'], $user->password)) return response([
                "success" => false,
                "errors" => ['Veuillez vérifier votre mot de passe'],
                "status" => 401
            ], 401);

            if($user->admins){
                $token = $user->createToken('CLE_SECRET')->plainTextToken;

                return response()->json([
                    "success" => true,
                    'data' => $user,
                    'token' => $token,
                    'status' => 200
                ], 200);
            }else{
                return response()->json([
                    "success" => false,
                    'errors' => ['Vous n\'êtes pas autorisé  vous connecter'],
                    'status' => 401
                ], 401);
            }
        } catch (ValidationException $exception) {
            return response()->json([
                'errors' => $exception->validator->errors(),
                'message' => 'Une ou plusieurs erreurs se sont produites.',
            ], 422)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $agent = $request->validate([
                'nom' => ['required', 'between:1,30'],
                'prenom' => ['required', 'between:1,30'],
                'tel' => [
                    'required',
                    'regex:/^\+225(01|07|05)\d{8}$/',
                ], [
                    'tel.regex' => 'Le numéro de téléphone doit être au format +225xxxxxxxx.',
                ],
                'sexe' => ['required', 'boolean'],
                'date_naissance' => ['required', 'before:01-01-2010'],
                'lieu_naissance' => ['required', 'between:1,125'],
                'id_service' => ['required', 'exists:services,id'],
                'id_fonction' => ['required', 'exists:fonctions,id'],
                'adresse' => ['nullable', 'max:255'],
                'email' => ['required', 'email'],
                'username' => ['required', 'unique:users,username'],
                'password' => ['required', 'min:8', 'confirmed'],
                'password_confirmation' => ['required', 'min:8'],
                'admins' => ['required', 'boolean']
            ]);

            $dateString = $agent["date_naissance"];
            $date_naissance = Carbon::parse($dateString)->toDateTimeString();

            $addAgent = User::create([
                'nom' => trim(breakPhrase(valid_donnees($agent['nom']))),
                'prenom' => trim(breakPhrase(valid_donnees($agent['prenom']))),
                'tel' => $agent['tel'],
                'sexe' => $agent['sexe'],
                'date_naissance' => $date_naissance,
                'lieu_naissance' => $agent['lieu_naissance'],
                'id_fonction' => $agent['id_fonction'],
                'id_service' => $agent['id_service'],
                'adresse' => trim(breakPhrase(valid_donnees($agent['adresse']))),
                'email' => strtolower($agent['email']),
                'username' => strtolower($agent['username']),
                'password' => bcrypt($agent["password"]),
                'admins' => $agent['admins'],
            ]);

            if($addAgent){
                $myuuid = Uuid::uuid4();
                $path = '../public/qrcodes/'.$addAgent->id.'.svg';
                $filename = "/qrcodes/$addAgent->id.svg";

                $qr = CodeQr::where('id_user', $addAgent->id)->get();

                if (!count($qr)) {
                    QrCode::size(200)->generate($myuuid, $path);
                    CodeQr::create([
                        "id_user" => $addAgent->id,
                        "token" => $myuuid,
                    ]);
                }

                return response()->json([
                    "success" => true,
                    "id_agent" => $addAgent->id,
                    "data" => $addAgent, 
                    "status" => 200
                ], 200);
            }
        } catch (ValidationException $exception) {
            return response()->json([
                'errors' => $exception->validator->errors(),
                'status' => 422,
            ], 422)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $findUser = User::find(intval($id));

        if (!$findUser) {
            return response([
                "success" => false,
                "message" => "Aucun agent de trouvé avec l'id $id",
                "status" => 200
            ], 404);
        }else{
            $user = User::where("id", intval($id))->first();

            return response()->json([
                "success" => true,
                "data" => $user, 
                "status" => 200
            ], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
            $validatorRequest = $request->validate([
                "id_champs" => ['required', 'numeric'],
            ]);

            $agent = User::find($id);

            if(intval($validatorRequest["id_champs"]) === 1){
                // Nom
                $validatorChamps = $request->validate([
                    'value' => ['required', 'between:2,30'],
                ]);

                if($agent){
                    $updateAgent = $agent->update([
                        "nom" => $validatorChamps["value"],
                    ]);

                    if($updateAgent){
                        return response([
                            "success" => true,
                            "message" => "Le nom à été modifié avec succès",
                            "status" => 200,
                        ], 200);
                    }else{
                        return response([
                            "success" => false,
                            "errors" => ["value" => ["Une erreur s'est produite lors de la modification du nom"]],
                            "status" => 401,
                        ], 401);
                    }
                }else{
                    return response([
                        "success" => false,
                        "errors" => ["value" => ["Une erreur s'est produite, cet agent n'existe pas!"]],
                        "status" => 401,
                    ], 401);
                }
            }else if(intval($validatorRequest["id_champs"]) === 2){
                // Prenom
                $validatorChamps = $request->validate([
                    'value' => ['required', 'between:1,30'],
                ]);

                if($agent){
                    $updateAgent = $agent->update([
                        "prenom" => $validatorChamps["value"],
                    ]);

                    if($updateAgent){
                        return response([
                            "success" => true,
                            "message" => "Le prénom à été modifié avec succès",
                            "status" => 200,
                        ], 200);
                    }else{
                        return response([
                            "success" => false,
                            "errors" => ["value" => ["Une erreur s'est produite lors de la modification du nom"]],
                            "status" => 401,
                        ], 401);
                    }
                }else{
                    return response([
                        "success" => false,
                        "errors" => ["value" => ["Une erreur s'est produite, cet agent n'existe pas!"]],
                        "status" => 401,
                    ], 401);
                }
            }else if(intval($validatorRequest["id_champs"]) === 3){
                // Lieu de naissnce
                $validatorChamps = $request->validate([
                    'value' => ['required', 'between:1,125'],
                ]);

                if($agent){
                    $updateAgent = $agent->update([
                        "lieu_naissance" => $validatorChamps["value"],
                    ]);

                    if($updateAgent){
                        return response([
                            "success" => true,
                            "message" => "Le lieu de naissance à été modifié avec succès",
                            "status" => 200,
                        ], 200);
                    }else{
                        return response([
                            "success" => false,
                            "errors" => ["value" => ["Une erreur s'est produite lors de la modification du nom"]],
                            "status" => 401,
                        ], 401);
                    }
                }else{
                    return response([
                        "success" => false,
                        "errors" => ["value" => ["Une erreur s'est produite, cet agent n'existe pas!"]],
                        "status" => 401,
                    ], 401);
                }
            }else if(intval($validatorRequest["id_champs"]) === 4){
                // Date de naissance
                $validatorChamps = $request->validate([
                    'value' => ['required', 'before:01-01-2010'],
                ]);

                $dateString = $validatorChamps["value"];
                $date_naissance = Carbon::parse($dateString)->toDateTimeString();

                if($agent){
                    $updateAgent = $agent->update([
                        "date_naissance" => $date_naissance,
                    ]);

                    if($updateAgent){
                        return response([
                            "success" => true,
                            "message" => "La date de naissance à été modifié avec succès",
                            "status" => 200,
                        ], 200);
                    }else{
                        return response([
                            "success" => false,
                            "errors" => ["value" => ["Une erreur s'est produite lors de la modification du nom"]],
                            "status" => 401,
                        ], 401);
                    }
                }else{
                    return response([
                        "success" => false,
                        "errors" => ["value" => ["Une erreur s'est produite, cet agent n'existe pas!"]],
                        "status" => 401,
                    ], 401);
                }
            }else if(intval($validatorRequest["id_champs"]) === 5){
                // Sexe
                $validatorChamps = $request->validate([
                    'value' => ['required', 'boolean'],
                ]);

                if($agent){
                    $updateAgent = $agent->update([
                        "sexe" => $validatorChamps["value"],
                    ]);

                    if($updateAgent){
                        return response([
                            "success" => true,
                            "message" => "Le genre à été modifié avec succès",
                            "status" => 200,
                        ], 200);
                    }else{
                        return response([
                            "success" => false,
                            "errors" => ["value" => ["Une erreur s'est produite lors de la modification du nom"]],
                            "status" => 401,
                        ], 401);
                    }
                }else{
                    return response([
                        "success" => false,
                        "errors" => ["value" => ["Une erreur s'est produite, cet agent n'existe pas!"]],
                        "status" => 401,
                    ], 401);
                }
            }else if(intval($validatorRequest["id_champs"]) === 6){
                // Tel
                $validatorChamps = $request->validate([
                    'value' => [
                        'required',
                        'regex:/^\+225(01|07|05)\d{8}$/',
                    ], [
                        'tel.regex' => 'Le numéro de téléphone doit être au format +225xxxxxxxx.',
                    ],
                ]);

                if($agent){
                    $updateAgent = $agent->update([
                        "tel" => $validatorChamps["value"],
                    ]);

                    if($updateAgent){
                        return response([
                            "success" => true,
                            "message" => "Le numéro de téphone à été modifié avec succès",
                            "status" => 200,
                        ], 200);
                    }else{
                        return response([
                            "success" => false,
                            "errors" => ["value" => ["Une erreur s'est produite lors de la modification du nom"]],
                            "status" => 401,
                        ], 401);
                    }
                }else{
                    return response([
                        "success" => false,
                        "errors" => ["value" => ["Une erreur s'est produite, cet agent n'existe pas!"]],
                        "status" => 401,
                    ], 401);
                }
            }else if(intval($validatorRequest["id_champs"]) === 7){
                // Adresse
                $validatorChamps = $request->validate([
                    'value' => ['nullable', 'max:255'],
                ]);

                if($agent){
                    $updateAgent = $agent->update([
                        "adresse" => $validatorChamps["value"],
                    ]);

                    if($updateAgent){
                        return response([
                            "success" => true,
                            "message" => "L'adresse à été modifié avec succès",
                            "status" => 200,
                        ], 200);
                    }else{
                        return response([
                            "success" => false,
                            "errors" => ["value" => ["Une erreur s'est produite lors de la modification du nom"]],
                            "status" => 401,
                        ], 401);
                    }
                }else{
                    return response([
                        "success" => false,
                        "errors" => ["value" => ["Une erreur s'est produite, cet agent n'existe pas!"]],
                        "status" => 401,
                    ], 401);
                }
            }else if(intval($validatorRequest["id_champs"]) === 8){
                // Email
                $validatorChamps = $request->validate([
                    'value' => ['required', 'email'],
                ]);

                $userTab = User::where('id_user', intval($agent?->id))->first();

                if ($userTab) {
                    $updateUser = $userTab->update([
                        'email' => $validatorChamps["value"],
                    ]);

                    return response([
                        "success" => true,
                        "message" => "Le service à été modifié avec succès",
                        "status" => 200,
                    ], 200);
                }else{
                    return response([
                        "success" => false,
                        "errors" => ["value" => ["Une erreur s'est produite, cet agent n'existe pas!"]],
                        "status" => 401,
                    ], 401);
                }
            }else if(intval($validatorRequest["id_champs"]) === 9){
                // Service
                $validatorChamps = $request->validate([
                    'value' => ['required', 'exists:services,id'],
                ]);

                if($agent){
                    $updateAgent = $agent->update([
                        "id_service" => $validatorChamps["value"],
                    ]);

                    if($updateAgent){
                        return response([
                            "success" => true,
                            "message" => "Le service à été modifié avec succès",
                            "status" => 200,
                        ], 200);
                    }else{
                        return response([
                            "success" => false,
                            "errors" => ["value" => ["Une erreur s'est produite lors de la modification du nom"]],
                            "status" => 401,
                        ], 401);
                    }
                }else{
                    return response([
                        "success" => false,
                        "errors" => ["value" => ["Une erreur s'est produite, cet agent n'existe pas!"]],
                        "status" => 401,
                    ], 401);
                }
            }else if(intval($validatorRequest["id_champs"]) === 10){
                // Fonction
                $validatorChamps = $request->validate([
                    'value' => ['required', 'exists:fonctions,id'],
                ]);

                if($agent){
                    $updateAgent = $agent->update([
                        "id_fonction" => $validatorChamps["value"],
                    ]);

                    if($updateAgent){
                        return response([
                            "success" => true,
                            "message" => "La fonction à été modifié avec succès",
                            "status" => 200,
                        ], 200);
                    }else{
                        return response([
                            "success" => false,
                            "errors" => ["value" => ["Une erreur s'est produite lors de la modification du nom"]],
                            "status" => 401,
                        ], 401);
                    }
                }else{
                    return response([
                        "success" => false,
                        "errors" => ["value" => ["Une erreur s'est produite, cet agent n'existe pas!"]],
                        "status" => 401,
                    ], 401);
                }
            }else{
                return response([
                    "success" => false,
                    "errors" => ["value" => ["Une erreur s'est produite, ce champs n'existe pas!"]],
                    "status" => 401,
                ], 401);
            }
        } catch (ValidationException $exception) {
            return response()->json([
                'errors' => $exception->validator->errors(),
                'status' => 422,
            ], 422)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if (auth()->check()){
                $userAuth = auth()->user();

                if ($userAuth->admins || $userAuth->id === intval($id)){
                    $user = User::find(intval($id));

                    if ($user) {
                        User::destroy(intval($id));
                        
                        $user->tokens()->each(function($token, $key){
                            $token->delete();
                        });

                        $qr_filename = "/qrcodes/$id.svg";
                        if (file_exists("../public$qr_filename")) {
                            unlink("../public$qr_filename");
                        }

                        return response([
                            "success" => true,
                            "message" => "L'utiliateur ".$user?->nom." ".$user?->prenom." a été supprimé avec success", 
                            "status" => 200
                        ], 200);
                    }else{
                        return response([
                            "success" => false,
                            "message" => "Aucun employé trouvé",
                            "status" => 401
                        ], 401);
                    }
                }else{
                    return response([
                        "success" => false,
                        "message" => "Acces réfusé",
                        "status" => 401
                    ], 401);
                }
            }else{
                return response([
                    "success" => false,
                    "message" => "Veuillez vérifier que vous êtes authentifié",
                    "status" => 401
                ], 401);
            }

        } catch (ValidationException $exception) {
            return response()->json([
                'errors' => $exception->validator->errors(),
                'message' => 'Une ou plusieurs erreurs se sont produites.',
            ], 422)->header('Content-Type', 'application/json');
        }
    }
}
