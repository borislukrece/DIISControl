<?php

namespace App\Http\Controllers;

use Ramsey\Uuid\Uuid;

use App\Models\Service;
use App\Models\Fonction;
use App\Models\CodeQr;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

session_start();

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
    public function index(Request $request){
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('username', $request['username'])->first();

        if (!$user) return redirect()->back()->withErrors('3');

        if(Hash::check($request['password'], $user->password)){
            // Connecter l'utilisateur
            $_SESSION["id"] = $user->id;
            $_SESSION["admins"] = $user->admins;
            return redirect('/');
        } else{
            return redirect()->back()->withErrors("4 $user");
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION['id']));

            if ($user->admins){
                return view('ajouter-agent');
            }else{
                return redirect('/');
            }
        } else {
            return redirect('/connexion');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])){

            $_SESSION['input_nom'] = trim(breakPhrase(valid_donnees($request['nom'])));
            $_SESSION['input_prenom'] = trim(breakPhrase(valid_donnees($request['prenom'])));
            $_SESSION['input_tel'] = valid_donnees($request['tel']);
            $_SESSION['input_sexe'] = intval(valid_donnees($request['sexe']));
            $_SESSION['input_date_naissance'] = valid_donnees($request['date_naissance']);
            $_SESSION['input_lieu_naissance'] = trim(breakPhrase(valid_donnees($request['lieu_naissance'])));
            $_SESSION['input_id_fonction'] = intval(valid_donnees($request['id_fonction']));
            $_SESSION['input_id_service'] = intval(valid_donnees($request['id_service']));
            $_SESSION['input_adresse'] = trim(breakPhrase(valid_donnees($request['adresse'])));
            $_SESSION['input_email'] = valid_donnees(strtolower($request['email']));
            $_SESSION['input_username'] = valid_donnees(strtolower($request['username']));
            $_SESSION['input_admins'] = intval(valid_donnees($request['admins']));

            $request->validate([
                'nom' => ['required', 'between:1,30'],
                'prenom' => ['required', 'between:1,30'],
                'tel' => [
                    'required',
                    'regex:/^\+225(01|07|05)\d{8}$/',
                ], [
                    'tel.regex' => 'The phone number must be in the format +225xxxxxxxx.',
                ],
                'sexe' => ['required', 'boolean'],
                'date_naissance' => ['required', 'date_format:Y-m-d', 'before:01-01-2010'],
                'lieu_naissance' => ['required', 'between:1,125'],
                'id_service' => ['required', 'numeric', 'min:1'],
                'id_fonction' => ['required','numeric', 'min:1'],
                'adresse' => ['nullable', 'max:255'],
                'email' => ['required', 'email'],
                'username' => ['required', 'unique:users,username'],
                'password' => ['required', 'min:8', 'confirmed'],
                'admins' => ['required', 'boolean']
            ]);

            $addAgent = User::create([
                'nom' => trim(breakPhrase(valid_donnees($request['nom']))),
                'prenom' => trim(breakPhrase(valid_donnees($request['prenom']))),
                'tel' => valid_donnees($request['tel']),
                'sexe' => intval(valid_donnees($request['sexe'])),
                'date_naissance' => valid_donnees($request['date_naissance']),
                'lieu_naissance' => trim(breakPhrase(valid_donnees($request['lieu_naissance']))),
                'id_fonction' => intval(valid_donnees($request['id_fonction'])),
                'id_service' => intval(valid_donnees($request['id_service'])),
                'adresse' => trim(breakPhrase(valid_donnees($request['adresse']))),
                'email' => valid_donnees(strtolower($request['email'])),
                'username' => valid_donnees(strtolower($request['username'])),
                'password' => bcrypt($request->password),
                'admins' => intval(valid_donnees($request['admins'])),
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
            }
            
            unset($_SESSION['input_nom']);
            unset($_SESSION['input_prenom']);
            unset($_SESSION['input_tel']);
            unset($_SESSION['input_sexe']);
            unset($_SESSION['input_date_naissance']);
            unset($_SESSION['input_lieu_naissance']);
            unset($_SESSION['input_id_fonction']);
            unset($_SESSION['input_id_service']);
            unset($_SESSION['input_adresse']);
            unset($_SESSION['input_email']);
            unset($_SESSION['input_username']);
            unset($_SESSION['input_admins']);

            $message = "L'employé ".trim(breakPhrase(valid_donnees($request['nom'])))." ".trim(breakPhrase(valid_donnees($request['prenom'])))." a été ajouter avec success";
            return redirect('/liste-agents')->with('success', $message);
        }else {
            return redirect('/connexion');
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
        //
    }

    /**
     * Update user profile picture .
     * @param  \Illuminate\Http\Request  $request
     */
    public function profile_pic (Request $request)
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])){
            // Connected
            $id = $_SESSION['id'];

            $user = User::find(intval($id));
            $name_profil_pic = $user?->profil_img ?? 'x.png';

            if ($user) {
                $validated = $request->validate([
                    'image' => 'image|mimes:jpeg,png,jpg|max:8048',
                ]);

                if (isset($request['image'])) {
                    // Update Picture
                    $imageName = $id.'.'. $request->image->getClientOriginalExtension();
                    $updatePic = User::find(intval($id))->update([
                        "profil_img" => $imageName,
                    ]);

                    if($updatePic){
                        if (file_exists("../public/profil_img/$name_profil_pic")) {
                            unlink("../public/profil_img/$name_profil_pic");
                        }

                        $request->image->move(public_path('profil_img'), $imageName);
                    }

                    return redirect()->back()->with('success', 'Votre photo à été modifié avec succès');
                }else{
                    // Destroy Picture
                    if($name_profil_pic){
                        $updatePic = User::find(intval($id))->update([
                            "profil_img" => null,
                        ]);

                        if($updatePic){
                            if (file_exists("../public/profil_img/$name_profil_pic")) {
                                unlink("../public/profil_img/$name_profil_pic");
                            }
                        }

                        return redirect()->back()->with('success', 'Votre photo à été retiré avec succès');
                    }else{
                        return redirect()->back();
                    }
                }
            }else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $id = $_SESSION['id'];

            if (intval($request['form']) === 1){
                // Modifier mes informations personnelles
                $agent = $request->validate([
                    'tel' => [
                        'required',
                        'regex:/^\+225(01|07|05)\d{8}$/',
                    ], [
                        'tel.regex' => 'Le numéro de téléphone doit être au format +225xxxxxxxx.',
                    ],
                    'adresse' => ['nullable', 'max:255'],
                    'email' => ['required', 'email'],
                ]);

                $updateAgent = User::find($id)->update([
                    'tel' => valid_donnees($agent['tel']),
                    'adresse' => trim(breakPhrase(valid_donnees($agent['adresse']))),
                    'email' => valid_donnees(strtolower($agent['email'])),
                ]);

                if ($updateAgent) {
                    $message = "Les modifications apporté ont été enregistré avec success";
                    return redirect()->back()->with('success', $message);
                } else {
                    return redirect()->back();
                }
                
            } else if(intval($request['form']) === 2) {
                // Modifier mes informations de connexion
                $dataAgent = User::find($id);

                $agent = $request->validate([
                    'current_mdp' => ['required', 'string'],
                    'password' => ['required', 'string', 'min:8', 'confirmed'],
                ]);

                if (Hash::check($agent['current_mdp'], $dataAgent->password)) {
                    $agentUpdate = $dataAgent->update([
                        'password' => bcrypt($agent["password"]),
                    ]);

                    if ($agentUpdate) {
                        $message = "Votre mot de passe a été modifié avec success";
                        return redirect()->back()->with('success', $message);
                    }else{
                        return redirect()->back();
                    }
                }else{
                    return redirect()->back()->withErrors('1');
                }
            } else if(intval($request['form']) === 3) {
                // Modifier mes informations personnelles par admin
                $id = valid_donnees($request['id']);
                
                $UsernameRetVal = (isset($request->username) && !empty($request->username)) ? 1 : 0 ;
                $MdpRetVal = (isset($request->password) && !empty($request->password) && isset($request->password_confirmation) && !empty($request->password_confirmation)) ? 1 : 0 ;

                $agent = $request->validate([
                    'nom' => ['required', 'between:1,30'],
                    'prenom' => ['required', 'between:1,30'],
                    'tel' => [
                        'required',
                        'regex:/^\+225(01|07|05)\d{8}$/',
                    ], [
                        'tel.regex' => 'The phone number must be in the format +225xxxxxxxx.',
                    ],
                    'sexe' => ['required', 'boolean'],
                    'date_naissance' => ['required', 'before:01-01-2010'],
                    'lieu_naissance' => ['required', 'between:1,125'],
                    'id_service' => ['required', 'numeric', 'min:1'],
                    'id_fonction' => ['required','numeric', 'min:1'],
                    'adresse' => ['nullable', 'max:255'],
                    'email' => ['required', 'email'],
                    'admins' => ['required', 'boolean']
                ]);

                if ($UsernameRetVal) {
                    if (strtolower($request['username']) !== strtolower(User::find($id)->username)) {
                        $request->validate([
                            'username' => ['required', 'unique:users,username'],
                        ]);
                    }
                }

                if ($MdpRetVal) {
                    $request->validate([
                        'password' => ['required', 'min:8', 'confirmed'],
                    ]);
                }

                $return = User::find($id)->update([
                    'nom' => trim(breakPhrase(valid_donnees($agent['nom']))),
                    'prenom' => trim(breakPhrase(valid_donnees($agent['prenom']))),
                    'tel' => valid_donnees($agent['tel']),
                    'sexe' => intval(valid_donnees($agent['sexe'])),
                    'date_naissance' => valid_donnees($agent['date_naissance']),
                    'lieu_naissance' => trim(breakPhrase(valid_donnees($agent['lieu_naissance']))),
                    'id_fonction' => intval(valid_donnees($agent['id_fonction'])),
                    'id_service' => intval(valid_donnees($agent['id_service'])),
                    'adresse' => trim(breakPhrase(valid_donnees($agent['adresse']))),
                    'email' => valid_donnees(strtolower($agent['email'])),
                    'admins' => intval(valid_donnees($agent['admins'])),
                ]);

                if ($UsernameRetVal) {
                    if (strtolower($request['username']) !== strtolower(User::find($id)->username)) {
                        $return = User::find($id)->update([
                            'username' => valid_donnees(strtolower($request['username'])),
                        ]);
                    }
                }

                if ($MdpRetVal) {
                    $return = User::find($id)->update([
                        'password' => valid_donnees($request['password']),
                    ]);
                }

                if (!isset($return)) {
                    $return = false;
                }

                if ($return) {
                    $message = "Les informations de l'employé ".trim(breakPhrase(valid_donnees($request['nom'])))." ".trim(breakPhrase(valid_donnees($request['prenom'])))." ont été modifié avec success";
                    return redirect()->back()->with('success', $message);
                }else{
                    return redirect()->back();
                }
            } else {
                // Aucun form choisis
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id = null)
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])){
            $agent = User::find(intval(valid_donnees($_GET['id'])));

            $message = "L'employé ".$agent->nom." ".$agent->prenom." à été supprimer avec succès";


            if ($agent) {
                User::destroy(intval(valid_donnees($_GET['id'])));
                
                $agent->tokens()->each(function($token, $key){
                    $token->delete();
                });

                $qr_filename = "qrcodes/$agent->id.svg";
                if (file_exists("../public/$qr_filename")) {
                    unlink("../public/$qr_filename");
                }
                if (intval($agent->id) === intval($_SESSION["id"])) {
                    unset($_SESSION["id"]);
                    unset($_SESSION['admins']);
                }

                return redirect()->back()->with('success', $message);
            }
        }else {
            return redirect('/connexion');
        }
    }
}
