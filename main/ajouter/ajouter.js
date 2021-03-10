var popup;
var popupOn = false;

function initAjout() {
    popup = document.getElementById("formAjout");
    popup.style.top = "-50%";
}

function montrerAjout(on = true) {
    if (on != popupOn) {
        popupOn = on;
        popup.style.top = on ? "50%" : "-50%";
        popup.style.opacity = on ? "1" : "0";
        if (on) {
            montrerModif(false);
        }
    }
}

function toggleAjout() {
    montrerAjout(!popupOn);
}

function addCoureur(){
    var xhr = new XMLHttpRequest();
    var nom = document.getElementById("addNom").value;
    var prenom = document.getElementById("addPrenom").value;
    var naiss = document.getElementById("add_annee_naissance").value;
    var anPrem = document.getElementById("add_annee_prem").value;
    var pays = document.getElementById("addPays").value;
    var debut = document.getElementById("add_annee_debut").value;
    document.getElementById("message")

    xhr.open('GET','main/ajouter/ajouter.php?nom='+ nom + '&prenom=' + prenom + '&annee_n=' + naiss + '&annee_prem=' + anPrem + '&pays=' + pays +'&debut=' +debut, true);

    var ajouter = function() 
    {
        if (xhr.readyState === 4 && xhr.status === 200) 
        { 
            if (xhr.responseText != "") {
                message(xhr.responseText);
            }
            else {
                message("Paramètres incorrects");
            }

            chargerDonnee();
        }
    }
    xhr.addEventListener("readystatechange", ajouter, false);		
    xhr.send(null); 
}

function fillAnneeDebut(){
    document.getElementById("add_annee_debut").value = document.getElementById("add_annee_naissance").value;
}