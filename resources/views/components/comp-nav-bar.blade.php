<div class="w-full h-14 flex items-center bg-white" style="
    border-bottom: solid 2px #EC9628;
">
    <div class="flex items-center px-4 font-bold">
        <a href="/menu/profil" class="ml-4 h-full flex items-center justify-center" style="
            <?php
            $route = Route::current()->uri;
            if ($route === 'menu/profil') {
            ?>
            border-bottom: solid 3px black;
            margin-bottom: 8px;
            <?php
                }
            ?>
        ">MON PROFIL</a>
        <a href="/menu/modifier-profil" class="ml-4 h-full flex items-center justify-center" style="
            <?php
            $route = Route::current()->uri;
            if ($route === 'menu/modifier-profil') {
            ?>
            border-bottom: solid 3px black;
            margin-bottom: 8px;
            <?php
                }
            ?>
        ">MODIFIER MON PROFIL</a>
    </div>
</div>