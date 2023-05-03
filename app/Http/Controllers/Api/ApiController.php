<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Pointage;
use App\Models\CodeQr;
use App\Models\User;
use App\Models\Sous_direction;
use App\Models\Direction;
use App\Models\Service;
use App\Models\Fonction;
use DateTime;

function formatHours($date)
{
    $time_str = "$date";
    $time_stamp = strtotime($time_str);
    $time_formatted = date('H\h:i\m', $time_stamp);
    return $time_formatted;
}

class ApiController extends Controller
{
    public function scan(Request $request)
    {
        if (auth()->check()) {
            $userAuth = auth()->user();

            if ($userAuth->admins) {
                try {
                    $scanValidation = $request->validate([
                        'token' => 'required|exists:code_qrs,token',
                        'pointage' => 'required|string|in:arrivee,depart',
                    ]);

                    date_default_timezone_set('UTC');
                    $now = time();

                    $date = date('Y-m-d', $now);
                    $heure = date('H:i', $now);

                    $verifNewDay = Pointage::where('date_actuelle', $date);

                    $token = CodeQr::where('token', $scanValidation['token'])->first();

                    $id_agent = $token?->id_user;
                    $searchAgent = User::find(intval($id_agent));
                    $nameAgent = $searchAgent?->nom . " " . $searchAgent?->prenom;

                    $verification = Pointage::where('date_actuelle', $date)->where('id_agent', $id_agent)->first();

                    if ($verifNewDay->exists()) {
                        if ($verification) {
                            $verif = true;
                        } else {
                            // Ajouter le nouvel utilisateur apres le debut d'une nouvelle journée
                            $addPointageNewUser = Pointage::create([
                                'id_agent' => $id_agent,
                                'id_admin' => $userAuth->id,
                                'date_actuelle' => $date,
                            ]);

                            // Verifier si l'on peut continuer
                            if ($addPointageNewUser) {
                                $verif = true;
                            } else {
                                $verif = false;
                            }
                        }

                        if ($scanValidation['pointage'] === 'arrivee') {
                            // POINTAGE ARRIVEE
                            // Si verif est vrai on continue
                            if ($verif) {
                                if ($verification?->heure_arrivee === null) {
                                    // Aucun pointage donc on peut le pointer
                                    Pointage::where('date_actuelle', $date)->where('id_agent', $id_agent)->update([
                                        'id_agent' => $id_agent,
                                        'id_admin' => $userAuth->id,
                                        'heure_arrivee' => $heure,
                                    ]);

                                    $addArrivee = Pointage::where('date_actuelle', $date)->where('id_agent', $id_agent)->first();

                                    return response([
                                        "success" => true,
                                        "data" => $addArrivee,
                                        "message" => "Pointage de $nameAgent, Heure d'arrivée " . formatHours($addArrivee->heure_arrivee) . " ajouter avec success",
                                        "name" => $nameAgent,
                                        "status" => 200,
                                    ], 200);
                                } else {
                                    // L'utilisateur à déjà été pointé
                                    return response([
                                        "success" => false,
                                        "data" => $verification,
                                        "message" => "$nameAgent à déjà été pointé à l'arrivée, à " . formatHours($verification->heure_arrivee) . "",
                                        "name" => $nameAgent,
                                        "status" => 200,
                                    ], 200);
                                }
                            } else {
                                return response([
                                    "success" => false,
                                    "message" => "Impossible d'effectuer le pointage car un echec s'est produite lors de l'ajout du nouvel employé.",
                                    "name" => $nameAgent,
                                    "status" => 200,
                                ], 200);
                            }
                        } else {
                            // POINTAGE DEPART
                            if ($verif) {
                                if ($verification?->heure_arrivee !== null) {
                                    // Si il y'a un pointage à l'arrivée donc on peut le pointer
                                    if ($verification?->heure_depart === null) {
                                        // Si l'utilisateur n'a pas déjà pointé on le pointe
                                        $heure1 = new DateTime($verification->heure_arrivee);
                                        $heure2 = new DateTime($verification->heure_depart);
                                        $difference = $heure2->diff($heure1);

                                        $total_heure = $difference->format("%H:%i");

                                        Pointage::where('date_actuelle', $date)->where('id_agent', $id_agent)->update([
                                            'id_admin' => $id_agent,
                                            'heure_depart' => $heure,
                                            'total_heure' => $total_heure,
                                        ]);

                                        $addDepart = Pointage::where('date_actuelle', $date)->where('id_agent', $id_agent)->first();

                                        return response([
                                            "success" => true,
                                            "data" => $addDepart,
                                            "message" => "Pointage de $nameAgent, Heure de départ " . formatHours($addDepart->heure_depart) . " ajouter avec success, arrivée à " . formatHours($addDepart->heure_arrivee) . " partir à " . formatHours($addDepart->heure_depart) . ", total d'heure de " . formatHours($addDepart->total_heure) . "",
                                            "name" => $nameAgent,
                                            "status" => 200,
                                        ], 200);
                                    } else {
                                        return response([
                                            "success" => false,
                                            "data" => $verification,
                                            "message" => "$nameAgent à déjà été pointé pour cette journée, arrivée à " . formatHours($verification->heure_arrivee) . " partir à " . formatHours($verification->heure_depart) . ", total d'heure de " . formatHours($verification->total_heure) . "",
                                            "name" => $nameAgent,
                                            "status" => 200,
                                        ], 200);
                                    }
                                } else {
                                    return response([
                                        "success" => false,
                                        "message" => "$nameAgent n'a pas encore été pointé à l'arrivée",
                                        "name" => $nameAgent,
                                        "status" => 200,
                                    ], 200);
                                }
                            } else {
                                return response([
                                    "success" => false,
                                    "message" => "Impossible d'effectuer le pointage car un echec s'est produite lors de l'ajout du nouvel employé.",
                                    "name" => $nameAgent,
                                    "status" => 200,
                                ], 200);
                            }
                        }
                    } else {
                        return response([
                            "success" => false,
                            "message" => "Aucune journée n'a debutée",
                            "name" => $nameAgent,
                            "status" => 200,
                        ], 200);
                    }
                } catch (ValidationException $exception) {
                    return response()->json([
                        'errors' => $exception->validator->errors(),
                        'status' => 422,
                    ], 422)->header('Content-Type', 'application/json');
                }
            }
        } else {
            return response([
                "success" => false,
                "message" => "Veuillez vérifier que vous êtes authentifié",
                "status" => 401
            ], 401);
        }
    }

