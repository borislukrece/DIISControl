<?php 
    use App\Models\User;
    use App\Models\Pointage;

    $id_conneted = $_SESSION['id'];

    $agentConnected = User::find($id_conneted);

    date_default_timezone_set('UTC');
    $now = time();
    $date = date('Y-m-d', $now);

    $verification = Pointage::where('date_actuelle', $date);
?>
{{-- LARGE SCREEN --}}
<div class="w-full h-16 bg-white/[1] sticky top-0 z-20 shadow-md max-sm:hidden">
    <div class="h-full flex items-center justify-between">
        <div class="text-lg max-md:text-sm px-4 ml-28 font-bold">
            Bienvenue @php
                $nomComplet = $agentConnected->nom.' '.$agentConnected->prenom;
                echo strtoupper($nomComplet);
            @endphp
        </div>
        <div class="flex">
            <?php
                if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
                    // Connected
                    $retAdmins = User::find(intval($_SESSION['id']));
                    $retAdmins = $retAdmins->admins;
                }else{
                    $retAdmins = false;
                }
            ?>
            @if ($retAdmins)
                @if (!$verification->exists())
                    <a href="/nouvelle-journee" class="btn btn-nouvelle-journee btn-nj flex items-center justify-center bg-black hover:bg-black text-white font-bold mr-4 max-lg:text-xs">Nouvelle Journée</a>
                    <a href="/nouvelle-journee" class="btn-nj flex items-center justify-center mr-4">
                        <i class="bi bi-toggle2-off text-2xl text-black flex items-center justify-center"></i>
                    </a>
                @else
                    <div class="btn flex items-center justify-center text-white bg-green-700 hover:bg-green-700 border-none font-bold mr-1 cursor-default max-md:text-sm">Nouvelle Journée</div>
                    <div class="flex items-center justify-center mr-4">
                        <i class="bi bi-toggle2-on text-2xl text-green-700 flex items-center justify-center"></i>
                    </div>
                @endif
            @endif
            <div class="dropdown" data-toggle="tooltip" title="Administrateur" class="pr-8 flex items-center relative">
                <button  class="btn bg-gray-500 hover:bg-gray-600 text-white max-md:text-sm font-bold py-2 px-4 rounded flex items-center justify-center mr-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" style="margin-right: 6px" fill="#EC9628" class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                    </svg>
                    @php
                        $nomComplet = $agentConnected->nom.' '.$agentConnected->prenom;
                        echo strtoupper($nomComplet);
                    @endphp
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="margin-top: 0.5rem;">
                    <a class="dropdown-item flex items-center text-red-600" href="/logout" data-toggle="tooltip" title="Se deconnecter">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="margin-right: 6px" fill="currentColor" class="bi bi-door-closed-fill" viewBox="0 0 16 16">
                            <path d="M12 1a1 1 0 0 1 1 1v13h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V2a1 1 0 0 1 1-1h8zm-2 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                        </svg>
                        Se deconnecter
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SMALL SCRREN --}}
<div class="sm:hidden w-screen h-20 bg-white/[1] sticky top-0  z-50 shadow-md">
    <div class="flex items-center justify-center">
        <button class="hamber-btn h-full absolute left-3 inline-flex items-center justify-center p-2 rounded-md text-gray-400">
            <svg class="block h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div>
            <a href="/">
                <img class="h-16 w-auto" src="{{asset('/img/logo.png')}}" alt="">
            </a>
        </div>
    </div>

    <div class="hamber-menu absolute top-0 inset-x-0 p-2 transition transform origin-top-right hidden sm:hidden" style="z-index: 999">
        <div class="rounded-lg shadow-md bg-white ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="px-5 pt-4 flex items-center justify-between">
                <div>
                  <img class="h-16 w-auto" src="{{asset('/img/logo.png')}}" alt="">
                </div>
                <div class="-mr-2">
                  <button type="button" class="bg-white hamber-btn rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" id="main-menu" aria-haspopup="true">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
            </div>
            <div role="menu" aria-orientation="vertical" aria-labelledby="main-menu">
                <div class="px-2 pt-2 pb-3 space-y-1" role="none">
                    <?php
                        $route = Route::current()->uri;

                        if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
                            // Connected
                            $retAdmins = User::find(intval($_SESSION['id']));
                            $retAdmins = $retAdmins->admins;
                        }else{
                            $retAdmins = false;
                        }
                    ?>

                    @if ($retAdmins)
                        @if (!$verification->exists())
                            <div class="flex justify-between pl-3 pr-4 py-2 border-l-4 text-base font-medium text-gray-900 bg-gray-50 border-blue">
                                <a href="/nouvelle-journee" class="btn btn-nouvelle-journee btn-nj flex items-center justify-center bg-black hover:bg-black text-white font-bold mr-4">Nouvelle Journée</a>
                                <a href="/nouvelle-journee" class="btn-nj flex items-center justify-center mr-4">
                                    <i class="bi bi-toggle2-off text-2xl text-black flex items-center justify-center"></i>
                                </a>
                            </div> <hr/>
                        @else
                            <div class="flex justify-between pl-3 pr-4 py-2 border-l-4 text-base font-medium text-gray-900 bg-gray-50 border-blue">
                                <div class="btn flex items-center justify-center text-white bg-green-700 hover:bg-green-700 border-none font-bold mr-1 cursor-default">Nouvelle Journée</div>
                                <div class="flex items-center justify-center mr-4">
                                    <i class="bi bi-toggle2-on text-2xl text-green-700 flex items-center justify-center"></i>
                                </div>
                            </div>
                        @endif
                    @endif

                    
                    <a href="/" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium text-gray-900 bg-gray-50 border-blue-500" style="
                        <?php
                            if ($route === '/') {
                        ?>
                        color: #EC9628;
                        border-color: #EC9628;
                        <?php
                            }
                        ?>
                    ">Accueil</a>
                    <a href="/menu/profil" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium text-gray-900 hover:bg-gray-50 hover:text-gray-900 border-blue-500" style="
                        <?php
                            $route = Route::current()->uri;

                            $string = $route;
                            $words = explode('/', $string);
                            $first_word = $words[0];

                            if ($first_word === 'menu') {
                        ?>
                        color: #EC9628;
                        border-color: #EC9628;
                        <?php
                            }
                        ?>
                    ">Menu</a>
                    <a href="/rapport-individuel" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium text-gray-900 hover:bg-gray-50 hover:text-gray-900 border-blue-500" style="
                        <?php
                            if ($route === 'rapport-individuel') {
                        ?>
                        color: #EC9628;
                        border-color: #EC9628;
                        <?php
                            }
                        ?>
                    ">Rapport Individuel</a> <hr/>

                    @if ($retAdmins)
                        <a href="/liste-agents" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium text-gray-900 hover:bg-gray-50 hover:text-gray-900 border-blue-500" style="
                            <?php
                                if ($route === 'liste-agents') {
                            ?>
                            color: #EC9628;
                            border-color: #EC9628;
                            <?php
                                }
                            ?>
                        ">Liste d'agents</a>
                        <a href="/ajouter-agent" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium text-gray-900 hover:bg-gray-50 hover:text-gray-900 border-blue-500" style="
                            <?php
                                if ($route === 'ajouter-agent') {
                            ?>
                            color: #EC9628;
                            border-color: #EC9628;
                            <?php
                                }
                            ?>
                        ">Ajouter un agent</a> <hr/>
                    @endif
                    
                    <a href="/logout" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium text-gray-900 hover:bg-gray-50 hover:text-gray-900 border-blue">Se déconnecter <span class="text-gray-500">(<?php echo $agentConnected?->nom." ".$agentConnected?->prenom  ?>)</span> </a>
                </div>
            </div>
        </div>
    </div>
</div>