<?php 
    use App\Models\User;
    use App\Models\Pointage;

    $id_conneted = $_SESSION['id'];
    $agent = User::find($id_conneted);

    $filter = $_GET['filter'] ?? null;

    // $month = 1; // mois en cours
    // $year = 2023; // année en cours
    // $weekNumber = 0; // la semaine que vous voulez afficher

    // $date = new DateTime();
    // $date->setDate($year, $month, 1);
    // $date->modify('first day of this month');
    // $firstDayOfWeek = $date->modify("+".($weekNumber - 1)." weeks");

    // for ($i = 0; $i < 7; $i++) {
    //     echo $firstDayOfWeek->modify('+1 day')->format('d')." ";
    // }

    function valid_donnees($donnees){
        $donnees = trim($donnees);
        $donnees = stripslashes($donnees);
        $donnees = htmlspecialchars($donnees);

        return $donnees;
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
        
            default:
                break;
        }
    }

    function getFirstWorkingDay($month, $year) {
        $date = new DateTime("first day of $year-$month");
        while ($date->format('N') > 5) {
            $date->add(new DateInterval('P1D'));
        }
        return $date->format('Y-m-d');
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

    if (isset($_GET['date']) && !empty($_GET['date'])) {
        $date = new DateTime(valid_donnees($_GET['date']));
        $date = $date->getTimestamp();
    }else{
        $date = time();
    }

    $givenDate = date('Y-m-d', $date);

    $dateDebut = new DateTime("$firstDatePointage");
    $dateFin = new DateTime("$lastDatePointage");
    $dateDonne = new DateTime("$givenDate");

    if ($dateDonne > $dateFin) {
        if ($filter === 'year' || $filter === 'month') {
            $date = $dateFin->getTimestamp();
        }
    } elseif ($dateDonne < $dateDebut) {
        $date = $dateDebut->getTimestamp();
    }

    $SET_YEAR = intval(date('Y', $date));
    $SET_MONTH = intval(date('m', $date));
    $SET_DAY = intval(date('d', $date));
    $SET_DAY_INDEX = intval(date("N", $date));

    if ($filter === 'year') {
        $SET_MONTH = 1;
        $SET_DAY = 1;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Accueil</title>
    <link rel="icon" href="{{asset('favicon.ico')}}" />
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.2.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

    <!-- Custom asset -->
    <link href="{{asset('css/app.css')}}" rel="stylesheet">
    @vite('resources/css/app.css')

    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script defer src="{{asset('js/main.js')}}"></script>
</head>
<body class="antialiased">
    <section>

        <div style="
            display: flex;
        ">
            {{-- SLIDER --}}
                <x-comp-slider />
            {{-- FIN SLIDER --}}

            <div class="w-full h-full max-sm:w-screen" style="
                background-image: url('{{asset('img/fond.jpg')}}');
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-size: cover;
            ">
                <div >
                    {{-- HEADER--}}
                    <x-comp-header />
                    {{-- FIN HEADER --}}

                    <div class="w-full min-h-full flex items-center justify-center">
                        {{-- BODY --}}
                        <div class="w-full min-h-full sm:pl-16 sm:pr-16">
                            
                            <div class="w-full min-h-screen bg-white/[0.8]">
                                <div class="w-full p-3">
                                    <div class="w-full flex items-center justify-center">
                                        <div class="w-full h-auto flex sm:flex-row justify-between max-sm:flex-col max-sm:items-center max-sm:justify-center">
                                            <div class="h-auto flex items-center">
                                                <div class="h-12 ml-4 flex">
                                                    <a href="/?<?php if(isset($_GET['date']) && isset($_GET['date'])){ echo "&date=".valid_donnees($_GET['date']); } ?>" class="p-3 mr-1 rounded-lg text-sm h-12 bg-opacity-60 bg-black/[0.162] flex items-center justify-center" style="
                                                    @if ($filter === null || $filter === 'day')
                                                        background: #EC9728C6;
                                                        @endif
                                                    ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="margin-right: 0.5rem;" fill="currentColor" class="bi bi-calendar-day" viewBox="0 0 16 16">
                                                            <path d="M4.684 11.523v-2.3h2.261v-.61H4.684V6.801h2.464v-.61H4v5.332h.684zm3.296 0h.676V8.98c0-.554.227-1.007.953-1.007.125 0 .258.004.329.015v-.613a1.806 1.806 0 0 0-.254-.02c-.582 0-.891.32-1.012.567h-.02v-.504H7.98v4.105zm2.805-5.093c0 .238.192.425.43.425a.428.428 0 1 0 0-.855.426.426 0 0 0-.43.43zm.094 5.093h.672V7.418h-.672v4.105z"/>
                                                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                                        </svg>
                                                        Jour
                                                    </a>
                                                    <a href="/?filter=month<?php if(isset($_GET['date']) && isset($_GET['date'])){ echo "&date=".valid_donnees($_GET['date']); } ?>" class="p-3 mr-1 rounded-lg text-sm h-12 bg-opacity-60 bg-black/[0.162] flex items-center justify-center" style="
                                                        @if ($filter === 'month')
                                                        background: #EC9728C6;
                                                        @endif
                                                    ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="margin-right: 0.5rem;" fill="currentColor" class="bi bi-calendar-month" viewBox="0 0 16 16">
                                                            <path d="M2.56 11.332 3.1 9.73h1.984l.54 1.602h.718L4.444 6h-.696L1.85 11.332h.71zm1.544-4.527L4.9 9.18H3.284l.8-2.375h.02zm5.746.422h-.676V9.77c0 .652-.414 1.023-1.004 1.023-.539 0-.98-.246-.98-1.012V7.227h-.676v2.746c0 .941.606 1.425 1.453 1.425.656 0 1.043-.28 1.188-.605h.027v.539h.668V7.227zm2.258 5.046c-.563 0-.91-.304-.985-.636h-.687c.094.683.625 1.199 1.668 1.199.93 0 1.746-.527 1.746-1.578V7.227h-.649v.578h-.019c-.191-.348-.637-.64-1.195-.64-.965 0-1.64.679-1.64 1.886v.34c0 1.23.683 1.902 1.64 1.902.558 0 1.008-.293 1.172-.648h.02v.605c0 .645-.423 1.023-1.071 1.023zm.008-4.53c.648 0 1.062.527 1.062 1.359v.253c0 .848-.39 1.364-1.062 1.364-.692 0-1.098-.512-1.098-1.364v-.253c0-.868.406-1.36 1.098-1.36z"/>
                                                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                                        </svg>
                                                        Mois
                                                    </a>
                                                    <a href="/?filter=year<?php if(isset($_GET['date']) && isset($_GET['date'])){ echo "&date=".valid_donnees($_GET['date']); } ?>" class="p-3 mr-1 rounded-lg text-sm h-12 bg-opacity-60 bg-black/[0.162] flex items-center justify-center" style="
                                                        @if ($filter === 'year')
                                                        background: #EC9728C6;
                                                        @endif
                                                    ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="margin-right: 0.5rem;" fill="currentColor" class="bi bi-calendar3" viewBox="0 0 16 16">
                                                            <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857V3.857z"/>
                                                            <path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                                        </svg>
                                                        Année
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="max-sm:mt-2 h-auto mr-1 flex">
                                                <a href="<?php 
                                                    $prec_date = "$SET_YEAR-$SET_MONTH-$SET_DAY";

                                                    if ($filter === 'month') {
                                                        $new_moins1 = strftime("%Y-%m-%d", strtotime("$prec_date -1 month"));
                                                        echo "?filter=month&date=$new_moins1";
                                                    }else if ($filter === 'week') {
                                                        $new_moins1 = strftime("%Y-%m-%d", strtotime("$prec_date -1 week"));
                                                        echo "?filter=week&date=$new_moins1";
                                                    }else if ($filter === 'year') {
                                                        $new_moins1 = strftime("%Y-%m-%d", strtotime("$prec_date -1 year"));
                                                        echo "?filter=year&date=$new_moins1";
                                                    }else {
                                                        $new_moins1 = strftime("%Y-%m-%d", strtotime("$prec_date -1 day"));
                                                        echo "?filter=day&date=$new_moins1";
                                                    }
                                                    ?>" id="<?php 
                                                    if ($filter === 'month') {
                                                        echo "btn-back-month";
                                                    }else if ($filter === 'week') {
                                                        echo "btn-back-week";
                                                    }else if ($filter === 'year') {
                                                        echo "btn-back-year";
                                                    }else {
                                                        echo "btn-back-day";
                                                    }
                                                    ?>" class="w-16 mr-1 rounded-lg text-sm h-12 bg-opacity-60 bg-black/[0.162] flex items-center justify-center" style="
                                                    background: #EC9728C6;
                                                ">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-skip-backward-fill" viewBox="0 0 16 16">
                                                        <path d="M.5 3.5A.5.5 0 0 0 0 4v8a.5.5 0 0 0 1 0V8.753l6.267 3.636c.54.313 1.233-.066 1.233-.697v-2.94l6.267 3.636c.54.314 1.233-.065 1.233-.696V4.308c0-.63-.693-1.01-1.233-.696L8.5 7.248v-2.94c0-.63-.692-1.01-1.233-.696L1 7.248V4a.5.5 0 0 0-.5-.5z"/>
                                                    </svg>
                                                </a>
                                                <div id="i_date" class="w-52 mr-1 rounded-lg text-sm h-12 bg-opacity-60 bg-black/[0.162] flex items-center justify-center">
                                                    <?php
                                                        if ($filter === 'month') {
                                                            echo getMoisLetter($SET_MONTH,$SET_YEAR)." $SET_YEAR"; 
                                                        }else if ($filter === 'week') {
                                                            echo "$SET_DAY - ".getMoisLetter($SET_MONTH,$SET_YEAR)." $SET_YEAR"; 
                                                        }else if ($filter === 'year') {
                                                            echo "$SET_YEAR"; 
                                                        }else {
                                                            echo "$SET_DAY - ".getMoisLetter($SET_MONTH,$SET_YEAR)." $SET_YEAR"; 
                                                        }
                                                    ?>
                                                </div>
                                                <a href="<?php 
                                                    $prec_date = "$SET_YEAR-$SET_MONTH-$SET_DAY";

                                                    if ($filter === 'month') {
                                                        $new_plus1 = strftime("%Y-%m-%d", strtotime("$prec_date +1 month"));
                                                        echo "?filter=month&date=$new_plus1";
                                                    }else if ($filter === 'week') {
                                                        $new_plus1 = strftime("%Y-%m-%d", strtotime("$prec_date +1 week"));
                                                        echo "?filter=week&date=$new_plus1";
                                                    }else if ($filter === 'year') {
                                                        $new_plus1 = strftime("%Y-%m-%d", strtotime("$prec_date +1 year"));
                                                        echo "?filter=year&date=$new_plus1";
                                                    }else {
                                                        $new_plus1 = strftime("%Y-%m-%d", strtotime("$prec_date +1 day"));
                                                        echo "?filter=day&date=$new_plus1";
                                                    }
                                                    ?>" id="<?php 
                                                    if ($filter === 'month') {
                                                        echo "btn-next-month";
                                                    }else if ($filter === 'week') {
                                                        echo "btn-next-week";
                                                    }else if ($filter === 'year') {
                                                        echo "btn-next-year";
                                                    }else {
                                                        echo "btn-next-day";
                                                    }
                                                    ?>" class="w-16 mr-1 rounded-lg text-sm h-12 bg-opacity-60 bg-black/[0.162] flex items-center justify-center" style="
                                                    background: #EC9728C6;
                                                ">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-skip-forward-fill" viewBox="0 0 16 16">
                                                        <path d="M15.5 3.5a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V8.753l-6.267 3.636c-.54.313-1.233-.066-1.233-.697v-2.94l-6.267 3.636C.693 12.703 0 12.324 0 11.693V4.308c0-.63.693-1.01 1.233-.696L7.5 7.248v-2.94c0-.63.693-1.01 1.233-.696L15 7.248V4a.5.5 0 0 1 .5-.5z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-full h-full max-sm:mt-6">
                                        <div class="w-full h-full">
                                            <?php
                                                if ($filter === 'day' || $filter === null) {
                                                    ?>
                                                        <!-- DAY -->
                                                        <div class="w-full h-full p-4">
                                                            <div class="flex items-center justify-center px-2">
                                                                <div id="semaine" class="w-full font-bold">{{DET_NOM_SEMAINE($SET_DAY_INDEX,"FULL")}}</div>
                                                            </div>
                                                            <div id="day-list" class="font-bold cursor-pointer">
                                                                <!-- JOUR -->
                                                                <?php
                                                                    function getInfoPointageDay($annee,$mois,$jours,$id){
                                                                        $BACKGROUND_NORMAL = 'rgba(174, 174, 174, 0.771)';
                                                                        $BACKGROUND_CURRENT = '#317AC1';
                                                                        $BACKGROUND_SUCCESS = 'rgba(0, 128, 0, 0.771)';
                                                                        $BACKGROUND_DANGER = 'rgba(255, 0, 0, 0.771)';
                                                                        $BACKGROUND_JUSTIFIER = 'rgba(255, 0, 0, 0.571)';
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
                                                                            <div id="$idDay" class="w-full h-10 cursor-pointer">
                                                                            <?php 
                                                                                if ($year === $current_year && $month === $current_month && $day === $current_day) {
                                                                                    $BACKGROUND = $BACKGROUND_CURRENT;
                                                                                    $SEND = '<div class="w-full h-full flex items-center justify-center text-sm text-black/[0.622]">Journée en cours</div>';              
                                                                                    $onclick = "";
                                                                                    $oncontext = '';
                                                                                }else{
                                                                                    if ($reqPointage->exists()) {
                                                                                        $recupInfoPointage = $reqPointage->get()[0];

                                                                                        if ($recupInfoPointage->heure_arrivee !== null && $recupInfoPointage->heure_depart !== null) {
                                                                                            $BACKGROUND = $BACKGROUND_SUCCESS;
                                                                                            $SEND = '<div title="Heure total(s)'.str_replace(":", 'h', $recupInfoPointage->total_heure).'m" class="w-full h-full flex items-center justify-center text-xs text-white/[0.622]">'.$recupInfoPointage->heure_arrivee.' / '.$recupInfoPointage->heure_depart.'</div><div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(255, 255, 255, 0.622);font-size: 0.6rem;">H.T. '.str_replace(":", 'h', $recupInfoPointage->total_heure).'m</div>';
                                                                                            $onclick = "";
                                                                                            $oncontext = '';
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
                                                                                                $SEND = '<div class="w-full h-full flex items-center justify-center text-sm text-white/[0.622]" title="'.$recupInfoPointage->motif.'">'.$motif.'</div>';
                                                                                                
                                                                                                if($verifAdmins->exists()){
                                                                                                    if ($verifAdmins->admins) {
                                                                                                        $onclick = "";
                                                                                                        $oncontext = $oncontext = 'oncontextmenu="choix(this, '.$year.', '.$month.', '.$day.', '.$id.')"';
                                                                                                    }else{
                                                                                                        $onclick = "";
                                                                                                        $oncontext = '';
                                                                                                    }
                                                                                                }
                                                                                            }else{
                                                                                                $SEND = '<div class="w-full h-full flex items-center justify-center text-sm text-black/[0.622]">Non Justifié</div>';
                                                                                                
                                                                                                if($verifAdmins->exists()){
                                                                                                    if ($verifAdmins->admins) {
                                                                                                        $onclick = "";
                                                                                                        $oncontext = $oncontext = 'oncontextmenu="choix(this, '.$year.', '.$month.', '.$day.', '.$id.')"';
                                                                                                    }else{
                                                                                                        $onclick = "";
                                                                                                        $oncontext = '';
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }else{
                                                                                        $BACKGROUND = $BACKGROUND_NORMAL;
                                                                                        $SEND = '';
                                                                                        $onclick = "";
                                                                                        $oncontext = '';
                                                                                    }
                                                                                }

                                                                                if (intval($month) === 1 && intval($day) === 1){
                                                                                    $text = "Jour de l'An";
                                                                                    $SEND = '<div class="w-full h-full flex items-center justify-center text-sm text-gray-500" style="
                                                                                    title="'.$text.'">
                                                                                        '.$text.'
                                                                                    </div>';
                                                                                    $BACKGROUND = $BACKGROUND_FERIE;
                                                                                    $onclick = "";
                                                                                    $color = "color: gray";
                                                                                } else if (intval($month) === 5 && intval($day) === 1) {
                                                                                    $text = "Fête du Travail";
                                                                                    $SEND = '<div class="w-full h-full flex items-center justify-center text-sm text-gray-500" style="
                                                                                    title="'.$text.'">
                                                                                        '.$text.'
                                                                                    </div>';
                                                                                    $BACKGROUND = $BACKGROUND_FERIE;
                                                                                    $onclick = "";
                                                                                    $color = "color: gray";
                                                                                } else if (intval($month) === 12 && intval($day) === 25) {
                                                                                    $text = "Noël";
                                                                                    $SEND = '<div class="w-full h-full flex items-center justify-center text-sm text-gray-500" style="
                                                                                    title="'.$text.'">
                                                                                        '.$text.'
                                                                                    </div>';
                                                                                    $BACKGROUND = $BACKGROUND_FERIE;
                                                                                    $onclick = "";
                                                                                    $color = "color: gray";
                                                                                } else if (intval($month) === 12 && intval($day) === 26) {
                                                                                    $text = "Lendemain de Noël";
                                                                                    $SEND = '<div class="w-full h-full flex items-center justify-center text-sm text-gray-500" style="
                                                                                    title="'.$text.'">
                                                                                        '.$text.'
                                                                                    </div>';
                                                                                    $BACKGROUND = $BACKGROUND_FERIE;
                                                                                    $onclick = "";
                                                                                    $color = "color: gray";
                                                                                }else{
                                                                                    $color = "";
                                                                                }

                                                                                $return = '<div class="day" onclick="'.$onclick.'" '.$oncontext.' style="
                                                                                    width: 100%;
                                                                                    height: 10vh;
                                                                                    font-weight: bold;
                                                                                    border: solid 1px rgba(255, 255, 255, 0.4);
                                                                                    background: '.$BACKGROUND.';
                                                                                    overflow: hidden;
                                                                                ">
                                                                                    <div style="padding: 0.5rem 0 0 0.5rem;">
                                                                                        <div id="box-day-3" style="'.$color.'">'.$day.'</div>
                                                                                        '.$SEND.'
                                                                                    </div>
                                                                                </div>';

                                                                                echo $return;
                                                                            ?>
                                                                            </div>
                                                                        <?php
                                                                    }

                                                                    $dateVerifWeekend = new DateTime("$SET_YEAR-$SET_MONTH-$SET_DAY");
                                                                    $dateVerifWeekend = $dateVerifWeekend->getTimestamp();
                                                                    if(intval(date("N", $dateVerifWeekend)) !== 7 && intval(date("N", $dateVerifWeekend)) !== 6){
                                                                        getInfoPointageDay($SET_YEAR,$SET_MONTH,$SET_DAY,$agent->id);
                                                                    }else{
                                                                        echo '<div class="day z-0" style="
                                                                                    width: 100%;
                                                                                    height: 10vh;
                                                                                    font-weight: bold;
                                                                                    border: solid 1px rgba(255, 255, 255, 0.4);
                                                                                    background: rgba(174, 174, 174, 0.771);
                                                                                    overflow: hidden;
                                                                                ">
                                                                                    <div style="padding: 0.5rem 0 0 0.5rem;">
                                                                                        <div id="box-day-3">'.$SET_DAY.'</div>
                                                                                        Week-end
                                                                                    </div>
                                                                                </div>';
                                                                    }
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <!-- END DAY -->
                                                    <?php
                                                }else if ($filter === 'month'){
                                                    ?>
                                                    <!-- MONTH -->
                                                    <div style="
                                                        width: 100%;
                                                        height: 100%;
                                                    ">
                                                        <div style="
                                                            width: 100%;
                                                            height: 8%;
                                                            display: grid;
                                                            grid-template-columns:repeat(5, 1fr);
                                                            font-weight: bold;
                                                        ">
                                                            <div id="semaine-1" style="width: 100%;display: flex;align-items: center;justify-content: center;
                                                            <?php 
                                                                if($SET_MONTH === intval(date('m', $now)) && $SET_YEAR === intval(date('Y', $now))){
                                                                    if (intval(date("N", $now)) === 1) {
                                                                        echo "border-bottom: solid 6px #317AC1";
                                                                    }else{
                                                                        echo "border-bottom: solid 0 #00000000";
                                                                    }
                                                                }else{
                                                                    echo "border-bottom: solid 0 #00000000";
                                                                }
                                                            ?>
                                                            ">Lun</div>
                                                            <div id="semaine-2" style="width: 100%;display: flex;align-items: center;justify-content: center;
                                                            <?php 
                                                                if($SET_MONTH === intval(date('m', $now)) && $SET_YEAR === intval(date('Y', $now))){
                                                                    if (intval(date("N", $now)) === 2) {
                                                                        echo "border-bottom: solid 6px #317AC1";
                                                                    }else{
                                                                        echo "border-bottom: solid 0 #00000000";
                                                                    }
                                                                }else{
                                                                    echo "border-bottom: solid 0 #00000000";
                                                                }
                                                            ?>
                                                            ">Mar</div>
                                                            <div id="semaine-3" style="width: 100%;display: flex;align-items: center;justify-content: center;
                                                            <?php 
                                                                if($SET_MONTH === intval(date('m', $now)) && $SET_YEAR === intval(date('Y', $now))){
                                                                    if (intval(date("N", $now)) === 3) {
                                                                        echo "border-bottom: solid 6px #317AC1";
                                                                    }else{
                                                                        echo "border-bottom: solid 0 #00000000";
                                                                    }
                                                                }else{
                                                                    echo "border-bottom: solid 0 #00000000";
                                                                }
                                                            ?>
                                                            ">Mer</div>
                                                            <div id="semaine-4" style="width: 100%;display: flex;align-items: center;justify-content: center;
                                                            <?php 
                                                                if($SET_MONTH === intval(date('m', $now)) && $SET_YEAR === intval(date('Y', $now))){
                                                                    if (intval(date("N", $now)) === 4) {
                                                                        echo "border-bottom: solid 6px #317AC1";
                                                                    }else{
                                                                        echo "border-bottom: solid 0 #00000000";
                                                                    }
                                                                }else{
                                                                    echo "border-bottom: solid 0 #00000000";
                                                                }
                                                            ?>
                                                            ">Jeu</div>
                                                            <div id="semaine-5" style="width: 100%;display: flex;align-items: center;justify-content: center;
                                                            <?php 
                                                                if($SET_MONTH === intval(date('m', $now)) && $SET_YEAR === intval(date('Y', $now))){
                                                                    if (intval(date("N", $now)) === 5) {
                                                                        echo "border-bottom: solid 6px #317AC1";
                                                                    }else{
                                                                        echo "border-bottom: solid 0 #00000000";
                                                                    }
                                                                }else{
                                                                    echo "border-bottom: solid 0 #00000000";
                                                                }
                                                            ?>
                                                            ">Ven</div>
                                                        </div>
                                                        <div id="day-list" style="
                                                            width: 100%;
                                                            height: 92%;
                                                            display: grid;
                                                            grid-template-columns:repeat(5, 1fr);
                                                        ">
                                                            <!-- MOIS -->
                                                            <?php
                                                                function getInfoPointageDay($annee,$mois,$jours,$id){
                                                                    $BACKGROUND_NORMAL = 'rgba(174, 174, 174, 0.771)';
                                                                    $BACKGROUND_CURRENT = '#317AC1';
                                                                    $BACKGROUND_SUCCESS = 'rgba(0, 128, 0, 0.771)';
                                                                    $BACKGROUND_DANGER = 'rgba(255, 0, 0, 0.771)';
                                                                    $BACKGROUND_JUSTIFIER = 'rgba(255, 0, 0, 0.571)';
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
                                                                        <div id="$idDay" style="width: 100%;height: 10%;cursor: pointer;">
                                                                        <?php 
                                                                            if ($year === $current_year && $month === $current_month && $day === $current_day) {
                                                                                $BACKGROUND = $BACKGROUND_CURRENT;
                                                                                $SEND = '<div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(0, 0, 0, 0.622);font-size: 0.8rem;">Journée en cours</div>';              
                                                                                $onclick = "window.location.href = `?filter=day&date=$year-$month-$day`";
                                                                                $oncontext = '';
                                                                            }else{
                                                                                if ($reqPointage->exists()) {
                                                                                    $recupInfoPointage = $reqPointage->get()[0];

                                                                                    if ($recupInfoPointage->heure_arrivee !== null && $recupInfoPointage->heure_depart !== null) {
                                                                                        $BACKGROUND = $BACKGROUND_SUCCESS;
                                                                                        $SEND = '<div title="Heure total(s)'.str_replace(":", 'h', $recupInfoPointage->total_heure).'m" style="width: 100%; height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(255, 255, 255, 0.622);font-size: 0.6rem;">'.$recupInfoPointage->heure_arrivee.' / '.$recupInfoPointage->heure_depart.'</div><div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(255, 255, 255, 0.622);font-size: 0.6rem;">H.T. '.str_replace(":", 'h', $recupInfoPointage->total_heure).'m</div>';
                                                                                        $onclick = "window.location.href = `?filter=day&date=$year-$month-$day`";
                                                                                        $oncontext = '';
                                                                                    }else{
                                                                                        $BACKGROUND = $BACKGROUND_DANGER;
                                                                                        $verifAdmins = User::find(intval($_SESSION['id']));

                                                                                        if (strlen($recupInfoPointage->motif) >= 10) {
                                                                                            $motif = substr("$recupInfoPointage->motif", 0, 10).'...';
                                                                                        }else{
                                                                                            $motif = $recupInfoPointage->motif;
                                                                                        }

                                                                                        if ($recupInfoPointage->motif !== null) {
                                                                                            $BACKGROUND = $BACKGROUND_JUSTIFIER;
                                                                                            $SEND = '<div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(255, 255, 255, 0.622);font-size: 0.8rem;" title="'.$recupInfoPointage->motif.'">'.$motif.'</div>';
                                                                                            
                                                                                            if($verifAdmins->exists()){
                                                                                                if ($verifAdmins->admins) {
                                                                                                    $onclick = "window.location.href = `?filter=day&date=$year-$month-$day`";
                                                                                                    $oncontext = 'oncontextmenu="choix(this, '.$year.', '.$month.', '.$day.', '.$id.')"';
                                                                                                }else{
                                                                                                    $onclick = "window.location.href = `?filter=day&date=$year-$month-$day`";
                                                                                                    $oncontext = '';
                                                                                                }
                                                                                            }
                                                                                        }else{
                                                                                            $SEND = '<div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: #000000C0;font-size: 0.8rem;">Non Justifié</div>';
                                                                                            
                                                                                            if($verifAdmins->exists()){
                                                                                                if ($verifAdmins->admins) {
                                                                                                    $onclick = "window.location.href = `?filter=day&date=$year-$month-$day`";
                                                                                                    $oncontext = 'oncontextmenu="choix(this, '.$year.', '.$month.', '.$day.', '.$id.')"';
                                                                                                }else{
                                                                                                    $onclick = "window.location.href = `?filter=day&date=$year-$month-$day`";
                                                                                                    $oncontext = '';
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }else{
                                                                                    $BACKGROUND = $BACKGROUND_NORMAL;
                                                                                    $SEND = '';
                                                                                    $onclick = "window.location.href = `?filter=day&date=$year-$month-$day`";
                                                                                    $oncontext = '';
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
                                                                                $onclick = "window.location.href = `?filter=day&date=$year-$month-$day`";
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
                                                                                $onclick = "window.location.href = `?filter=day&date=$year-$month-$day`";
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
                                                                                $onclick = "window.location.href = `?filter=day&date=$year-$month-$day`";
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
                                                                                $onclick = "window.location.href = `?filter=day&date=$year-$month-$day`";
                                                                                $color = "color: gray";
                                                                            }else{
                                                                                $color = "";
                                                                            }

                                                                            $return = '<div class="day" onclick="'.$onclick.'" '.$oncontext.' style="
                                                                                width: 100%;
                                                                                height: 10vh;
                                                                                font-weight: bold;
                                                                                border: solid 1px rgba(255, 255, 255, 0.4);
                                                                                background: '.$BACKGROUND.';
                                                                                overflow: hidden;
                                                                            ">
                                                                                <div style="padding: 0.5rem 0 0 0.5rem;">
                                                                                    <div id="box-day-3" style="'.$color.'">'.$day.'</div>
                                                                                    '.$SEND.'
                                                                                </div>
                                                                            </div>';

                                                                            echo $return;
                                                                        ?>
                                                                        </div>
                                                                    <?php
                                                                }

                                                                for ($i = 1; $i <= 7; $i++){
                                                                    $getDebutMois = new DateTime(getFirstWorkingDay($SET_MONTH,$SET_YEAR));
                                                                    $getDebutMois = $getDebutMois->getTimestamp();
                                                                    $datestart = intval(date('N', $getDebutMois));
                                                                    if ($i===$datestart) {
                                                                        for ($j = 1; $j <= cal_days_in_month(CAL_GREGORIAN, $SET_MONTH, $SET_YEAR); $j++) {
                                                                            $dateVerifWeekend = new DateTime("$SET_YEAR-$SET_MONTH-$j");
                                                                            $dateVerifWeekend = $dateVerifWeekend->getTimestamp();
                                                                            if(intval(date("N", $dateVerifWeekend)) !== 7 && intval(date("N", $dateVerifWeekend)) !== 6){
                                                                                getInfoPointageDay($SET_YEAR,$SET_MONTH,$j,$agent->id);
                                                                            }
                                                                        }
                                                                    }else{
                                                                        echo  '<div style="width: 100%;height: 10%;"></div>';
                                                                    }
                                                                }
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <!-- END MONTH -->
                                                    <?php
                                                }else if ($filter === 'year'){
                                                    ?>
                                                    <!-- YEAR -->
                                                    <div class="w-full h-full pt-12">
                                                        <div id="calendar" class="w-full h-full flex flex-col items-center justify-center">
                                                            <div class="w-full h-full flex flex-col items-center justify-center">
                                                                <div class="grid md:grid-cols-2 lg:grid-cols-3">
                                                                    <?php 
                                                                        function getInfoPointage($annee,$mois,$jours,$id){
                                                                            $BACKGROUND_NORMAL = 'rgba(174, 174, 174, 0.771)';
                                                                            $BACKGROUND_CURRENT = '#317AC1';
                                                                            $BACKGROUND_SUCCESS = 'rgba(0, 128, 0, 0.771)';
                                                                            $BACKGROUND_DANGER = 'rgba(255, 0, 0, 0.771)';
                                                                            $BACKGROUND_JUSTIFIER = 'rgba(255, 0, 0, 0.571)';
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
                                                                                <div id="{{$idDay}}" style="width: 100%;height: 10%;cursor: pointer;">
                                                                                <?php 
                                                                                    if ($year === $current_year && $month === $current_month && $day === $current_day) {
                                                                                        $BACKGROUND = $BACKGROUND_CURRENT;
                                                                                        $SEND = '<div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(0, 0, 0, 0.622);font-size: 0.8rem;">Journée en cours</div>';              
                                                                                        $onclick = "window.location.href = `?filter=month&date=$year-$month-01`";
                                                                                        $oncontext = '';
                                                                                    }else{
                                                                                        if ($reqPointage->exists()) {
                                                                                            $recupInfoPointage = $reqPointage->get()[0];

                                                                                            if ($recupInfoPointage->heure_arrivee !== null && $recupInfoPointage->heure_depart !== null) {
                                                                                                $BACKGROUND = $BACKGROUND_SUCCESS;
                                                                                                $SEND = '<div title="Heure total(s)'.str_replace(":", 'h', $recupInfoPointage->total_heure).'m" style="width: 100%; height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(255, 255, 255, 0.622);font-size: 0.6rem;">'.$recupInfoPointage->heure_arrivee.' / '.$recupInfoPointage->heure_depart.'</div><div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(255, 255, 255, 0.622);font-size: 0.6rem;">H.T. '.str_replace(":", 'h', $recupInfoPointage->total_heure).'m</div>';
                                                                                                $onclick = "window.location.href = `?filter=month&date=$year-$month-01`";
                                                                                                $oncontext = '';
                                                                                            }else{
                                                                                                $BACKGROUND = $BACKGROUND_DANGER;
                                                                                                $verifAdmins = User::find(intval($_SESSION['id']));

                                                                                                if (strlen($recupInfoPointage->motif) >= 10) {
                                                                                                    $motif = "";
                                                                                                }else{
                                                                                                    $motif = $recupInfoPointage->motif;
                                                                                                }

                                                                                                if ($recupInfoPointage->motif !== null) {
                                                                                                    $BACKGROUND = $BACKGROUND_JUSTIFIER;
                                                                                                    $SEND = '<div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: rgba(255, 255, 255, 0.622);font-size: 0.8rem;" title="'.$recupInfoPointage->motif.'">'.$motif.'</div>';
                                                                                                    
                                                                                                    if($verifAdmins->exists()){
                                                                                                        if ($verifAdmins->admins) {
                                                                                                            $onclick = "window.location.href = `?filter=month&date=$year-$month-01`";
                                                                                                            $oncontext = 'oncontextmenu="choix(this, '.$year.', '.$month.', '.$day.', '.$id.')"';
                                                                                                        }else{
                                                                                                            $onclick = "window.location.href = `?filter=month&date=$year-$month-01`";
                                                                                                            $oncontext = '';
                                                                                                        }
                                                                                                    }
                                                                                                }else{
                                                                                                    $SEND = '<div style="width: 100%;height: 100%;display: flex;align-items: center;justify-content:center;color: #000000C0;font-size: 0.8rem;">Non Justifié</div>';
                                                                                                    
                                                                                                    if($verifAdmins->exists()){
                                                                                                        if ($verifAdmins->admins) {
                                                                                                            $onclick = "window.location.href = `?filter=month&date=$year-$month-01`";
                                                                                                            $oncontext = 'oncontextmenu="choix(this, '.$year.', '.$month.', '.$day.', '.$id.')"';
                                                                                                        }else{
                                                                                                            $onclick = "window.location.href = `?filter=month&date=$year-$month-01`";
                                                                                                            $oncontext = '';
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }else{
                                                                                            $BACKGROUND = $BACKGROUND_NORMAL;
                                                                                            $SEND = '';
                                                                                            $onclick = "window.location.href = `?filter=month&date=$year-$month-01`";
                                                                                            $oncontext = '';
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
                                                                                        $onclick = "window.location.href = `?filter=month&date=$year-$month-01`";
                                                                                        $color = "color: gray";
                                                                                    }else if (intval($month) === 5 && intval($day) === 1) {
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
                                                                                        $onclick = "window.location.href = `?filter=month&date=$year-$month-01`";
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
                                                                                        $onclick = "window.location.href = `?filter=month&date=$year-$month-01`";
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
                                                                                        $onclick = "window.location.href = `?filter=month&date=$year-$month-01`";
                                                                                        $color = "color: gray";
                                                                                    }else{
                                                                                        $color = "";
                                                                                    }

                                                                                    $return = '<div class="day" onclick="'.$onclick.'" '.$oncontext.' style="
                                                                                        width: 100%;
                                                                                        height: 2rem;
                                                                                        font-weight: bold;
                                                                                        border: solid 1px rgba(255, 255, 255, 0.4);
                                                                                        background: '.$BACKGROUND.';
                                                                                        overflow: hidden;
                                                                                    ">
                                                                                        <div style="padding: 0.5rem 0 0 0.5rem;">
                                                                                            <div id="box-day-3" style="'.$color.'">'.$day.'</div>
                                                                                        </div>
                                                                                    </div>';

                                                                                    echo $return;
                                                                                ?>
                                                                                </div>
                                                                            <?php
                                                                        }
                                                                        for ($x=1; $x <= 12; $x++) { 
                                                                            ?>
                                                                            <div class="w-72 max-sm:w-72 min-height-full" style="border-top: solid 1px rgba(0, 0, 0, 0.6);cursor: pointer;">
                                                                                <div style="width: 100%;min-height: 100%;padding: 0.5rem;">
                                                                                    <div style="width: 100%;height: 10%;font-weight: bold;padding-bottom: 2rem;">{{getMoisLetter($x,$SET_YEAR)}}</div>
                                                                                    <div style="width: 100%;height: 90%;font-weight: bold;">
                                                                                        <div style="width: 100%;height: 14%;font-size: 0.8rem;display: grid;grid-template-columns:repeat(5, 1fr)">
                                                                                            <div id="semaine-1" style="width: 100%;display:flex;align-items:center;">L</div>
                                                                                            <div id="semaine-2" style="width: 100%;display:flex;align-items:center;">M</div>
                                                                                            <div id="semaine-3" style="width: 100%;display:flex;align-items:center;">M</div>
                                                                                            <div id="semaine-4" style="width: 100%;display:flex;align-items:center;">J</div>
                                                                                            <div id="semaine-5" style="width: 100%;display:flex;align-items:center;">V</div>
                                                                                        </div>
                                                                                        <div style="width: 100%;height: 100%;display: grid;grid-template-columns:repeat(5, 1fr)">
                                                                                            <!-- {{-- LIST DAY --}} -->
                                                                                            <?php
                                                                                                for ($i = 1; $i <= 7; $i++){
                                                                                                    $getDebutMois = new DateTime(getFirstWorkingDay($x,$SET_YEAR));
                                                                                                    $getDebutMois = $getDebutMois->getTimestamp();
                                                                                                    $datestart = intval(date('N', $getDebutMois));
                                                                                                    if ($i===$datestart) {
                                                                                                        for ($j = 1; $j <= cal_days_in_month(CAL_GREGORIAN, $x, $SET_YEAR); $j++) {
                                                                                                            $dateVerifWeekend = new DateTime("$SET_YEAR-$x-$j");
                                                                                                            $dateVerifWeekend = $dateVerifWeekend->getTimestamp();
                                                                                                            if(intval(date("N", $dateVerifWeekend)) !== 7 && intval(date("N", $dateVerifWeekend)) !== 6){
                                                                                                                getInfoPointage($SET_YEAR,$x,$j,$agent->id);
                                                                                                            }
                                                                                                        }
                                                                                                    }else{
                                                                                                        echo  '<div style="width: 100%;height: 10%;"></div>';
                                                                                                    }
                                                                                                }
                                                                                            ?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <?php
                                                                        }
                                                                    ?>
                                                                </div>

                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- END YEAR -->
                                                    <?php
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{-- END BODY --}}

                        {{-- SLIDER RIGHT --}}
                        <?php
                            $retAdmins = User::find(intval($_SESSION['id']));
                            $retAdmins = $retAdmins->admins;

                            if ($retAdmins) {
                            ?>
                                <x-comp-slider-right />
                            <?php
                            }else {
                                ?>
                                    <div class="w-16 h-screen fixed top-0 z-10 right-0 bg-opacity-40 bg-white/[1] shadow-md max-sm:hidden" style="
                                        background-image: linear-gradient(#317AC1, #497B96);
                                        box-shadow: 0 4px 2px 3px rgba(0, 0, 0, 0.26);
                                    ">&nbsp;</div>
                                <?php
                            }
                        ?>
                        {{-- FIN SLIDER RIGHT --}}
                    </div>
                </div>
            </div>
        </div>

    </section>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>