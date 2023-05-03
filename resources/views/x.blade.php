<?php 
    use App\Models\User;
    use App\Models\Pointage;
    
    if (!empty($id_agent)) {
        $id = valid_donnees($id_agent);
        if (User::find($id_agent) === null) {
            $id = $_SESSION['id'];
        }
    }else{
        $id = $_SESSION['id'];
    }
    
    $agent = User::find($id);

    function valid_donnees($donnees){
        $donnees = trim($donnees);
        $donnees = stripslashes($donnees);
        $donnees = htmlspecialchars($donnees);

        return $donnees;
    }

    function DET_INDEX_DAY($date){
        $dates = new DateTime($date);
        $dates = $dates->getTimestamp();
        $SET_DAY_INDEX = intval(date("N", $dates));

        return $SET_DAY_INDEX;
    }

    function DET_NOM_SEMAINE($index, $action){
        switch ($action) {
            case "FULL":
                switch ($index) {
                    case 1:
                        return "Lundi";
                        break;
                    case 2:
                        return "Mardi";
                        break;
                    case 3:
                        return "Mercredi";
                        break;
                    case 4:
                        return "Jeudi";
                        break;
                    case 5:
                        return "Vendredi";
                        break;
                    case 6:
                        return "Samedi";
                        break;
                    case 7:
                        return "Dimanche";
                        break;

                    default:
                        break;
                }
                break;
            case "ABR":
                switch ($index) {
                    case 1:
                        return "Lun";
                        break;
                    case 2:
                        return "Mar";
                        break;
                    case 3:
                        return "Mer";
                        break;
                    case 4:
                        return "Jeu";
                        break;
                    case 5:
                        return "Ven";
                        break;
                    case 6:
                        return "Sam";
                        break;
                    case 7:
                        return "Dim";
                        break;

                    default:
                        break;
                }
                break;
            case "LETTER":
                switch ($index) {
                    case 1:
                        return "L";
                        break;
                    case 2:
                        return "M";
                        break;
                    case 3:
                        return "M";
                        break;
                    case 4:
                        return "J";
                        break;
                    case 5:
                        return "V";
                        break;
                    case 6:
                        return "S";
                        break;
                    case 7:
                        return "D";
                        break;

                    default:
                        break;
                }
                break;
        
            default:
                break;
        }
    }

    function getMoisLetter($i_mois,$i_year){
        $givenDate = new DateTime("$i_year-$i_mois-1");
        $givenDate = $givenDate->getTimestamp();

        return date('F', $givenDate);
    }

    date_default_timezone_set('UTC');
    $now = time();

    if (Pointage::first() !== null) {
        $debutPointage = new DateTime(Pointage::first()->date_actuelle);
        $debutPointage = $debutPointage->getTimestamp();
    }else{
        $debutPointage = time();
    }

    if (Pointage::latest('id')->first() !== null) {
        $lastedPointage = new DateTime(Pointage::latest('id')->first()->date_actuelle);
        $lastedPointage = $lastedPointage->getTimestamp();
    }else{
        $lastedPointage = time();
    }

    $firstDatePointage = date('Y-m-d', $debutPointage);
    $lastDatePointage = date('Y-m-d', $lastedPointage);

    $debutAnneeDePointage = date('Y', $debutPointage);

    if (!empty($date_rapport)) {
        $date = new DateTime(valid_donnees($date_rapport));
        $date = $date->getTimestamp();
    }else{
        $date = time();
    }

    $givenDate = date('Y-m-d', $date);

    $dateDebut = new DateTime("$firstDatePointage");
    $dateFin = new DateTime("$lastDatePointage");
    $dateDonne = new DateTime("$givenDate");

    if ($dateDonne > $dateFin) {
        $date = $dateFin->getTimestamp();
    } elseif ($dateDonne < $dateDebut) {
        $date = $dateDebut->getTimestamp();
    }

    $SET_YEAR = intval(date('Y', $date));
    $SET_MONTH = intval(date('m', $date));

    if ($SET_YEAR === intval(date('Y', $now)) && $SET_MONTH === intval(date('m', $now))){
        if($SET_MONTH === 1){
            $SET_MONTH = 12;
            $SET_YEAR = $SET_YEAR - 1;
        }else{
            $SET_MONTH = $SET_MONTH - 1;
        }
    }

    // Nombre de Jours Ouvrables
    $given_date = "$SET_YEAR-$SET_MONTH-1";

    $given_year = date('Y', strtotime($given_date));
    $given_month = date('m', strtotime($given_date));
    $nbr_jrs_mois = cal_days_in_month(CAL_GREGORIAN, $SET_MONTH, $SET_YEAR);

    $nbr_jrs_ouvres = 0;
    for ($i=1; $i <= $nbr_jrs_mois; $i++) { 
        if ($i < 10) $jrs = "0".$i;
        else $jrs = $i;
        $req = Pointage::where('date_actuelle', "$given_year-$given_month-$jrs");
        if ($req->exists()) {
            $nbr_jrs_ouvres++;
        }
    }

    // Nombre de Jours non Travaillé
    $given_date = "$SET_YEAR-$SET_MONTH-1";

    $given_year = date('Y', strtotime($given_date));
    $given_month = date('m', strtotime($given_date));
    $nbr_jrs_mois = cal_days_in_month(CAL_GREGORIAN, $SET_MONTH, $SET_YEAR);

    $nbr_jrs_non_travaille = 0;
    for ($i=1; $i <= $nbr_jrs_mois; $i++) { 
        if ($i < 10) $jrs = "0".$i;
        else $jrs = $i;
        $req = Pointage::where('date_actuelle', "$given_year-$given_month-$jrs");
        if (!$req->exists()) {
            $nbr_jrs_non_travaille++;
        }
    }

    // Durée d'absence
    $given_date = "$SET_YEAR-$SET_MONTH-1";

    $given_year = date('Y', strtotime($given_date));
    $given_month = date('m', strtotime($given_date));
    $nbr_jrs_mois = cal_days_in_month(CAL_GREGORIAN, $SET_MONTH, $SET_YEAR);

    $duree_absence = 0;
    $non_justifie = 0;

    for ($i=1; $i <= $nbr_jrs_mois; $i++) {
        if ($i < 10) $jrs = "0".$i;
        else $jrs = $i;
        $req = Pointage::where('date_actuelle', "$given_year-$given_month-$jrs")->where('id_agent', $agent->id);
        if ($req->exists()) {
            $reqPointage = $req->get()[0];
            if ($reqPointage->heure_arrivee === null || $reqPointage->heure_depart === null) {
                if ($reqPointage->motif === null) {
                    $non_justifie++;
                }
                $duree_absence++;
            }
        }
    }

    // Durée de presence
    $given_date = "$SET_YEAR-$SET_MONTH-1";

    $given_year = date('Y', strtotime($given_date));
    $given_month = date('m', strtotime($given_date));
    $nbr_jrs_mois = cal_days_in_month(CAL_GREGORIAN, $SET_MONTH, $SET_YEAR);

    $duree_presence = 0;

    for ($i=1; $i <= $nbr_jrs_mois; $i++) {
        if ($i < 10) $jrs = "0".$i;
        else $jrs = $i;
        $req = Pointage::where('date_actuelle', "$given_year-$given_month-$jrs")->where('id_agent', $agent->id);
        if ($req->exists()) {
            $reqPointage = $req->get()[0];
            if ($reqPointage->heure_arrivee !== null && $reqPointage->heure_depart !== null) {
                $duree_presence++;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ strtoupper($agent->nom) }} {{ $agent->prenom }} - Rapport Individuel de {{getMoisLetter($SET_MONTH,$SET_YEAR)}}-{{$SET_YEAR}}</title>
    
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }

        .item {
            flex: 1 1 200px;
            margin: 10px;
            padding: 20px;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body class="antialiased">
    <section>

        <div style="
            display: flex;
        ">
            <div style="
                width: 95%;
                height: 100%;
            ">
                <div style="width: 100%; height: 100%;">
                    <div style="width: 100%; height: 100%; display: flex;">
                        <div style="
                            width: 100%;
                            height: 100%;
                            background: rgba(255, 255, 255, 0.8);
                            padding: 1rem;
                        ">
                            <div style="
                                width: 100%;
                                height: 100%;
                            ">
                                {{-- <div><img src="{{ '/img/logo.png' }}" alt="logo" width="100" height="100"></div> --}}
                                <div style="font-weight: bold;font-size: 1.4rem;">Rapport Individuel <span style="color: gray;">({{ strtoupper($agent->nom) }} {{ $agent->prenom }})</span> | <span class="select_date">{{getMoisLetter($SET_MONTH,$SET_YEAR)}} {{$SET_YEAR}}</span></div>
                                <div style="
                                    width: 100%;
                                    display: flex;
                                    flex-direction: column;
                                    margin-top: 2rem;
                                ">
                                    <div style="
                                        width: 100%;
                                        height: 100%;
                                    ">
                                        <div style="
                                            width: 100%;
                                            height: 100%;
                                        ">
                                            <div>Employé: <span style="font-weight: bold;">{{ strtoupper($agent->nom) }} {{ $agent->prenom }}</span></div>
                                            <div style="padding-top: 1rem;">Date: <span class="select_date" style="font-weight: bold;">{{getMoisLetter($SET_MONTH,$SET_YEAR)}} {{$SET_YEAR}}</span></div>
                                            <div style="padding-top: 1rem;">Nombre de jours: <span class="select_date_nbrjrs" style="font-weight: bold;">{{cal_days_in_month(CAL_GREGORIAN, $SET_MONTH, $SET_YEAR)}}</span></div>
                                            <div style="padding-top: 1rem;">Nombre de jours ouvrés: <span id="select_date_nbrjrs_ouvres" style="font-weight: bold;">{{$nbr_jrs_ouvres}}</span></div>
                                            <div style="padding-top: 1rem;">Nombre de jours non travaillés: <span id="select_date_nbrjrs_non_travailles" style="color:gray;font-weight: bold;">{{$nbr_jrs_non_travaille}}</span></div>
                                            <div style="padding-top: 1rem;">Durée d'absences: <span id="select_date_duree_absence" style="font-weight: bold;">{{$duree_absence}} / <span style='color: gray;'>Non justifié </span> {{$non_justifie}}</span></div>
                                            <div style="padding-top: 1rem;">Durée de présences: <span id="select_date_duree_presence" style="font-weight: bold;">{{$duree_presence}}</span></div>
                                        </div>
                                    </div>
                                    <div style="
                                        width: 100%;
                                        height: 100%;
                                    ">
                                        <!-- MONTH -->
                                            <div style="
                                                width: 100%;
                                                height: 100%;
                                                margin-top: 2rem;
                                            ">
                                                <div style="font-weight: bold;font-size: 1.4rem;margin-bottom: 2rem;">Liste des présences et absences du mois</div>
                                                <div id="day-list" class="container" style="display: flex;">
                                                    <!-- MOIS -->
                                                    <?php
                                                        function getInfoPointageDay($annee,$mois,$jours,$id){
                                                            $BACKGROUND_NORMAL = '';
                                                            $BACKGROUND_CURRENT = '';
                                                            $BACKGROUND_SUCCESS = '';
                                                            $BACKGROUND_DANGER = '';
                                                            $BACKGROUND_JUSTIFIER = '';
                                                            $BACKGROUND_FERIE = 'rgba(0, 0, 0, 1)';

                                                            $idDay = "idDay$jours";

                                                            date_default_timezone_set('UTC');
                                                            $now = time();
                                                            $currentDate = date('Y-m-d', $now);
                                                            $current_day = date('d', strtotime($currentDate));
                                                            $current_month = date('m', strtotime($currentDate));
                                                            $current_year = date('Y', strtotime($currentDate));

                                                            $date = "$annee-$mois-$jours";
                                                            $day = date('d', strtotime($date));
                                                            $month = date('m', strtotime($date));
                                                            $year = date('Y', strtotime($date));

                                                            $reqPointage = Pointage::where('date_actuelle', "$year-$month-$day")->where('id_agent', $id);

                                                            ?>
                                                                <div id="$idDay" class="container" style="background: red;">
                                                                <?php 
                                                                    if ($year === $current_year && $month === $current_month && $day === $current_day) {
                                                                        $BACKGROUND = $BACKGROUND_NORMAL;
                                                                        $SEND = '<div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(0, 0, 0, 0.622);font-size: 0.8rem;"></div>';              
                                                                        $onclick = "";
                                                                    }else{
                                                                        if ($reqPointage->exists()) {
                                                                            $recupInfoPointage = $reqPointage->get()[0];

                                                                            if ($recupInfoPointage->heure_arrivee !== null && $recupInfoPointage->heure_depart !== null) {
                                                                                $BACKGROUND = $BACKGROUND_SUCCESS;
                                                                                $SEND = '<div title="Heure total(s)'.str_replace(":", 'h', $recupInfoPointage->total_heure).'m" style="width: 100%; height: 100%;display: flex;align-items: center;justify-content:center;color: black;font-size: 0.6rem;">'.$recupInfoPointage->heure_arrivee.' / '.$recupInfoPointage->heure_depart.' - Heure total(s) '.str_replace(":", 'h', $recupInfoPointage->total_heure).'m </div><div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(0, 0, 0, 0.622);font-size: 0.6rem;">Heure Totale '.str_replace(":", 'h', $recupInfoPointage->total_heure).'m</div>';
                                                                                $onclick = "";
                                                                            }else{
                                                                                $BACKGROUND = $BACKGROUND_DANGER;
                                                                                $verifAdmins = User::find(intval($_SESSION['id']));

                                                                                if (strlen($recupInfoPointage->motif) >= 10) {
                                                                                    $motif = substr("$recupInfoPointage->motif", 0, strlen($recupInfoPointage->motif));
                                                                                }else{
                                                                                    $motif = $recupInfoPointage->motif;
                                                                                }

                                                                                if ($recupInfoPointage->motif !== null) {
                                                                                    $BACKGROUND = $BACKGROUND_JUSTIFIER;
                                                                                    $SEND = '<div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: black;font-size: 0.8rem;" title="'.$recupInfoPointage->motif.'"> Absence - justifé<div>'.$motif.'</div></div>';
                                                                                    
                                                                                    if($verifAdmins->exists()){
                                                                                        if ($verifAdmins->admins) {
                                                                                            $onclick = "justify('$year-$month-$day','$id')";
                                                                                        }else{
                                                                                            $onclick = "";
                                                                                        }
                                                                                    }
                                                                                }else{
                                                                                    $SEND = '<div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: #000000C0;font-size: 0.8rem;">Absence - non Justifié</div>';
                                                                                    
                                                                                    if($verifAdmins->exists()){
                                                                                        if ($verifAdmins->admins) {
                                                                                            $onclick = "justify('$year-$month-$day','$id')";
                                                                                        }else{
                                                                                            $onclick = "";
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }else{
                                                                            $BACKGROUND = $BACKGROUND_NORMAL;
                                                                            $SEND = '';
                                                                            $onclick = "";
                                                                        }
                                                                    }

                                                                    if (intval($month) === 1 && intval($day) === 1){
                                                                        $text = "Jour de l'An";
                                                                        $SEND = '<div style="
                                                                        width: 100%;
                                                                        height: 100%;
                                                                        display: flex;
                                                                        align-items: center;
                                                                        justify-content:center;
                                                                        color: gray;
                                                                        font-size: 0.8rem;" 
                                                                        title="'.$text.'">
                                                                            '.$text.'
                                                                        </div>';
                                                                        $BACKGROUND = $BACKGROUND_FERIE;
                                                                        $onclick = "";
                                                                        $color = "color: gray";
                                                                    } else if (intval($month) === 5 && intval($day) === 1) {
                                                                        $text = "Fête du Travail";
                                                                        $SEND = '<div style="
                                                                        width: 100%;
                                                                        height: 100%;
                                                                        display: flex;
                                                                        align-items: center;
                                                                        justify-content:center;
                                                                        color: gray;
                                                                        font-size: 0.8rem;" 
                                                                        title="'.$text.'">
                                                                            '.$text.'
                                                                        </div>';
                                                                        $BACKGROUND = $BACKGROUND_FERIE;
                                                                        $onclick = "";
                                                                        $color = "color: gray";
                                                                    } else if (intval($month) === 12 && intval($day) === 25) {
                                                                        $text = "Noël";
                                                                        $SEND = '<div style="
                                                                        width: 100%;
                                                                        height: 100%;
                                                                        display: flex;
                                                                        align-items: center;
                                                                        justify-content:center;
                                                                        color: gray;
                                                                        font-size: 0.8rem;" 
                                                                        title="'.$text.'">
                                                                            '.$text.'
                                                                        </div>';
                                                                        $BACKGROUND = $BACKGROUND_FERIE;
                                                                        $onclick = "";
                                                                        $color = "color: gray";
                                                                    } else if (intval($month) === 12 && intval($day) === 26) {
                                                                        $text = "Lendemain de Noël";
                                                                        $SEND = '<div style="
                                                                        width: 100%;
                                                                        height: 100%;
                                                                        display: flex;
                                                                        align-items: center;
                                                                        justify-content:center;
                                                                        color: gray;
                                                                        font-size: 0.8rem;" 
                                                                        title="'.$text.'">
                                                                            '.$text.'
                                                                        </div>';
                                                                        $BACKGROUND = $BACKGROUND_FERIE;
                                                                        $onclick = "";
                                                                        $color = "color: gray";
                                                                    }else{
                                                                        $color = "";
                                                                    }

                                                                    $return = '<div class="item" style="
                                                                        width: 10vw;
                                                                        height: 8rem;
                                                                        font-weight: bold;
                                                                        border: solid 1px rgba(255, 255, 255, 0.4);
                                                                        background: '.$BACKGROUND.';
                                                                        background: pink;
                                                                        overflow: hidden;
                                                                    ">
                                                                        <div style="padding: 0.5rem 0 0 0.5rem;">
                                                                            <div>
                                                                                <div style="border-bottom: solid 1px gray;'.$color.'"> 
                                                                                    '.DET_NOM_SEMAINE(DET_INDEX_DAY("$annee-$mois-$day"),"FULL").'
                                                                                </div>
                                                                                <div> 
                                                                                    <div id="box-day-3" style="'.$color.'">'.$day.'</div>
                                                                                    <div style="padding-bottom: 1rem;">'.$SEND.'</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>';

                                                                    echo $return;
                                                                ?>
                                                                </div>
                                                            <?php
                                                        }

                                                        for ($j = 1; $j <= cal_days_in_month(CAL_GREGORIAN, $SET_MONTH, $SET_YEAR); $j++) {
                                                            $dateVerifWeekend = new DateTime("$SET_YEAR-$SET_MONTH-$j");
                                                            $dateVerifWeekend = $dateVerifWeekend->getTimestamp();
                                                            if(intval(date("N", $dateVerifWeekend)) !== 7 && intval(date("N", $dateVerifWeekend)) !== 6){
                                                                $dateVerifWeekend = new DateTime("$SET_YEAR-$SET_MONTH-$j");
                                                                $dateVerifWeekend = $dateVerifWeekend->getTimestamp();
                                                                if(intval(date("N", $dateVerifWeekend)) !== 7 && intval(date("N", $dateVerifWeekend)) !== 6){
                                                                    getInfoPointageDay($SET_YEAR,$SET_MONTH,$j,$agent->id);
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                        <!-- END MONTH -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</body>
</html>