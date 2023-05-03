<?php 
    use Ramsey\Uuid\Uuid;

    use App\Models\User;
    use App\Models\Pointage;
    use App\Models\CodeQr;

    function valid_donnees($donnees){
        $donnees = trim($donnees);
        $donnees = stripslashes($donnees);
        $donnees = htmlspecialchars($donnees);

        return $donnees;
    }

    date_default_timezone_set('UTC');
    $now = time();
    
    $date = date('Y-m-d', $now);
    $heure = date('H:i', $now);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pointages</title>
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
                <div class="w-full h-full">
                    {{-- HEADER--}}
                    <x-comp-header />
                    {{-- FIN HEADER --}}

                    <div class="w-full min-h-full flex items-center justify-center">
                        {{-- BODY --}}
                        <div class="w-full min-h-full sm:pl-16 sm:pr-16">
                            
                            <div class="w-full h-full min-h-screen bg-white/[0.8]">
                                <div class="w-full h-full p-3">
                                    <div class="w-full h-full" style="border-top: solid 1px #EC9628;">
                                        <div class="w-full h-full bg-white/80 lg:flex items-center justify-center" style="
                                            border-bottom: solid 3px gray;
                                            border-right: solid 1px rgba(0, 0, 0, 0.123);
                                        ">
                                            @if (!isset($_GET['agent']) || empty($_GET['agent']))
                                                <div class="h-full lg:w-1/5 bg-black/70 text-white">
                                                    <div class="w-full h-full">
                                                        <div class="text-center pt-4">Agent sélectionné</div>
                                                        <div class="text-center pt-4 font-bold">AUCUN</div>
                                                    </div>
                                                    <div id="horloge" class="w-full h-1/6 flex flex-col items-center justify-center"></div>
                                                </div>
                                            @endif

                                            <div class="h-full lg:w-1/5 bg-black/70 flex items-center justify-center" style="height: 30rem;">
                                                @if (isset($_GET['agent']) && !empty($_GET['agent']))
                                                    <div class="h-full w-full text-white">
                                                        <div class="w-full h-5/6">
                                                            <?php 
                                                                $agent = User::find(valid_donnees($_GET['agent']));
                                                            ?>
                                                            <div class="text-center pt-4">Agent sélectionné</div>

                                                            @if ($agent !== null)
                                                                <div class="text-center pt-4 font-bold">
                                                                <?php
                                                                    $myuuid = Uuid::uuid4();
                                                                    $path = '../public/qrcodes/'.$agent->id.'.svg';
                                                                    $filename = "qrcodes/$agent->id.svg";

                                                                    $qr = CodeQr::where('id_user', $agent->id)->get();

                                                                    if (!count($qr)) {
                                                                        QrCode::size(200)->generate($myuuid, $path);
                                                                        CodeQr::create([
                                                                            "id_user" => $agent->id,
                                                                            "token" => $myuuid,
                                                                        ]);
                                                                    }else{
                                                                        if (!file_exists("../public/$filename")) {
                                                                            $updateQr = CodeQr::where("id_user", $agent->id)->get();
                                                                            if(count($updateQr)){
                                                                                CodeQr::destroy($updateQr[0]->id);
                                                                                echo "<script> window.location.href = '/scan?agent=$agent->id';</script>";
                                                                            }
                                                                        }
                                                                    }

                                                                    echo strtoUpper($agent->nom).' '.strtoUpper($agent->prenom);
                                                                ?>
                                                                </div>

                                                                @if (file_exists("../public/$filename"))
                                                                    <div class="flex items-center justify-center">
                                                                        <img src="{{$filename}}" alt="QR Code" class="h-auto w-auto p-8">
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <div class="text-center font-bold pt-4">
                                                                    AUCUN
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div id="horloge" class="w-full h-auto flex flex-col items-center justify-center"></div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="h-full lg:w-2/6 flex items-center justify-center"style="height: 30rem;">
                                                @if (isset($_GET['agent']) && !empty($_GET['agent']) && $agent!==null)
                                                    <div id="option-scan" class="w-full h-full px-4 py-4 max-lg:pt-4" style="
                                                        background: rgba(192, 192, 192, 0.301);
                                                        border-right: solid 1px rgba(0, 0, 0, 0.123);
                                                    ">
                                                        <div class="w-full h-1/5 mb-1 flex items-center justify-center"><span class="font-bold">CHOISIR UNE OPTION</span></div>
                                                        <div class="w-full h-4/5 flex flex-col items-center justify-center">
                                                            <form action="{{action('\App\Http\Controllers\PostControllers@scan')}}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="agent" value="{{$agent->id}}">
                                                                <?php
                                                                    $verifExistence = Pointage::where('date_actuelle', $date);
                                                                    if ($verifExistence->exists()) {
                                                                        $verification = Pointage::where('date_actuelle', $date)->where('id_agent', $agent->id)->first();
                                                                        if ($verification) {
                                                                            if (!$verification?->heure_arrivee){
                                                                                $verif = false;
                                                                            }else{
                                                                                $verif = true;
                                                                            }
                                                                        } else {
                                                                            $verif = false;
                                                                        }
                                                                    } else {
                                                                        $verif = false;
                                                                    }
                                                                ?>
                                                                @if (!$verif)
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" name="pointage" value="arrivee" id="arrivee">
                                                                        <label class="form-check-label" for="arrivee">
                                                                            <div class="btn btn-lg m-4" style="background: #317AC1;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-door-open-fill" viewBox="0 0 16 16">
                                                                                    <path d="M1.5 15a.5.5 0 0 0 0 1h13a.5.5 0 0 0 0-1H13V2.5A1.5 1.5 0 0 0 11.5 1H11V.5a.5.5 0 0 0-.57-.495l-7 1A.5.5 0 0 0 3 1.5V15H1.5zM11 2h.5a.5.5 0 0 1 .5.5V15h-1V2zm-2.5 8c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1z"/>
                                                                                </svg>
                                                                                <div>Heure d'arrivée</div>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" name="pointage" value="depart" id="depart" disabled>
                                                                        <label class="form-check-label" for="depart">
                                                                            <div class="btn btn-lg m-4" style="background: #317AC1;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-door-closed-fill" viewBox="0 0 16 16">
                                                                                    <path d="M12 1a1 1 0 0 1 1 1v13h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V2a1 1 0 0 1 1-1h8zm-2 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                                                                </svg>
                                                                                <div>Heure de départ</div>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                @else
                                                                    <?php
                                                                        $pointage = Pointage::where('date_actuelle', $date)->where('id_agent', $agent->id)->first();
                                                                    ?>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" name="pointage" value="arrivee" id="arrivee" disabled>
                                                                        <label class="form-check-label" for="arrivee">
                                                                            <div class="btn btn-lg m-4" style="background: #317AC1;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-door-open-fill" viewBox="0 0 16 16">
                                                                                    <path d="M1.5 15a.5.5 0 0 0 0 1h13a.5.5 0 0 0 0-1H13V2.5A1.5 1.5 0 0 0 11.5 1H11V.5a.5.5 0 0 0-.57-.495l-7 1A.5.5 0 0 0 3 1.5V15H1.5zM11 2h.5a.5.5 0 0 1 .5.5V15h-1V2zm-2.5 8c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1z"/>
                                                                                </svg>
                                                                                <div>Heure d'arrivée</div>
                                                                            </div>
                                                                            <span class="text-success">{{ $pointage?->heure_arrivee }}</span>
                                                                        </label>
                                                                    </div>
                                                                    @if (!$pointage?->heure_depart)
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="pointage" value="depart" id="depart">
                                                                            <label class="form-check-label" for="depart">
                                                                                <div class="btn btn-lg m-4" style="background: #317AC1;">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-door-closed-fill" viewBox="0 0 16 16">
                                                                                        <path d="M12 1a1 1 0 0 1 1 1v13h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V2a1 1 0 0 1 1-1h8zm-2 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                                                                    </svg>
                                                                                    <div>Heure de départ</div>
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    @else
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="pointage" value="depart" id="depart" disabled>
                                                                            <label class="form-check-label" for="depart">
                                                                                <div class="btn btn-lg m-4" style="background: #317AC1;">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-door-closed-fill" viewBox="0 0 16 16">
                                                                                        <path d="M12 1a1 1 0 0 1 1 1v13h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V2a1 1 0 0 1 1-1h8zm-2 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                                                                    </svg>
                                                                                    <div>Heure de départ</div>
                                                                                </div>
                                                                                <span class="text-success">{{ $pointage?->heure_depart }}</span>
                                                                            </label>
                                                                        </div>
                                                                    @endif
                                                                @endif

                                                                <div class="flex items-center justify-center mt-8">
                                                                    <button class="btn btn-lg bg-white font-bold text-black">Pointer</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="h-full lg:w-6/12 flex items-center justify-center" style="height: 30rem;">
                                                @if (isset($_GET['agent']) && !empty($_GET['agent']) && $agent!==null)
                                                    <div class="w-full h-full items-center justify-center bg-white/10">
                                                        @if (\Session::has('success'))
                                                        <?php
                                                            $value = Pointage::where('date_actuelle', $date)->where('id_agent', $agent->id)->first();
                                                        ?>
                                                            <div class="w-full h-full flex flex-col items-center justify-center">
                                                                <div class="text-success font-bold text-lg">SUCCES</div>
                                                                <div class="mt-8 bg-gray-300 px-4 py-2 rounded-lg">
                                                                    <div class="">
                                                                        <div class="font-bold mb-8">
                                                                            {{ strtoUpper($agent->nom).' '.strtoUpper($agent->prenom) }}
                                                                        </div>
                                                                        <div class="w-full mb-8 flex items-center justify-center">
                                                                            <div>
                                                                                <div class="flex items-center">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-door-open-fill mr-2" viewBox="0 0 16 16">
                                                                                        <path d="M1.5 15a.5.5 0 0 0 0 1h13a.5.5 0 0 0 0-1H13V2.5A1.5 1.5 0 0 0 11.5 1H11V.5a.5.5 0 0 0-.57-.495l-7 1A.5.5 0 0 0 3 1.5V15H1.5zM11 2h.5a.5.5 0 0 1 .5.5V15h-1V2zm-2.5 8c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1z"/>
                                                                                    </svg>
                                                                                    Heure d'arrivée
                                                                                    <span class="text-success pl-2">{{ $value?->heure_arrivee }}</span>
                                                                                </div>
                                                                                @if ($value->heure_depart !== null)
                                                                                    <div class="mt-4 flex items-center">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-door-closed-fill mr-2" viewBox="0 0 16 16">
                                                                                            <path d="M12 1a1 1 0 0 1 1 1v13h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V2a1 1 0 0 1 1-1h8zm-2 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                                                                        </svg>
                                                                                        Heure de depart
                                                                                        <span class="text-success pl-2">{{ $value?->heure_depart }}</span>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="w-full pb-4 flex items-center justify-center">
                                                                            <div>
                                                                                Total d'heure(s)
                                                                                <span class="text-success">{{str_replace(":", 'h', $value?->total_heure)}}m</span> 
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="w-full h-full font-bold px-3 text-center">
                                                            @if ($errors->any())
                                                                @foreach ($errors->all() as $error)
                                                                    @if (intval($error) === 1)
                                                                        <div class="scan-error w-full h-full flex flex-col items-center justify-center">
                                                                            <div class="text-danger" class="w-full h-full font-bold text-lg mt-8 text-center">
                                                                                Veuillez s'il vous plaît choisir une option "Heure d'arrivée, Heure de départ".
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    @if (intval($error) === 2)
                                                                        <div class="scan-error w-full h-full flex flex-col items-center justify-center">
                                                                            <div class="text-danger w-full h-full font-bold text-lg mt-8 text-center">
                                                                                {{ (!$agent->sexe) ? "M." : "Mme" }}
                                                                                {{ strtoUpper($agent->nom).' '.strtoUpper($agent->prenom) }}
                                                                                à déjà pointé à l'arrivée, veuillez choisir l'option départ
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    @if (intval($error) === 3)
                                                                        <div class="scan-error w-full h-full flex flex-col items-center justify-center">
                                                                            <div class="text-danger w-full h-full font-bold text-lg mt-8 text-center">
                                                                                {{ (!$agent->sexe) ? "M." : "Mme" }}
                                                                                {{ strtoUpper($agent->nom).' '.strtoUpper($agent->prenom) }}
                                                                                n'à pas été pointé à l'arrivée, veuillez choisir l'option arrivée
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    @if (intval($error) === 4)
                                                                        <div class="scan-error w-full h-full flex flex-col items-center justify-center">
                                                                            <div class="text-danger w-full h-full font-bold text-lg mt-8 text-center">
                                                                                {{ (!$agent->sexe) ? "M." : "Mme" }}
                                                                                {{ strtoUpper($agent->nom).' '.strtoUpper($agent->prenom) }}
                                                                                à déjà pointé à son départ, veuillez attendre la journée de demain
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    @if (intval($error) === 5)
                                                                        <div class="scan-error w-full h-full flex flex-col items-center justify-center">
                                                                            <div class="text-danger w-full h-full font-bold text-lg mt-8 text-center">
                                                                                Veuillez debuter une nouvelle journée pour effectuer un pointage
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
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
                                    <div class="w-5 h-full bg-white bg-opacity-40 shadow-md" style="
                                        /* width:5%;
                                        height: 100%;
                                        background: rgba(255, 255, 255, 0.4);
                                        box-shadow: 0 4px 2px 3px rgba(0, 0, 0, 0.26); */
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