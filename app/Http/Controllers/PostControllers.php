<?php

namespace App\Http\Controllers;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\CodeQr;
use App\Models\Pointage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Dompdf\Dompdf;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use DateTime;

session_start();

function firstLetterUpper($mot)
{
    $firstLetter = strtoupper(substr($mot, 0, 1));
    $restLetters = strtolower(substr($mot, 1));

    return $firstLetter . '' . $restLetters;
}

function breakPhrase($phrase)
{
    $remplacer = array(" ");

    $recupPhrase = $phrase;

    $breakStrPhrase = trim(str_replace($remplacer, " ", $recupPhrase));

    $separateur = "#[ ]+#";
    $mots = preg_split($separateur, $breakStrPhrase);

    $nbrMots = count($mots);

    $return = "";

    for ($i = 0; $i < $nbrMots; $i++) {
        $word = firstLetterUpper($mots[$i]);
        $return = $return . ' ' . $word;
    }

    return $return;
}

function valid_donnees($donnees)
{
    $donnees = trim($donnees);
    $donnees = stripslashes($donnees);
    $donnees = htmlspecialchars($donnees);

    return $donnees;
}

class PostControllers extends Controller
{
    public function index()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                return view('home');
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function historiquePointages()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                if ($user->admins) {
                    return view('historique-pointages');
                } else {
                    return redirect('/');
                }
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function connexion()
    {
        return view('connexion');
    }