    // To review
    public function currentStatus()
    {
        date_default_timezone_set('UTC');
        $now = time();

        $date = date('Y-m-d', $now);
        $heure = date('H:i', $now);

        $verifExistence = Pointage::where('date_actuelle', $date);

        if ($verifExistence->exists()) {
            $verifAgent = User::all();

            if (!$verifAgent->isEmpty()) {

                $dataStatusAgents = [];

                foreach ($verifAgent as $key => $item) {
                    $verification = Pointage::where('date_actuelle', $date)->where('id_agent', $item?->id)->first();

                    if ($verification?->heure_arrivee === null) {
                        $status = false;
                    } else {
                        $status = true;
                    }

                    $dataStatusAgent = new \stdClass(); // Création d'un nouvel objet vide
                    $dataStatusAgent->id = $item?->id; // Attribution des valeurs
                    $dataStatusAgent->status = $status;

                    array_push($dataStatusAgents, $dataStatusAgent); // Ajout de l'objet dans le tableau

                }

                return $dataStatusAgents;
            }
        } else {
            return response([
                "success" => false,
                "message" => false,
                "status" => 200,
            ], 200);
        }
    }

    public function checkNewDay()
    {
        date_default_timezone_set('UTC');
        $now = time();

        $date = date('Y-m-d', $now);

        $verification = Pointage::where('date_actuelle', $date);

        if ($verification->exists()) {
            return response([
                "success" => true,
                "message" => "Une nouvelle journée à déjà débutée",
                "status" => 200,
            ], 200);
        } else {
            return response([
                "success" => false,
                "message" => "Aucune journée n'a débutée",
                "status" => 200,
            ], 200);
        }
    }

    public function newDay()
    {
        if (auth()->check()) {
            $userAuth = auth()->user();

            if ($userAuth->admins) {
                $agent = User::all();
                $nbrAgent = count($agent);

                date_default_timezone_set('UTC');
                $now = time();

                $date = date('Y-m-d', $now);

                $verification = Pointage::where('date_actuelle', $date);

                if (!$verification->exists()) {
                    foreach ($agent as $key => $item) {
                        $addDay = Pointage::create([
                            'id_agent' => $item->id,
                            'id_admin' => $userAuth->id,
                            'date_actuelle' => $date,
                        ]);
                    }

                    if ($addDay) {
                        return response([
                            "success" => true,
                            "message" => "Une nouvelle journée vient de débuter",
                            "status" => 200,
                        ], 200);
                    } else {
                        return response([
                            "success" => false,
                            "message" => "Une erreur s'est produite, Impossible de débuter une nouvelle journée",
                            "status" => 401,
                        ], 401);
                    }
                } else {
                    return response([
                        "success" => false,
                        "message" => "Une nouvelle journée à déjà débutée, impossible de désactiver",
                        "status" => 401,
                    ], 401);
                }
            } else {
                return response([
                    "success" => false,
                    "message" => "Accès réfusé",
                    "status" => 401,
                ], 401);
            }
        }
    }

    public function service()
    {
        $service = Service::with(['sous_direction'])->get();

        if (count($service) <= 0) {
            return response([
                "success" => false,
                "message" => "Aucune sous direction de trouvé",
                "status" => 401,
            ], 401);
        } else {
            return response([
                "success" => true,
                "data" => $service,
                "status" => 200
            ], 200);
        }
    }

