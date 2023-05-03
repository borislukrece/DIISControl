<?php 
    use App\Models\User;
    use App\Models\Service;
    use App\Models\Fonction;
    use App\Models\Direction;
    use App\Models\Sous_direction;

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

    if (isset($_GET['search'])) {
        $search = trim(strtolower(valid_donnees($_GET['search'])));

        if (isset($_GET['filter']) && !empty($_GET['filter']) && isset($_GET['search']) && !empty($_GET['search'])) {
            $searchTerm = strtoupper($_GET['search']);

            if (strtolower($_GET['filter']) === 'service') {
                $agents = DB::table('users')
                    ->join('services', 'users.id_service', '=', 'services.id')
                    ->select('users.*')
                    ->where('services.nom', '=', $searchTerm)
                    ->get()
                    ->sortBy('nom');
            }else{
                $agents = DB::table('users')
                    ->select('users.*')
                    ->where(function($query) use ($searchTerm) {
                    $query->where('users.nom', 'like', '%'.$searchTerm.'%')
                        ->orWhere('users.prenom', 'like', '%'.$searchTerm.'%');
                    })
                    ->get()
                    ->sortBy('nom');
            }
        }else{
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                // dd(trim(breakPhrase(valid_donnees($_GET['search']))));
                $agents = DB::table('users')
                ->select('users.*')
                ->where(DB::raw("CONCAT(users.nom, ' ', users.prenom)"), '=', trim(breakPhrase(valid_donnees($_GET['search']))))
                ->orWhere(function($query) {
                    $searchTerm = trim(breakPhrase(valid_donnees($_GET['search'])));
                    $query->where('users.nom', '=', $searchTerm)
                          ->orWhere('users.prenom', '=', $searchTerm);
                })
                ->get()
                ->sortBy('nom');
            }else{
                $agents = User::all()->sortBy('nom');
            }
        }
    }else{
        $agents = User::all()->sortBy('nom');
    }

    if (!isset($agents)) {
        $agents = User::all()->sortBy('nom');
    }

    $countAgents = count($agents);

    $service = Service::all()->sortBy('id');
    $fonction = Fonction::all()->sortBy('id'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Liste des agents</title>
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
                                    <div>
                                        <div class="w-full h-14 bg-white flex items-center justify-center" style="
                                            border-bottom: solid 2px #EC9628;
                                        ">
                                            <div class="w-full h-full flex flex-col justify-center font-bold">
                                                <form method="GET" class="form-row flex items-center justify-between">
                                                    <div class="flex items-center justify-center pl-4">
                                                        <div class="flex items-center justify-center mr-4">
                                                            <a href="?<?php 
                                                                if (isset($_GET['search'])) {
                                                                    echo "search=".valid_donnees($_GET['search'])."&";
                                                                }
                                                            ?>filter=<?php 
                                                                if (isset($_GET['filter']) && strtolower($_GET['filter']) === 'name') {
                                                                    echo "service";
                                                                }else{
                                                                    echo "name";
                                                                }
                                                            ?>" class="flex items-center justify-center cursor-pointer" style="color: #EC9628">
                                                                <i class="bi bi-filter-circle-fill text-2xl flex items-center justify-center"></i>
                                                            </a>
                                                        </div>

                                                        <div class="w-64 sm:w-72">
                                                            <input type="text" class="form-control max-sm:border-none" id="search" name="search" placeholder="<?php 
                                                            if (isset($_GET['filter']) && strtolower($_GET['filter']) === 'name') {
                                                                echo "Recherche par service (acronyme)";
                                                            }else{
                                                                echo "Recherche par nom";
                                                            }
                                                        ?>" value="<?php 
                                                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                                                echo valid_donnees(trim($_GET['search']));
                                                            }
                                                        ?>">
                                                        </div>

                                                        <input type="hidden" name="filter" value="<?php 
                                                            if (isset($_GET['filter']) && !empty($_GET['filter'])) {
                                                                echo valid_donnees(strtolower($_GET['filter']));
                                                            }
                                                        ?>" />
                                                    </div>

                                                    <div class="flex justify-center pr-4">
                                                        <button type="submit" id="search-btn" class="btn h-8 rounded-md p-2 inline-flex items-center justify-center bg-blue-500 text-white hover:text-gray-500 hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-100" style="background: #EC9628">
                                                            <span  class="max-sm:hidden">Rechercher</span>
                                                            <i class="bi bi-search flex items-center justify-center sm:hidden"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="h-10"></div>

                                        <div class="w-full h-full">
                                            <div class="w-full h-full" style="
                                                border-top: solid 1px #EC9628;
                                            ">
                                                <div class="w-full h-14 bg-white pl-4 text-base font-bold flex items-center" style="
                                                    border-bottom: solid 3px gray;
                                                ">
                                                    Total(s)&nbsp;<span style="color: #EC9628;"> {{$countAgents}}</span>
                                                </div>

                                                <div class="w-full h-full">
                                                    <div class="w-full h-full flex">
                                                        <div class="w-full h-full">
                                                            <div class="w-full h-full bg-white p-6 overflow-x-scroll">
                                                                <div id="list-search">
                                                                    <table class="table table-striped">
                                                                        <thead class="thead-dark">
                                                                            <tr>
                                                                              <th scope="col">Image de profil</th>
                                                                              <th scope="col">Nom</th>
                                                                              <th scope="col">Prenom</th>
                                                                              <th scope="col">Sexe</th>
                                                                              <th scope="col">Date de naissance</th>
                                                                              <th scope="col">Lieu de naissance</th>
                                                                              <th scope="col">Fonction</th>
                                                                              <th scope="col">Service</th>
                                                                              <th scope="col">Sous Direction</th>
                                                                              <th scope="col">Direction</th>
                                                                              <th scope="col">Telephone</th>
                                                                              <th scope="col">Adresse</th>
                                                                              <th scope="col">Nom d'utilisateur</th>
                                                                              <th scope="col">Administrateur</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($agents as $item)
                                                                            <?php
                                                                                $path_img = $item?->profil_img;
                                                                            ?>
                                                                            <tr data-toggle="modal" data-whatever="{{ $item->id }}" data-target="#modalUpdate" class="cursor-pointer">
                                                                                <th>
                                                                                    <span data-toggle="tooltip" title="Profil" class="w-16 h-16 bg-none text-gray-500 p-1 flex justify-center items-center rounded-full cursor-pointer shadow overflow-hidden">
                                                                                        @if ($path_img)
                                                                                            <img src="../profil_img/<?php echo $path_img; ?>" alt="Profil" class="w-full h-auto" data-toggle="modal" data-target="#modalProfil">
                                                                                        @else
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                                                                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                                                                            </svg>
                                                                                        @endif
                                                                                    </span>
                                                                                </th>
                                                                                <th scope="row"><?php echo $item->nom; ?></th>
                                                                                <td><?php echo $item->prenom; ?></td>
                                                                                <td><?php echo (!$item->sexe) ? "M" : "F"; ?></td>
                                                                                <td><?php echo $item->date_naissance; ?></td>
                                                                                <td><?php echo $item->lieu_naissance; ?></td>
                                                                                <td><?php
                                                                                    $searchFonction = Fonction::find($item->id_fonction);
                                                                                    echo $searchFonction->nom;
                                                                                ?></td>
                                                                                <td><?php
                                                                                    $searchService = Service::find($item->id_service);
                                                                                    echo $searchService->libelle;
                                                                                ?></td>
                                                                                <td><?php
                                                                                    $searchServiceSousDirection = Service::find($item->id_service);
                                                                                    if (intval($searchServiceSousDirection->id_sous_direction) === 0) {
                                                                                        echo '<span style="color: gray;">(Aucune sous direction)</span> Secrétariat';
                                                                                    }else{
                                                                                        $searchSous_direction = Sous_direction::find($searchServiceSousDirection->id_sous_direction);
                                                                                        echo $searchSous_direction->libelle;
                                                                                    }
                                                                                ?></td>
                                                                                <td><?php
                                                                                    $searchServiceSousDirectionDirection = Service::find($item->id_service);
                                                                                    if (intval($searchServiceSousDirection->id_sous_direction) === 0) {
                                                                                        $searchDirection = Direction::find(1);
                                                                                        echo $searchDirection->libelle;
                                                                                    }else{
                                                                                        $searchSous_directionDirection = Sous_direction::find($searchServiceSousDirection->id_sous_direction);
                                                                                        $searchDirection = Direction::find($searchSous_directionDirection->id_direction);
                                                                                        echo $searchDirection->libelle;
                                                                                    }
                                                                                ?></td>
                                                                                <td><?php echo $item->tel; ?></td>
                                                                                <td><?php echo $item->adresse; ?></td>
                                                                                <td><?php echo $item->username; ?></td>
                                                                                <td><?php 
                                                                                    if ($item->admins) {
                                                                                      echo 'Oui';
                                                                                    } else {
                                                                                      echo 'Non';
                                                                                    }
                                                                                    ?></td>
                                                                            </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
                                    <div class="w-5 h-full bg-white bg-opacity-40 shadow-md">&nbsp;</div>
                                <?php
                            }
                        ?>
                        {{-- FIN SLIDER RIGHT --}}
                    </div>
                </div>
            </div>
        </div>

    </section>

    @if (\Session::has('success'))
        <div class="fixed bottom-2 sm:right-24 max-sm:w-full px-6 z-50">
            <div class="alert alert-success flex items-center justify-center pl-4" id="message">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-check2-circle ml-5 mr-5" viewBox="0 0 16 16">
                    <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
                    <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
                </svg>
                <span>{{ \Session::get('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="fixed bottom-2 sm:right-24 max-sm:w-full px-6 z-50">
            <div class="alert alert-danger flex items-center justify-center pl-4" id="message">
                <ul>
                    @if ($errors->has('nom'))
                        @foreach ($errors->get('nom') as $message)
                            <li>Nom - {{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->has('prenom'))
                        @foreach ($errors->get('prenom') as $message)
                            <li>Prenom - {{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->has('tel'))
                        @foreach ($errors->get('tel') as $message)
                            <li>Téléphone - {{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->has('sexe'))
                        @foreach ($errors->get('sexe') as $message)
                            <li>Sexe - {{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->has('date_naissance'))
                        @foreach ($errors->get('date_naissance') as $message)
                            <li>Date de naissance - {{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->has('lieu_naissance'))
                        @foreach ($errors->get('lieu_naissance') as $message)
                            <li>Lieu de naissance - {{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->has('id_fonction'))
                        @foreach ($errors->get('id_fonction') as $message)
                            <li>Fonction - {{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->has('id_service'))
                        @foreach ($errors->get('id_service') as $message)
                            <li>Service - {{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->has('adresse'))
                        @foreach ($errors->get('adresse') as $message)
                            <li>Adresse - {{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->has('email'))
                        @foreach ($errors->get('email') as $message)
                            <li>Email - {{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->has('username'))
                        @foreach ($errors->get('username') as $message)
                            <li>Nom d'utilisateur - {{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->has('password'))
                        @foreach ($errors->get('password') as $message)
                            <li>Mot de passe - {{ $message }}</li>
                        @endforeach
                    @endif
                    @if ($errors->has('admins'))
                        @foreach ($errors->get('admins') as $message)
                            <li>Administrateur - {{ $message }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    @endif

    <section class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="ModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form autocomplete="off" class="modal-content" action="{{action('\App\Http\Controllers\AgentController@update')}}" method="POST">
                {{ csrf_field() }}

                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLongTitle">Modifier les informations d'un employé</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {{-- FORM UPDATE --}}
                    <div id="form-data-preload"></div>
                    {{-- END UPDATE --}}
                </div>

                <div class="modal-footer">
                    <div class="dropdown flex items-center pr-8" data-toggle="tooltip" title="Administrateur">
                        <button  class="btn btn-success dropdown-toggle h-8 rounded-md p-2 inline-flex items-center justify-center bg-blue-500 text-white hover:text-gray-500 hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-100" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" style="margin-right: 6px" fill="currentColor" class="bi bi-menu-button-wide-fill" viewBox="0 0 16 16">
                                <path d="M1.5 0A1.5 1.5 0 0 0 0 1.5v2A1.5 1.5 0 0 0 1.5 5h13A1.5 1.5 0 0 0 16 3.5v-2A1.5 1.5 0 0 0 14.5 0h-13zm1 2h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1 0-1zm9.927.427A.25.25 0 0 1 12.604 2h.792a.25.25 0 0 1 .177.427l-.396.396a.25.25 0 0 1-.354 0l-.396-.396zM0 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V8zm1 3v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2H1zm14-1V8a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2h14zM2 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0 4a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"/>
                            </svg>
                            Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item text-secondary flex items-center" href="" id="rapport-individuel" target="_blank" data-toggle="tooltip" title="Rapport Individuel">Rapport Individuel</a>
                            <a class="dropdown-item text-info flex items-center" href="" id="historique-pointages" target="_blank" data-toggle="tooltip" title="Historique de pointage cet employé">Historique de pointage cet employé</a>
                            <a class="dropdown-item text-primary flex items-center" href="" id="pointer" data-toggle="tooltip" title="Pointer cet employé">Pointer cet employé</a>
                            <a class="dropdown-item text-danger btn-delete flex items-center" href="" id="delete" data-toggle="tooltip" title="Supprimer cet employé">Supprimer cet employé</a>
                        </div>
                    </div>
                    <button type="submit" class="btn h-8 rounded-md p-2 inline-flex items-center justify-center bg-blue-500 text-white hover:text-gray-500 hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-100" style="background-color:#EC9628;">Enregitrer les modifications</button>
                </div>
            </form>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>