var popupModif;
var modifOn = false;

function initModif() {
    popupModif = document.getElementById("modification");
}

function montrerModif(on = true) {
    if (on != modifOn) {
        modifOn = on;
        popupModif.style.top = on ? "50%" : "-50%";
        popupModif.style.opacity = on ? "1" : "0";
        if (on) {
            montrerAjout(false);
        }
    }
}

function toggleModif() {
    montrerModif(!modifOn);
}

function envoiForm(nom, prenom, pays){
    nom = nom.replaceAll("+", "'");
    prenom = prenom.replaceAll("+", "'");
    pays = pays.replaceAll("+", "'");

	var xhr = new XMLHttpRequest();

    xhr.open('GET','main/modifier/formModif.php?nom=' +nom +"&prenom=" +prenom +"&pays=" +pays, true);

    var ajouter = function() 
    {
        if (xhr.readyState === 4 && xhr.status === 200) 
        { 
            popupModif.innerHTML = xhr.responseText;
            montrerModif();
        }
    }
    xhr.addEventListener("readystatechange", ajouter, false);		
    xhr.send(null); 
}
function modifierForm(ancienNom, ancienPrenom, ancienPays){
    ancienNom = ancienNom.replaceAll("+", "'");
    ancienPrenom = ancienPrenom.replaceAll("+", "'");

    var xhr = new XMLHttpRequest();
    var nom = document.getElementById("nomModif").value;
    var prenom = document.getElementById("prenomModif").value;
    var naiss = document.getElementById("naissanceModif").value;
    var anPrem = document.getElementById("premModif").value;
    var pays = document.getElementById("modifPays").value;
    var debut = document.getElementById("debutModif").value;

    var motif = "";

    for(i = 0; i < document.getElementsByName('motif').length; i++){
        if(document.getElementsByName('motif')[i].checked){
            motif = document.getElementsByName('motif')[i].value;
        }
    }

    xhr.open('GET','main/modifier/modifier.php?nom=' +nom +"&prenom=" +prenom +"&naiss=" +naiss +"&anPrem=" +anPrem +"&pays=" +pays 
    +"&debut=" +debut +"&ancienNom=" +ancienNom +"&ancienPrenom=" +ancienPrenom +"&motif=" +motif +"&ancienPays=" +ancienPays, true);

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

            if (xhr.responseText.includes("effect")) {
                chargerDonnee();
                montrerModif(false);
            }
        }
    }
    xhr.addEventListener("readystatechange", ajouter, false);		
    xhr.send(null); 
}