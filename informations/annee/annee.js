var classementAnnee;
var classementInfo;

function init() {
    classementAnnee = document.getElementById('anneeInput');
    classementInfo = document.getElementById('menuInfos');

    initSelectAnnee()
}

function initSelectAnnee() {
    var xhr = new XMLHttpRequest();

    xhr.open('GET', 'php/initAnnees.php');

    var remplir = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("anneeInput").innerHTML = xhr.responseText;

            rechercheAnnee();
        }
    }

    xhr.addEventListener("readystatechange", remplir, false);		
    xhr.send(null); 
}

function rechercheAnnee(){
	var xhr = new XMLHttpRequest();
	
	var annee = classementAnnee.value;
    var info = classementInfo.value;
    console.log(info);
    var path = 'php/' + info + '.php?annee=' + annee;
    console.log(path);

    xhr.open('GET', path, true);

    var ajouter = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("resultats").innerHTML = xhr.responseText;
        }
    }
    
    xhr.addEventListener("readystatechange", ajouter, false);		
    xhr.send(null); 
}