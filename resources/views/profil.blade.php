<?php 
use Ramsey\Uuid\Uuid;

use App\Models\User;
use App\Models\Service;
use App\Models\Fonction;
use App\Models\Sous_direction;
use App\Models\Direction;
use App\Models\CodeQr;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$id_connected = $_SESSION['id'];

$agentConnected = User::find($id_connected);

$path_img = $agentConnected->profil_img;

$nom_connecte = $agentConnected->nom;
$prenom_connecte = $agentConnected->prenom;
$sexe_connecte = $agentConnected->sexe;
$date_naissance_connecte = $agentConnected->date_naissance;
$lieu_naissance_connecte = $agentConnected->lieu_naissance;
$tel_connecte = $agentConnected->tel;
$fonction_connecte = $agentConnected->id_fonction;
$service_connecte = $agentConnected->id_service;
$adresse_connecte = $agentConnected->adresse;
$email_connecte = $agentConnected->email;
$username_connecte = $agentConnected->username;

$service = Service::find($service_connecte);
$fonction = Fonction::find($fonction_connecte);

if (intval($service->id_sous_direction) === 0) {
    $sous_direction = 'NULL';
} else {
    $search_sous_direction = Sous_direction::find($service->id_sous_direction);
    $sous_direction = $search_sous_direction->libelle;
}

