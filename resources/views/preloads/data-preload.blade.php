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

    $get_id = intval(valid_donnees($_GET['id']));

    $agent = User::find($get_id);

    $service = Service::all()->sortBy('id');
    $fonction = Fonction::all()->sortBy('id');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
<body>
    <div class="w-full h-full">
        <input type="hidden" name="form" value="3">
        <input type="hidden" name="id" value="{{ $agent->id }}">
        <div class="w-full h-full bg-white">
            <div class="w-full flex justify-center">
                <div class="pt-1">
                    <div class="px-4">
                        <div>
                            <div class="form-row w-full mt-4 lg:flex flex-row">
                                <div class="col-md-4">
                                    <label for="nom">Nom</label>
                                    <input type="nom" autocomplete="off" class="form-control" id="nom" name="nom" placeholder="Nom" value="<?php echo $agent->nom; ?>" required>
                                </div>
                                <div class="col-md-4 lg:pl-8 max-lg:pt-4">
                                    <label for="prenom">Prenom</label>
                                    <input type="prenom" autocomplete="off" class="form-control" id="prenom" name="prenom" placeholder="Prenom" value="<?php echo $agent->prenom; ?>" required>
                                </div>
                                <div class="col-md-4 lg:pl-8 max-lg:pt-4">
                                    <label for="tel">Telephone</label>
                                    <input type="tel" autocomplete="off" class="form-control" id="tel" name="tel" placeholder="Telephone" value="<?php echo $agent->tel; ?>" required>
                                </div>
                            </div>
    
                            <div class="form-row w-full mt-4 lg:flex flex-row">
                                <div class="col-md-4">
                                    <label for="sexe">Sexe <span style="color: red;">*</span> </label>
                                    <div class="form-group">
                                        <select class="form-select form-control" id="sexe" name="sexe" autocomplete="off" aria-label="Default select example" required>
                                            <?php
                                                if(!empty($agent->sexe)){
                                                    $sexe = $agent->sexe;
                                                }else{
                                                    $sexe = 0;
                                                }
    
                                                if ($sexe === 0) {
                                                    echo '<option selected value="0">M</option>';
                                                    echo '<option value="1">F</option>';
                                                }else {
                                                    echo '<option selected value="1">F</option>';
                                                    echo '<option value="0">M</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    @if ($errors->any())
                                        @foreach ($errors->get('sexe') as $message)
                                            <div class="feedback text-danger">{{ $message }}</div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="col-md-4 lg:pl-8 max-lg:pt-4">
                                    <label for="date_naissance">Date de naissance <span style="color: red;">*</span> </label>
                                    <input type="date" class="form-control" id="date_naissance" name="date_naissance" autocomplete="off" placeholder="Date de naissance" value="<?php echo $agent->date_naissance; ?>" required>
                                    @if ($errors->any())
                                        @foreach ($errors->get('date_naissance') as $message)
                                            <div class="feedback text-danger">{{ $message }}</div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="col-md-4 lg:pl-8 max-lg:pt-4">
                                    <label for="lieu_naissance">Lieu de naissance <span style="color: red;">*</span> </label>
                                    <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" autocomplete="off" placeholder="Lieu de Naissance" value="<?php echo $agent->lieu_naissance; ?>" required>
                                    @if ($errors->any())
                                        @foreach ($errors->get('lieu_naissance') as $message)
                                            <div class="feedback text-danger">{{ $message }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
    
                            <div class="form-row w-full mt-4 lg:flex flex-row">
                                <div class="col-md-4">
                                    <label for="fonction">Fonction</label>
                                    <div class="form-group">
                                        <select class="form-select form-control" id="id_fonction" name="id_fonction" aria-label="Default select" required>
                                            <?php
                                                if(!empty($agent->id_fonction)){
                                                    $input_fonction = intval($agent->id_fonction);
                                                }else{
                                                    $input_fonction = 1;
                                                }
    
                                                foreach ($fonction as $key => $item) {
                                                    if ($input_fonction === $item->id) {
                                                        echo '<option value="'.$item->id.'">'.$item->nom.'</option>';
                                                    }
                                                }
                                                foreach ($fonction as $key => $item) {
                                                    if ($input_fonction !== $item->id) {
                                                        echo '<option value="'.$item->id.'">'.$item->nom.'</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 lg:pl-8 max-lg:pt-4">
                                    <label for="id_service">Service</label>
                                    <div class="form-group">
                                        <select class="form-select form-control" id="id_service" name="id_service" aria-label="Default select" required>
                                            <?php
                                                if(!empty($agent->id_service)){
                                                    $input_service = intval($agent->id_service);
                                                }else{
                                                    $input_service = 1;
                                                }
    
                                                foreach ($service as $key => $item) {
                                                    if ($input_service === $item->id) {
                                                        echo '<option value="'.$item->id.'">'.$item->libelle.'</option>';
                                                    }
                                                }
                                                foreach ($service as $key => $item) {
                                                    if ($input_service !== $item->id) {
                                                        echo '<option value="'.$item->id.'">'.$item->libelle.'</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
    
                            <div class="form-row w-full mt-4 lg:flex flex-row">
                                <div class="col-md-4">
                                    <label for="adresse">Adresse</label>
                                    <input type="name" autocomplete="off" class="form-control" id="adresse" name="adresse" placeholder="adresse" value="<?php echo $agent->adresse; ?>">
                                </div>
                                <div class="col-md-4 lg:pl-8 max-lg:pt-4">
                                    <label for="email">Email</label>
                                    <input type="email" autocomplete="off" class="form-control" id="email" name="email" value="<?php echo $agent->email; ?>" placeholder="Email" required>
                                </div>
                            </div>
    
                            <div class="form-row w-full mt-4 lg:flex flex-row">
                                <div class="col-md-4">
                                    <label for="password">Nouveau mot de passe</label>
                                    <input type="password" autocomplete="off" class="form-control" id="password" name="password" placeholder="Nouveau mot de passe">
                                </div>
    
                                <div class="col-md-6 lg:pl-8 max-lg:pt-4">
                                    <label for="password_confirmation">Confirmer le mot de passe</label>
                                    <input type="password" autocomplete="off" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmer le nouveau mot de passe">
                                </div>
                            </div>
    
                            <div class="form-row w-full mt-4 lg:flex flex-row">
                                <div class="col-md-4">
                                    <label for="username">Nom d'utilisateur</label>
                                    <input type="username" autocomplete="off" class="form-control" id="username" name="username" value="<?php echo $agent->username; ?>" placeholder="Nom d'utilisateur">
                                </div>
                                <div class="col-md-4 lg:pl-8 max-lg:pt-4">
                                    <label for="admins">Administrateur</label>
                                    <div class="form-group">
                                        <select class="form-select form-control" id="admins" name="admins" aria-label="Default select example" required>
                                            <?php
                                                if(intval($agent->admins)){
                                                    $input_admin = intval($agent->admins);
                                                }else{
                                                    $input_admin = 0;
                                                }
        
                                                if ($input_admin === 0) {
                                                    echo '<option selected value="0">Non</option>';
                                                    echo '<option value="1">Oui</option>';
                                                }else {
                                                    echo '<option selected value="1">Oui</option>';
                                                    echo '<option value="0">Non</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>