    public function fonction()
    {
        $fonction = Fonction::all();

        if (count($fonction) <= 0) {
            return response([
                "success" => false,
                "message" => "Aucune sous direction de trouvé",
                "status" => 200,
            ], 200);
        } else {
            return response([
                "success" => true,
                "data" => $fonction,
                "status" => 200
            ], 200);
        }
    }

    public function sous_direction()
    {
        $sous_direction = Sous_direction::with(['direction'])->get();

        if (count($sous_direction) <= 0) {
            return response([
                "success" => false,
                "message" => "Aucune sous direction de trouvé",
                "status" => 200,
            ], 200);
        } else {
            return response([
                "success" => true,
                "data" => $sous_direction,
                "status" => 200
            ], 200);
        }
    }

    public function direction()
    {
        $direction = Direction::all();

        if (count($direction) <= 0) {
            return response([
                "success" => false,
                "message" => "Aucune sous direction de trouvé",
                "status" => 200,
            ], 200);
        } else {
            return response([
                "success" => true,
                "data" => $direction,
                "status" => 200
            ], 200);
        }
    }

    public function token($id)
    {
        $token = CodeQr::where('id_user', $id)->first();
        return response([
            "success" => true,
            "token" => $token?->token,
            "status" => 200,
        ], 200);
    }

    public function profilUser(Request $request)
    {

        if (auth()->check()) {
            $findAgent = auth()->user();
            $id = $findAgent->id;

            if (!$findAgent) {
                $dataAgent = [];
            } else {
                $agent = User::with(['service', 'fonction'])->findOrFail($id);
                $dataAgent = $agent;
            }

            // Verif status du jour si present ou absent
            date_default_timezone_set('UTC');
            $now = time();

            $date = date('Y-m-d', $now);
            $heure = date('H:i', $now);

            $verifExistence = Pointage::where('date_actuelle', $date);

            if ($verifExistence->exists()) {
                $verifAgent = User::all();

                if (!$verifAgent->isEmpty()) {
                    $verification = Pointage::where('date_actuelle', $date)->where('id_agent', $id)->first();

                    if ($verification?->heure_arrivee === null) {
                        $status = false;
                    } else {
                        $status = true;
                    }

                    $dataStatus = $status;
                }
            } else {
                $dataStatus = false;
            }

            // Sous direcetion
            if (intval($dataAgent?->service?->id_sous_direction) !== 0) {
                $sous_direction = Sous_direction::with(['direction'])->findOrFail(intval($dataAgent?->service?->id_sous_direction));
                $dataSD = $sous_direction;
            } else {
                $dataSD = "Secretariat";
            }

            // Recuperer token actuelle de son Code Qr
            $getToken = CodeQr::where('id_user', $id)->first();
            $token = $getToken?->token;

            //Retourner les données de l'employé
            $dataProfils = [
                "agent" => $dataAgent,
                "token" => $token,
                "agentStatus" => $dataStatus,
                "sousDirection" => $dataSD,
            ];

            return response()->json([
                "success" => true,
                "data" => $dataProfils,
                "status" => 200
            ]);
        } else {
            return response([
                "success" => false,
                "message" => "Veuillez vérifier que vous êtes authentifié",
                "status" => 401
            ], 401);
        }
    }

    public function profils(Request $request, $id = null)
    {
        if (!$id) {
            $userConnected = $request->user();
            $id = $userConnected?->id_user;
        }

        $findAgent = User::find($id);

        if (!$findAgent) {
            $dataAgent = [];
        } else {
            $agent = User::with(['service', 'fonction'])->findOrFail($id);
            $dataAgent = $agent;
        }


        date_default_timezone_set('UTC');
        $now = time();

        $date = date('Y-m-d', $now);
        $heure = date('H:i', $now);

        $verifExistence = Pointage::where('date_actuelle', $date);

        if ($verifExistence->exists()) {
            $verifAgent = User::all();

            if (!$verifAgent->isEmpty()) {
                $verification = Pointage::where('date_actuelle', $date)->where('id_agent', $id)->first();

                if ($verification?->heure_arrivee === null) {
                    $status = false;
                } else {
                    $status = true;
                }

                $dataStatus = $status;
            }
        } else {
            $dataStatus = false;
        }

        if (intval($dataAgent?->service?->id_sous_direction) !== 0) {
            $sous_direction = Sous_direction::with(['direction'])->findOrFail(intval($dataAgent?->service?->id_sous_direction));
            $dataSD = $sous_direction;
        } else {
            $dataSD = "Secretariat";
        }

        $getToken = CodeQr::where('id_user', $id)->first();
        $token = $getToken?->token;

        $dataProfils = [
            "agent" => $dataAgent,
            "token" => $token,
            "agentStatus" => $dataStatus,
            "sousDirection" => $dataSD,
        ];

        return response()->json([
            "success" => true,
            "data" => $dataProfils,
            "status" => 200
        ]);
    }
}