if (intval($service->id_sous_direction) === 0) {
    $search_direction = Direction::find(1);
    $direction = $search_direction->libelle;
} else {
    $search_sous_direction = Sous_direction::find($service->id_sous_direction);
    $search_direction = Direction::find($search_sous_direction->id_direction);
    $direction = $search_direction->libelle;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mon profil</title>
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
                                        {{-- NAV BAR --}}
                                        <x-comp-nav-bar />
                                        {{-- END NAV BAR --}}

                                        <div class="h-10"></div>

                                        <div class="w-full h-full">
                                            <div class="w-full h-full">
                                                <div class="w-full h-14 bg-white pl-4 text-base font-bold flex items-center" style="
                                                    border-bottom: solid 3px gray;
                                                ">
                                                    Mon dossier personnel&nbsp;<span style="color: #EC9628;"> / PROFIL</span>
                                                </div>

                                                <div class="w-full h-full">
                                                    <div class="w-full h-full bg-white p-6">
                                                        <div class="w-full h-full lg:flex flex-row justify-center">
                                                            <div class="lg:w-6/12 h-full max-lg:mb-16 ">
                                                                <div class="w-full mt-4 leading-10">
                                                                    <div class="w-full lg:w-8/12 mb-8 z-0">
                                                                        <div class="w-full flex items-center justify-between mt-2">
                                                                            <div data-toggle="tooltip" title="Profil" class="w-16 h-16 bg-none text-gray-500 p-1 flex justify-center items-center rounded-full cursor-pointer shadow overflow-hidden">
                                                                                @if ($path_img)
                                                                                    <img src="../profil_img/<?php echo $path_img; ?>" alt="Profil" class="w-full h-auto" data-toggle="modal" data-target="#modalProfil">
                                                                                @else
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                                                                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                                                                    </svg>
                                                                                @endif
                                                                            </div>

                                                                            <div class="hidden" id="preview">
                                                                                <div class="flex flex-col items-center justify-center">
                                                                                    <div class="w-16 h-16 bg-none text-gray-500 p-1 flex justify-center items-center rounded-full shadow overflow-hidden">
                                                                                        <img src="" alt="Preview import profil" id="imagePreview" class="w-full h-auto">
                                                                                    </div>
                                                                                    <div class="text-gray-400">
                                                                                        Profil Preview
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <form class="mt-6" method="POST" enctype="multipart/form-data" action="{{action('\App\Http\Controllers\AgentController@profile_pic')}}">
                                                                            @csrf

                                                                            <div class="input-group z-0">
                                                                                <input type="file" class="form-control z-0" id="imageUpload" name="image" accept="image/*">
                                                                                <label class="input-group-text z-0" for="imageUpload">Upload</label>
                                                                            </div>

                                                                            <button type="submit" class="border-none bg-none mb-3"><span class="text-gray-500 font-semibold">Modifier ma photo de profil</span></button>
                                                                        </form>

                                                                        <hr />
                                                                    </div>
                                                                    <div>
                                                                        <span>Nom: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        ">{{ $nom_connecte }}</span>
                                                                    </div>

                                                                    <div>
                                                                        <span>Prenom: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        ">{{ $prenom_connecte }}</span>
                                                                    </div>

                                                                    <div>
                                                                        <span>Sexe: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        ">{{ !$sexe_connecte ? 'M' : 'F' }}</span>
                                                                    </div>

                                                                    <div>
                                                                        <span>Date de naissance: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        ">{{ $date_naissance_connecte }}</span>
                                                                    </div>

                                                                    <div>
                                                                        <span>Lieu de naissance: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        ">{{ $lieu_naissance_connecte }}</span>
                                                                    </div>

                                                                    <div>
                                                                        <span>Telephone: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        ">{{ $tel_connecte }}</span>
                                                                    </div>

                                                                    <div>
                                                                        <span>Adresse: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        ">{{ $adresse_connecte }}</span>
                                                                    </div>
                                                                
                                                                    <div>
                                                                        <span>Direction: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        ">{{ $direction }}</span>
                                                                    </div>

                                                                    <div>
                                                                        <span>Sous Direction: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        "><?php if ($sous_direction === "NULL") {
                                                                            ?>
                                                                            <span style="color: gray;">(Aucune sous
                                                                                direction)</span> Secrétariat
                                                                            <?php
                                                                        }else{
                                                                            echo $sous_direction;
                                                                        } ?></span>
                                                                    </div>

                                                                    <div>
                                                                        <span>Service: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        ">{{ $service->libelle }}</span>
                                                                    </div>

                                                                    <div>
                                                                        <span>Fonction: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        ">{{ $fonction->nom }}</span>
                                                                    </div>

                                                                    <div>
                                                                        <span>Email: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        ">{{ $email_connecte }}</span>
                                                                    </div>

                                                                    <div>
                                                                        <span>Nom d'utilisateur: </span>
                                                                        <span
                                                                            style="
                                                                            padding-left: 1rem;
                                                                            color: #EC9628;
                                                                        ">{{ $username_connecte }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <hr />
                                                            
                                                            <div class="lg:w-6/12 h-full flex justify-center">
                                                                <div class="w-full h-full flex flex-col items-center justify-center">
                                                                    <?php
                                                                    $myuuid = Uuid::uuid4();
                                                                    $path = '../public/qrcodes/' . $id_connected . '.svg';
                                                                    $filename = "/qrcodes/$id_connected.svg";
                                                                    
                                                                    $qr = CodeQr::where('id_user', $id_connected)->get();
                                                                    
                                                                    if (!count($qr)) {
                                                                        QrCode::size(200)->generate($myuuid, $path);
                                                                        CodeQr::create([
                                                                            'id_user' => $id_connected,
                                                                            'token' => $myuuid,
                                                                        ]);
                                                                    } else {
                                                                        if (!file_exists("../public/$filename")) {
                                                                            $updateQr = CodeQr::where('id_user', $id_connected)->get();
                                                                            if (count($updateQr)) {
                                                                                CodeQr::destroy($updateQr[0]->id);
                                                                                echo "<script> window.location.href = '/menu/profil';</script>";
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <div class="pt-2 text-black/75">Votre QR Code personnel</div>
                                                                    <img src="{{ $filename }}" alt="QR Code" class="w-32 h-auto p-8 cursor-pointer" data-toggle="modal" data-target="#modal">
                                                                    <div class="w-6/12 h-1 bg-black/30">&nbsp;</div>
                                                                    <a href="{{ $filename }}" download="{{ $nom_connecte . '.svg' }}" style="padding-top: 0.5rem;color:#EC9628;">Télécharger le QR Code </a>
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
                    @if ($errors->has('image'))
                        @foreach ($errors->get('image') as $message)
                            <li>Image - {{ $message }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    @endif

    <section class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="ModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg bg-white" role="document">
            <div class="modal-header">
                <h5 class="modal-title font-bold" id="ModalLongTitle">Mon Qr Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="w-full h-80 flex items-center justify-center">
                    <img src="{{ $filename }}" alt="QR Code" class="w-72 h-auto p-8 cursor-pointer">
                </div>
            </div>
        </div>
    </section>

    <section class="modal fade" id="modalProfil" tabindex="-1" role="dialog" aria-labelledby="ModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg bg-white" role="document">
            <div class="modal-header">
                <h5 class="modal-title font-bold" id="ModalLongTitle">Image de profil</h5>
                <button type="button" class="close" data-dismiss="modalProfil" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="w-full h-full py-28 flex items-center justify-center">
                    <div data-toggle="tooltip" title="Profil" class="w-72 h-auto bg-none p-1 flex justify-center items-center shadow">
                        @if ($path_img)
                            <img src="../profil_img/<?php echo $path_img; ?>" alt="Profil" class="w-full h-auto" data-toggle="modal" data-target="#modalProfil">
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                            </svg>
                        @endif
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