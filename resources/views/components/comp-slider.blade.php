<?php
    use Ramsey\Uuid\Uuid;

    use App\Models\User;
    use App\Models\CodeQr;

    $id_connected = $_SESSION['id'];
    $agentConnected = User::find($id_connected);
    
    $path_img = $agentConnected->profil_img;
?>
<div class="w-5/100 h-screen z-30 overflow-hidden px-2 fixed max-sm:hidden" style="background-image: linear-gradient(#317AC1, #497B96)">
    <div class="w-full flex justify-center items-center mt-2">
        <a href="/menu/profil" data-toggle="tooltip" title="Profil" class="w-14 h-14 bg-white text-gray-500 p-1 flex justify-center items-center rounded-full overflow-hidden">
            @if ($path_img)
                <img src="../../profil_img/<?php echo $path_img; ?>" alt="Profil" class="w-full h-auto">
            @else
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                </svg>
            @endif
        </a>
    </div>
    <a href="/" data-toggle="tooltip" title="Accueil" class="w-full flex justify-center items-center mt-8"><i class="bi bi-house-door" style="
        font-size: 1.5rem; 
        color: #FFFFFF;
        <?php
            $route = Route::current()->uri;
            if ($route === '/') {
        ?>
        color: #EC9628;
        <?php
            }
        ?>
    "></i></a>
    <a href="/menu/profil" data-toggle="tooltip" title="Menu" class="w-full flex justify-center items-center mt-2"><i class="bi bi-grid" style="
        font-size: 1.5rem; 
        color: #FFFFFF;
        <?php
            $route = Route::current()->uri;

            $string = $route;
            $words = explode('/', $string);
            $first_word = $words[0];
            
            if ($first_word === 'menu') {
        ?>
        color: #EC9628;
        <?php
            }
        ?>
    "></i></a>
    <a href="/rapport-individuel" data-toggle="tooltip" title="Rapport Individuel" class="w-full flex justify-center items-center mt-2"><i class="bi bi-card-text" style="
        font-size: 1.5rem; 
        color: #FFFFFF;
        <?php
            $route = Route::current()->uri;
            if ($route === 'rapport-individuel') {
        ?>
        color: #EC9628;
        <?php
            }
        ?>
    "></i></a>
</div>