var rechNom;
var popupMessage;
var messageTime = 3000;
var messageOn = false;
var popupCoureur;
var coureurOn = false;
var sort = "nom";
var sortDesc = false;


function init(){
    rechNom = document.getElementById("nom");
    popupMessage = document.getElementById("popupMessage");
    popupCoureur = document.getElementById("infosCoureur");

    initAjout();
    initModif();

    charge();
}

function setSortTerm(sortBy) {
    if (sortBy != sort) {
        sort = sortBy;
        sortDesc = false;
    }
    else {
        sortDesc = !sortDesc;
    }

    chargerDonnee();
}

function message(msg) {
    popupMessage.innerHTML = msg;
    montrerMessage();
    setTimeout(function() {montrerMessage(false)}, messageTime);
}

function montrerMessage(on = true) {
    if (on != messageOn) {
        popupMessage.style.transform = "translate(-50%, " + (on ? "30%" : "-120%") + ")";
        messageOn = on;
    }
}

function montrerInfos(on = true) {
    if (on != coureurOn) {
        popupCoureur.style.left = on ? "50%" : "0%";
        popupCoureur.style.transform = "translate(" + (on ? "-50%" : "-120%") + ", -50%)";
        coureurOn = on;
    }
}

function infosCoureur(nom, prenom) {
    nom = nom.replaceAll("+", "'");
    prenom = prenom.replaceAll("+", "'");

	var xhr = new XMLHttpRequest();

    xhr.open('GET','main/recherche/infosCoureur.php?nom=' + nom +"&prenom=" + prenom, true);

    var ajouter = function() {
        if (xhr.readyState === 4 && xhr.status === 200) 
        { 
            popupCoureur.innerHTML = xhr.responseText;
            montrerInfos();
        }
    }

    xhr.addEventListener("readystatechange", ajouter, false);
    xhr.send(null);
}

function chargerDonnee(){
    var xhr = new XMLHttpRequest();
    var nom = rechNom.value;
    xhr.open('GET','main/recherche/afficher_form.php?nom='+ nom + '&sort=' + sort + '&sortDesc=' + sortDesc, true);

    var Lire = function() 
    {
        if (xhr.readyState === 4 && xhr.status === 200) 
        { 
            document.getElementById('resultat').innerHTML = xhr.responseText;
        }
    }
    xhr.addEventListener("readystatechange", Lire, false);		
    xhr.send(null); 
}

function charge(){
    chargerDonnee();

    var charger = function() 
    {
        chargerDonnee();
    };

    rechNom.addEventListener('keyup', charger, false);
}