// -------------------------------------------------------
// USUAL

function getParameter(paramName){
    const currentUrl = document.location.href;

    const url = new URL(currentUrl);
    const search_params = new URLSearchParams(url.search); 

    if(search_params.has(paramName)) {
        return search_params.get(paramName);
    }else{
        return null;
    }
}

let urlsplit = location.href.split("/");
let scriptName = urlsplit.pop().split("?")[0].split("#")[0] || "index.php";

$("body").append(`
    <div id="loader-wrapper" style="
        width: 100vw;
        height: 100vh;
        background: white;
        position: absolute;
        top: 0;
        right: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    ">
        <img src="/img/Infinity.svg" alt="loader">
    </div>
`);

document.onreadystatechange = function() 
{
    if (document.readyState != "complete") 
    {
      document.querySelector("#loader-wrapper").style.visibility = "visible";
    }

    else 
    {
      document.querySelector("#loader-wrapper").style.display = "none";
    }
};

$('.btn-nj').click((event) => {
    if(confirm('Voulez vous vraiment commencer une nouvelle journée!?')){
        console.log('Ok');
    }else{
        event.preventDefault();
    }
});

$('.btn-delete').click((event) => {
    if(!confirm('Voulez vous vraiment effectuer cette action de suppression!?')){
        event.preventDefault();
    }
});


// -------------------------------------------------------
// MENU

$("#submit" ).click(function(e) {
    e.preventDefault();
    $(".modal-send").show();
});

function fermerModal (e) {
    $(".modal-send").hide();
}

const previewImport = (event) => {
    $('#previewItem').html('');

    if (event.target.files && event.target.files.length > 0) {
        for (let file of event.target.files) {
            $('#previewItem').append(`
            <div style="
                width: 100%;
                height: auto;
                font-size: 0.8rem;
                font-weight: 600;
                background: rgba(0, 0, 0, 0.152);
                display: flex;
                padding: 0.3rem;
            ">
                <div>
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-file-earmark-plus-fill" viewBox="0 0 16 16">
                    <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM8.5 7v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 1 0z"/>
                </svg>
                ${file.name}
                </div>
            </div>`);
        }
    }
}

$("#import").on('change', previewImport);

// -------------------------------------------------------
// LISTE AGENTS

setTimeout(() => {
    $("#message").hide("fast");
}, 10000);

$("#search-btn").click(function(e){
    const search = $("#search").val();

    if (search) {
        
    }else{
        e.preventDefault();
    }
})

// -------------------------------------------------------
// AJOUTER AGENTS

setTimeout(() => {
    $("#agent-update-danger").hide("fast");
}, 10000);

// -------------------------------------------------------
// AGENTS

const callPreloadData = (val) => {
    try {
        $.ajax({
            url: 'preload-data-form',
            type: 'GET',
            data: `id=${val}`,
            dataType: 'html',
            beforeSend: function () {
                document.querySelector("#loader-wrapper").style.display = "flex";
            },
            success: function(data) {
                data = data.trim();
                if (data) {
                    // Data
                    $('#form-data-preload').html(data);
                }
                document.querySelector("#loader-wrapper").style.display = "none";
            },
            error: function(e){
                console.log(e);
                document.querySelector("#loader-wrapper").style.display = "none";
            }
        })
    } catch (err) {
        console.error(err);
    }
}

$('#modalUpdate').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('whatever');

    callPreloadData(id);
    $('#delete').attr("href", `/delete-agent?id=${id}`);
    $('#historique-pointages').attr("href", `/historique-pointages?agent=${id}`);
    $('#pointer').attr("href", `/scan?agent=${id}`);
    $('#rapport-individuel').attr("href", `/rapport-individuel-agent?agent=${id}`);
});

//---------------------------------------------------------------------------------
// HOME

function justify(dates, agent){}

$(".day").on('contextmenu', function (e) {
    e.preventDefault();
});

let tabDate = [];
function choix(e, year, month, day, agent) {    
    year = year;
    month = month;
    day = day;

    let date = `${year}-${month}-${day}`;
    
    $(e).toggleClass(function() {
        if ($(this).hasClass("selection")) {
            let index = tabDate.indexOf(`${date}`);
            if (index > -1) {
                tabDate.splice(index, 1);
            }
            $(e).css({border: "solid 1px rgba(255, 255, 255, 0.4)"});
            return "selection";
        } else {
            tabDate.push(date);
            $(e).css({border: "solid 3px black"});
            return "selection";
        }
    });
    
    if (tabDate.length) {
        // ADD BOUTON JUSTIFIER
        $('body').append(`<div class="btn-justifier btn-nouvelle-journee" onclick="justifyGroup(${agent})" style="position:absolute;bottom: 1rem;right: 6rem;"><div><button class="btn  btn-success">Justifié</button></div></div>`);
    }else{
        // REMOVE BOUTON JUSTIFIER
        $('.btn-justifier').remove();
    }
}