    public function logout()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            unset($_SESSION["id"]);
            unset($_SESSION['admins']);
            return redirect('/connexion');
        } else {
            return redirect('/connexion');
        }
    }

    /**
     * Generer pointages
     */
    public function insertPointage()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                $all_user = User::all();
                date_default_timezone_set('UTC');

                $date = "2021-02-01";

                for ($i=1; $i < 817; $i++) { 
                    $date = strftime("%Y-%m-%d", strtotime("$date +1 day"));

                    $set_date = new DateTime("$date");
                    $set_date = $set_date->getTimestamp();

                    if(intval(date("N", $set_date)) !== 7 && intval(date("N", $set_date)) !== 6){
                        $verification = Pointage::where('date_actuelle', $date);
                        if(!$verification->exists()){
                            foreach ($all_user as $key => $item) {
                                Pointage::create([
                                    'id_agent' => $item->id,
                                    'id_admin' => valid_donnees(intval($_SESSION['id'])),
                                    'date_actuelle' => $date,
                                ]);
                            }
                        }else{
                            foreach ($all_user as $key => $item){
                                $numbersU = rand(1, 28);
                                $year = intval(date("Y", $set_date));
                                $mois = intval(date("m", $set_date));
                                $jour = intval(date("d", $set_date));
                                $jrAbsent = "$year-$mois-$numbersU";
                                $jrAbsent = new DateTime("$jrAbsent");
                                $jrAbsent = $jrAbsent->getTimestamp();

                                if (date('Y-m-d', $jrAbsent) === date("Y-m-d",$set_date)) {
                                    echo date('Y-m-d', $jrAbsent)."--------------".date("Y-m-d",$set_date)."--------------";
                                }else{
                                    $verification = Pointage::where('date_actuelle', $date)->where('id_agent', $item->id)->get()[0];
                                    if ($verification->heure_arrivee === null) {
                                        date_default_timezone_set('UTC');
                                        $current = time();
                                        $heure = date('H:i', $current);
                                        $update = Pointage::where('date_actuelle', $date)->where('id_agent', $item->id)->update([
                                            'heure_arrivee' => $heure,
                                        ]);
                                    }else{
                                        date_default_timezone_set('UTC');
                                        $current = time();
                                        $heure = date('H:i', $current);

                                        $heure1 = new DateTime($verification->heure_arrivee);
                                        $heure2 = new DateTime($verification->heure_depart);
                                        $difference = $heure2->diff($heure1);

                                        $total_heure = $difference->format("%H:%i");
                                        $update = Pointage::where('date_actuelle', $date)->where('id_agent', $item->id)->update([
                                            'heure_depart' => $heure,
                                            'total_heure' => $total_heure,
                                        ]);
                                    }
                                }
                            }
                        }
                    }else{
                        echo "weekend";
                    }
                }

                // $date = "2023-01-1";

                // for ($i=1; $i < 18; $i++) { 
                //     $date = strftime("%Y-%m-%d", strtotime("$date +1 day"));

                //     $set_date = new DateTime("$date");
                //     $set_date = $set_date->getTimestamp();
                //     echo $date."---------------";
                //     if(intval(date("N", $set_date)) !== 7 && intval(date("N", $set_date)) !== 6){
                //         $verification = Pointage::where('date_actuelle', $date);
                //         if(!$verification->exists()){
                //             foreach ($all_user as $key => $item) {
                //                 Pointage::create([
                //                     'id_agent' => $item->id,
                //                     'id_admin' => valid_donnees(intval($_SESSION['id'])),
                //                     'date_actuelle' => $date,
                //                 ]);
                //             }
                //         }else{
                //             foreach ($all_user as $key => $item){
                //                 $numbersU = rand(1, 28);
                //                 $year = intval(date("Y", $set_date));
                //                 $mois = intval(date("m", $set_date));
                //                 $jour = intval(date("d", $set_date));
                //                 $jrAbsent = "$year-$mois-$numbersU";
                //                 $jrAbsent = new DateTime("$jrAbsent");
                //                 $jrAbsent = $jrAbsent->getTimestamp();

                //                 if (date('Y-m-d', $jrAbsent) === date("Y-m-d",$set_date)) {
                //                     echo date('Y-m-d', $jrAbsent)."--------------".date("Y-m-d",$set_date)."--------------";
                //                 }else{
                //                     $verification = Pointage::where('date_actuelle', $date)->where('id_agent', $item->id)->get()[0];
                //                     if ($verification->heure_arrivee === null) {
                //                         date_default_timezone_set('UTC');
                //                         $current = time();
                //                         $heure = date('H:i', $current);
                //                         $update = Pointage::where('date_actuelle', $date)->where('id_agent', $item->id)->update([
                //                             'heure_arrivee' => $heure,
                //                         ]);
                //                     }else{
                //                         date_default_timezone_set('UTC');
                //                         $current = time();
                //                         $heure = date('H:i', $current);

                //                         $heure1 = new DateTime($verification->heure_arrivee);
                //                         $heure2 = new DateTime($verification->heure_depart);
                //                         $difference = $heure2->diff($heure1);

                //                         $total_heure = $difference->format("%H:%i");
                //                         $update = Pointage::where('date_actuelle', $date)->where('id_agent', $item->id)->update([
                //                             'heure_depart' => $heure,
                //                             'total_heure' => $total_heure,
                //                         ]);
                //                     }
                //                 }
                //             }
                //         }
                //     }else{
                //         // echo "weekend";
                //     }
                // }
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function nouvelleJournee()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                if ($user->admins) {
                    $agent = User::all();
                    $nbrAgent = count($agent);

                    date_default_timezone_set('UTC');
                    $now = time();

                    $date = date('Y-m-d', $now);

                    $verification = Pointage::where('date_actuelle', $date);

                    if (!$verification->exists()) {
                        foreach ($agent as $key => $item) {
                            Pointage::create([
                                'id_agent' => $item->id,
                                'id_admin' => valid_donnees(intval($_SESSION['id'])),
                                'date_actuelle' => $date,
                            ]);
                        }

                        return redirect()->back();
                    } else {
                        return redirect()->back();
                    }
                } else {
                    return redirect('/');
                }
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function rapportIndividuel()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                return view('rapport-individuel');
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function rapportIndividuelAgent()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                if ($user->admins) {
                    return view('rapport-individuel-agent');
                } else {
                    return redirect('/');
                }
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function agents()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                if ($user->admins) {
                    return view('agents');
                } else {
                    return redirect('/');
                }
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function tableauDeBord()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                if ($user->admins) {
                    return view('tableau-de-bord');
                } else {
                    return redirect('/');
                }
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function menu()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                return view('profil');
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function profil()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                return view('profil');
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function modifierProfil()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                return view('modifier-profil');
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function preloadDataForm()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                return view('preloads.data-preload');
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function preloadDataDate(Request $request)
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                $BACKGROUND_NORMAL = 'rgba(174, 174, 174, 0.771)';
                $BACKGROUND_CURRENT = '#317AC1';
                $BACKGROUND_SUCCESS = 'rgba(0, 128, 0, 0.771)';
                $BACKGROUND_DANGER = 'rgba(255, 0, 0, 0.771)';

                if (isset($_GET['action']) && !empty($_GET['action'])) {
                    if (valid_donnees($_GET['action']) === 'HISTORY') {
                        if (isset($_GET['agent']) && !empty($_GET['agent'])) {
                            $agent = intval(valid_donnees($_GET["agent"]));
                        } else {
                            $agent = intval(valid_donnees($_SESSION["id"]));
                        }
                    } else {
                        $agent = intval(valid_donnees($_SESSION["id"]));
                    }
                } else {
                    $agent = intval(valid_donnees($_SESSION["id"]));
                }

                if (User::find($agent) === null) {
                    $agent = intval(valid_donnees($_SESSION["id"]));
                }

                $date = valid_donnees($request['date']);

                date_default_timezone_set('UTC');
                $now = time();
                $currentDate = date('Y-m-d', $now);
                $current_day = date('d', strtotime($currentDate));
                $current_month = date('m', strtotime($currentDate));
                $current_year = date('Y', strtotime($currentDate));

                if (!isset($request['date'])) {
                    $date = $currentDate;
                }

                $day = date('d', strtotime($date));
                $month = date('m', strtotime($date));
                $year = date('Y', strtotime($date));



                $reqPointage = Pointage::where('date_actuelle', $date)->where('id_agent', $agent);

                if (isset($request['filter']) && !empty($request['filter'])) {
                    if (valid_donnees($request['filter']) === 'day') {
                        $filterAction = 'day';
                    } else if (valid_donnees($request['filter']) === 'month') {
                        $filterAction = 'month';
                    } else if (valid_donnees($request['filter']) === 'year') {
                        $filterAction = 'year';
                    } else {
                        $filterAction = 'day';
                    }
                } else {
                    $filterAction = 'day';
                }

                if ($year === $current_year && $month === $current_month && $day === $current_day) {
                    $BACKGROUND = $BACKGROUND_CURRENT;
                    if ($filterAction === 'day' || $filterAction === 'month') {
                        $SEND = '<div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(0, 0, 0, 0.622);font-size: 0.8rem;">Journée en cours</div>';
                    } else if ($filterAction === 'year') {
                        $SEND = '';
                    }
                    $onclick = "window.location.href = `?filter=day&month=$month&year=$year&day=$day`";
                } else {
                    if ($reqPointage->exists()) {
                        $recupInfoPointage = $reqPointage->get()[0];

                        if ($recupInfoPointage->heure_arrivee !== null && $recupInfoPointage->heure_depart !== null) {
                            $BACKGROUND = $BACKGROUND_SUCCESS;
                            if ($filterAction === 'day' || $filterAction === 'month') {
                                $SEND = '<div title="Heure total(s)' . str_replace(":", 'h', $recupInfoPointage->total_heure) . 'm" style="width: 100%; height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(255, 255, 255, 0.622);font-size: 0.6rem;">' . $recupInfoPointage->heure_arrivee . ' / ' . $recupInfoPointage->heure_depart . '</div><div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(255, 255, 255, 0.622);font-size: 0.6rem;">H.T. ' . str_replace(":", 'h', $recupInfoPointage->total_heure) . 'm</div>';
                            } else if ($filterAction === 'year') {
                                $SEND = '';
                            }
                            $onclick = "window.location.href = `?filter=day&month=$month&year=$year&day=$day`";
                        } else {
                            $BACKGROUND = $BACKGROUND_DANGER;
                            $verifAdmins = User::find(intval($_SESSION['id']));

                            if (strlen($recupInfoPointage->motif) >= 10) {
                                $motif = substr("$recupInfoPointage->motif", 0, 10) . '...';
                            } else {
                                $motif = $recupInfoPointage->motif;
                            }

                            if ($recupInfoPointage->motif !== null) {
                                if ($filterAction === 'day' || $filterAction === 'month') {
                                    $SEND = '<div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(255, 255, 255, 0.622);font-size: 0.8rem;" title="' . $recupInfoPointage->motif . '">' . $motif . '</div>';
                                } else if ($filterAction === 'year') {
                                    $SEND = '';
                                }
                                if ($verifAdmins->exists()) {
                                    if ($verifAdmins->admins) {
                                        $onclick = "justify('$date','$agent')";
                                    } else {
                                        $onclick = "window.location.href = `?filter=day&month=$month&year=$year&day=$day`";
                                    }
                                }
                            } else {
                                if ($filterAction === 'day' || $filterAction === 'month') {
                                    $SEND = '<div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: #000000C0;font-size: 0.8rem;">Non Justifié</div>';
                                } else if ($filterAction === 'year') {
                                    $SEND = '';
                                }
                                if ($verifAdmins->exists()) {
                                    if ($verifAdmins->admins) {
                                        $onclick = "justify('$date','$agent')";
                                    } else {
                                        $onclick = "window.location.href = `?filter=day&month=$month&year=$year&day=$day`";
                                    }
                                }
                            }
                        }
                    } else {
                        $BACKGROUND = $BACKGROUND_NORMAL;
                        $SEND = '';
                        $onclick = "window.location.href = `?filter=day&month=$month&year=$year&day=$day`";
                    }
                }

                if ($filterAction === 'day' || $filterAction === 'month') {
                    $return = '<div class="day" onclick="' . $onclick . '" style="
                        width: 100%;
                        height: 10vh;
                        font-weight: bold;
                        border: solid 1px rgba(255, 255, 255, 0.4);
                        background: ' . $BACKGROUND . ';
                    ">
                        <div style="padding: 0.5rem 0 0 0.5rem;">
                            <div id="box-day-3">' . $day . '</div>
                            ' . $SEND . '
                        </div>
                    </div>';
                } else if ($filterAction === 'year') {
                    $return = '<div class="day-list" style="
                        width: 100%;
                        height: 100%;
                        border: solid 1px rgba(255, 255, 255, 0.699);
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        background: ' . $BACKGROUND . ';
                    ">' . $day . '</div>';
                }

                return $return;
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return false;
        }
    }

    public function preload(Request $request)
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                $retAdmins = User::find(intval($_SESSION['id']));
                $retAdmins = $retAdmins->admins;

                if ($request['action'] === 'INSERT_MOTIF') {
                    if ($retAdmins) {
                        $agent_absent = valid_donnees($request['agent']);
                        $motif_absent = trim(valid_donnees($request['motif']));
                        $date_absent = valid_donnees($request['date']);

                        $reqPointageAgent = Pointage::where('id_agent', $agent_absent)->where('date_actuelle', $date_absent);

                        if ($reqPointageAgent->exists()) {
                            $requete = $reqPointageAgent->get()[0];
                            if ($requete->heure_depart === null) {
                                $update_motif = Pointage::where('id_agent', $agent_absent)->where('date_actuelle', $date_absent)->update([
                                    'motif' => $motif_absent,
                                ]);

                                if ($update_motif) {
                                    return true;
                                } else {
                                    return false;
                                }
                            } else {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return false;
        }
    }

    public function updateQrCode()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                $myuuid = Uuid::uuid4();
                $path = '../public/qrcodes/' . $_SESSION['id'] . '.svg';

                QrCode::size(200)->generate($myuuid, $path);
                CodeQr::update([
                    "id_user" => $_SESSION['id'],
                    "token" => $myuuid,
                ]);

                return redirect()->back();
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function scanner()
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                if ($user->admins) {
                    return view('scan');
                } else {
                    return redirect('/');
                }
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function scan(Request $request)
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                if ($user->admins) {
                    if (isset($request['agent']) && !empty($request['agent']) && isset($request['pointage']) && !empty($request['pointage'])) {
                        $agent = User::find(intval(valid_donnees($request['agent'])));

                        if ($agent) {
                            date_default_timezone_set('UTC');
                            $now = time();

                            $date = date('Y-m-d', $now);
                            $heure = date('H:i', $now);

                            $verifNewDay = Pointage::where('date_actuelle', $date);

                            $verification = Pointage::where('date_actuelle', $date)->where('id_agent', valid_donnees($request['agent']))->first();


                            if ($verifNewDay->exists()) {
                                if ($verification) {
                                    $verif = true;
                                } else {
                                    // Ajouter le nouvel utilisateur apres le debut d'une nouvelle journée
                                    $addPointageNewUser = Pointage::create([
                                        'id_agent' => valid_donnees($request['agent']),
                                        'id_admin' => valid_donnees(intval($_SESSION['id'])),
                                        'date_actuelle' => $date,
                                    ]);

                                    // Verifier si l'on peut continuer
                                    if ($addPointageNewUser) {
                                        $verif = true;
                                    } else {
                                        $verif = false;
                                    }
                                }

                                if ($request['pointage'] === 'arrivee') {
                                    // POINTAGE ARRIVEE
                                    // Si verif est vrai on continue
                                    if ($verif) {
                                        if ($verification?->heure_arrivee === null) {
                                            // Aucun pointage donc on peut le pointer
                                            Pointage::where('date_actuelle', $date)->where('id_agent', valid_donnees($request['agent']))->update([
                                                'id_agent' => valid_donnees($request['agent']),
                                                'id_admin' => valid_donnees(intval($_SESSION['id'])),
                                                'heure_arrivee' => $heure,
                                            ]);

                                            return redirect()->back()->with('success', 'sucess');
                                        } else {
                                            // L'utilisateur à déjà été pointé
                                            return redirect()->back()->withErrors("2");
                                        }
                                    } else {
                                        return redirect()->back()->withErrors("2");
                                    }
                                } else if ($request['pointage'] === 'depart') {
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

                                                Pointage::where('date_actuelle', $date)->where('id_agent', valid_donnees($request['agent']))->update([
                                                    'id_admin' => valid_donnees(intval($_SESSION['id'])),
                                                    'heure_depart' => $heure,
                                                    'total_heure' => $total_heure,
                                                ]);

                                                return redirect()->back()->with('success', 'sucess');
                                            } else {
                                                return redirect()->back()->withErrors("4");
                                            }
                                        } else {
                                            return redirect()->back()->withErrors("3");
                                        }
                                    } else {
                                        return redirect()->back()->withErrors("2");
                                    }
                                } else {
                                    return redirect()->back()->withErrors("1");
                                }
                            } else {
                                return redirect()->back()->withErrors("5");
                            }
                        } else {
                            return redirect()->back()->withErrors("1");
                        }
                    } else {
                        return redirect()->back()->withErrors("1");
                    }
                } else {
                    return redirect('/');
                }
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }

    public function rapport($agent, $date)
    {
        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            // Connected
            $user = User::find(intval($_SESSION["id"]));

            if ($user) {
                // $snappy = \App::make('snappy.pdf');

                // $html = view('generer-rapport', ['id_agent' => $agent, 'date_rapport' => $date])->render();

                // $name_file = $agent;
                // if (file_exists("pdf/$agent.pdf")) {
                //     unlink("pdf/$agent.pdf");
                //     $snappy->generateFromHtml($html, public_path("pdf/$name_file.pdf"));
                // } else {
                //     $snappy->generateFromHtml($html, public_path("pdf/$name_file.pdf"));
                // }

                // return redirect()->back()->with('success', 'PDF a été géneré avec succès');

                $name_file = $agent;
                
                $dompdf = new Dompdf();

                // Récupérer la vue
                $html = view('generer-rapport', ['id_agent' => $agent, 'date_rapport' => $date])->render();

                // Générer le PDF
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4');
                $dompdf->render();

                //Télécharger le PDF
                $dompdf->stream("$name_file.pdf");

                return redirect()->back();
            } else {
                unset($_SESSION["id"]);
                unset($_SESSION['admins']);
                return redirect('/connexion');
            }
        } else {
            return redirect('/connexion');
        }
    }
}
