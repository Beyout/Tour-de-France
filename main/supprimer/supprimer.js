function supprimerCoureur(nom, prenom){
    nom = nom.replaceAll("+", "'");
    prenom = prenom.replaceAll("+", "'");

    if (!confirm("Etes-vous sûr de vouloir supprimer " + prenom + " " + nom + " ?"))
        return;

	var xhr = new XMLHttpRequest();

    xhr.open('GET','main/supprimer/supprimer.php?nom=' +nom +"&prenom=" +prenom, true);

    var ajouter = function() 
    {
        if (xhr.readyState === 4 && xhr.status === 200) 
        { 
            message(xhr.responseText);

            chargerDonnee();
        }
    }
    xhr.addEventListener("readystatechange", ajouter, false);		
    xhr.send(null); 
}