function justifyGroup (agent) {
    if (agent !== "" || agent.length > 0) {
        let tabMotif = [
            "Congé Annuel", 
            "Congé Maladie", 
            "Congé Maternité", 
            "Décès d'un ascendant ou d'un descendant en ligne direct", 
            "Mariage de l'agent ou d'un enfant de l'agent", 
            "Naissance survenue au foyer du fonctionnaire", 
            "Autorisations d'Absences", 
            "Autorisations spéciale d'Absences", 
            "Autres"
        ];

        let listMotif = ""
        for (let i = 0; i < tabMotif.length; i++) {
            listMotif = listMotif+`\n ${i+1}- ${tabMotif[i]}`
        }

        let motif = 0;
        while (motif === 0 || motif > 9) {
            motif = prompt(`Choisissez le motif d'absence des dates sélectionnés ${listMotif}`);
            motif = parseInt(motif.trim());
            if(!motif){
                motif = 0;
            }
        }

        if (motif === 9) {
            motif = "";
        }else{
            motif = tabMotif[motif-1];
        }

        if (motif === "" || motif === "undefined") {
            while (parseInt(motif.length) === 0) {
                // alert(motif);
                motif = prompt(`Saisissez le motif d'absence`);
                motif = motif.trim();
            }
        }

        tabDate.forEach(element => {
            try {
                $.ajax({
                    url: 'preload',
                    type: 'GET',
                    data: `date=${element}&action=INSERT_MOTIF&agent=${agent}&motif=${motif}`,
                    dataType: 'html',
                    beforeSend: function () {},
                    success: function(data) {
                        data = data.trim();
                        if (parseInt(data) === 1) {
                            location.reload();
                        }
                    },
                    error: function(error){
                        console.error(error);
                    }
                });
            } catch (err) {
                console.error(err);
            }
        });
    }
}

//---------------------------------------------------------------------------------
// SCAN

function DET_NOM_SEMAINE(index, action){
    switch (action) {
        case "FULL":
            switch (index) {
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
            switch (index) {
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

function DET_NOM_MOIS(index){
    switch (index) {
        case 1:
            return "Janvier";
            break;
        case 2:
            return "Février";
            break;
        case 3:
            return "Mars";
            break;
        case 4:
            return "Avril";
            break;
        case 5:
            return "Mai";
            break;
        case 6:
            return "Juin";
            break;
        case 7:
            return "Juillet";
            break;
        case 8:
            return "Aout";
            break;
        case 9:
            return "Septembre";
            break;
        case 10:
            return "Octobre";
            break;
        case 11:
            return "Novembre";
            break;
        case 12:
            return "Decembre";
            break;

        default:
            return "Janvier";
            break;
    }
}

setInterval(() => {
    let currentDate = new Date();

    let getDay = (currentDate.getDay() !== 0) ? currentDate.getDay() : 7;
    let getDate = currentDate.getDate();
    let getMonth = currentDate.getMonth();
    let getYear = currentDate.getFullYear();

    let hours = (currentDate.getHours() < 10) ? '0'+currentDate.getHours() : currentDate.getHours();
    let minutes = (currentDate.getMinutes() < 10) ? '0'+currentDate.getMinutes() : currentDate.getMinutes();
    let secondes = (currentDate.getSeconds() < 10) ? '0'+currentDate.getSeconds() : currentDate.getSeconds();

    $('#horloge').html(`<div>${DET_NOM_SEMAINE(getDay, "FULL")}, ${getDate} ${DET_NOM_MOIS(getMonth)} ${getYear}</div>
    <div>${hours}:${minutes}:${secondes}</div>`);
}, 1000);

//---------------------------------------------------------------------------------
// RAPPORT INDIVIDUEL

$("#rapport" ).click(function(e) {
    const date_mois = parseInt($('#date_mois').val());
    const date_annee = parseInt($('#date_annee').val());
    const date_given = `${date_annee}-${date_mois}-1`;

    $(this).attr('href', `?agent=${getParameter('agent')}&date=${date_given}`);
});

$("#rapportAgent" ).click(function(e) {
    const date_mois = parseInt($('#date_mois').val());
    const date_annee = parseInt($('#date_annee').val());
    const date_given = `${date_annee}-${date_mois}-1`;

    $(this).attr('href', `?date=${date_given}`);
});

//---------------------------------------------------------------------------------
// MENU

$(".hamber-btn").click(() => {
    $(".hamber-menu").slideToggle()
})


//----------------------------------------------------------------------------------
// PROFIL

$('#imageUpload').change(function(e){
    let reader = new FileReader();

    reader.onload = function(e){
        $('#preview').show(1000);
        $('#imagePreview').attr('src', e.target.result);
    }
    reader.readAsDataURL(this.files[0]);
});
