var classementInfo;
var villeRecherche;

function init() {
    classementInfo = document.getElementById('menuInfos');
    villeRecherche = document.getElementById('formRecherche');

    recherche();
    charge();
}

function recherche(){
    var xhr = new XMLHttpRequest();
    var info = classementInfo.value;

    if(info == 'villes') {
        villeRecherche.style.display = "block";
    }
    else {
        villeRecherche.style.display = "none";
    }

    var path = 'php/' + info + '.php';
    
    xhr.open('GET',path, true);

    var Lire = function() 
    {
        if (xhr.readyState === 4 && xhr.status === 200) 
        { 
            document.getElementById('resultats').innerHTML = xhr.responseText;
        }
    }
    xhr.addEventListener("readystatechange", Lire, false);		
    xhr.send(null);
}

function chargerDonnee(){
    var xhr = new XMLHttpRequest();
    
    xhr.open('GET','php/villes.php?ville=' +nom.value, true);

    var Lire = function() 
    {
        if (xhr.readyState === 4 && xhr.status === 200) 
        { 
            document.getElementById('resultats').innerHTML = xhr.responseText;
        }
    }
    xhr.addEventListener("readystatechange", Lire, false);		
    xhr.send(null); 
}

function charge(){
    var rechNom;
    rechNom = document.getElementById("nom");

    var charger = function() 
    {
        chargerDonnee();
    };

    rechNom.addEventListener('keyup', charger, false);
}
