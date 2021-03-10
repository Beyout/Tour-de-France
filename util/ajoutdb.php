<?php
// retourne le numéro de coureur maximum
function GetNbCoureurs($conn){
	// requête pour récupérer le nombre de coureurs.
	$req_nb_coureurs = "select max(n_coureur) from tdf_coureur";
	LireDonneesPDO1($conn, $req_nb_coureurs, $tab_res_nb_coureurs);

	// Permet de récupérer le nombre de coureurs dans la variable nb_coureurs.
	$nb_coureurs = $tab_res_nb_coureurs[0]["MAX(N_COUREUR)"];

	return $nb_coureurs;
}

// retourne le numéro de coureur du coureur spécifié
function GetNbCoureurSpecifique($conn, $nom, $prenom){
	$req = "select n_coureur from tdf_coureur
	where nom like :nom and prenom like :prenom";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':nom'=>$nom,
		':prenom'=>$prenom,
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);
	if(!empty($donnees[0]['N_COUREUR']))
		return $donnees[0]['N_COUREUR'];

	return -1;
}

// retourne le code CIO d'un pays dont le nom est passé en paramètre
function GetCodeCIO($conn, $nom_pays){
	$req = "select code_cio from tdf_nation where lower(nom) like lower(:nom)";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':nom'=>strtoupper($nom_pays),
	);
	majDonneesPrepareesTabPDO($cur, $tab);

	LireDonneesPDOPreparee($cur, $donnees);
	return $donnees[0]['CODE_CIO'];
}

// ajoute un numéro de coureur et un code cio dans la table tdf_app_nation
function AjouteCoureurNationAvecAnnee($conn, $n_coureur, $cio, $annee_debut){
	$req = "insert into TDF_APP_NATION (N_COUREUR, CODE_CIO, ANNEE_DEBUT, COMPTE_ORACLE, DATE_INSERT)
			values ($n_coureur, '$cio', :annee_debut, 'pphp2a_09', sysdate)";
	$cur = preparerRequetePDO($conn, $req);

	$tab =array (
		':annee_debut'=>$annee_debut,
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	Commit($conn);
}

// ajoute un numéro de coureur et un code cio dans la table tdf_app_nation
function AjouteCoureurNationSansAnnee($conn, $n_coureur, $cio){
	$req = "insert into TDF_APP_NATION (N_COUREUR, CODE_CIO, COMPTE_ORACLE, DATE_INSERT)
			values ($n_coureur, '$cio', 'pphp2a_09', sysdate)";

	majDonneesPDO($conn, $req);
	Commit($conn);
}



// Ajoute un coureur à la base de données en renseignant son année de naissance et son année de première participation
function AjouterCoureurAvecAnnees($conn, $nom, $prenom, $annee_n, $annee_prem, $pays, $annee_debut){
	$nb_coureurs = GetNbCoureurs($conn);
	$nb_coureurs++;
	
	// requête pour ajouter le coureur à la base en spécifiant son année de naissance et son année de première participation.
	$req = "insert into TDF_COUREUR 
			values ($nb_coureurs, :nom, :prenom, :annee_n, :annee_prem, 'pphp2a_09', sysdate)";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':nom'=>$nom,
		':prenom'=>$prenom,
		':annee_n'=>$annee_n,
		':annee_prem'=>$annee_prem,
	);

	majDonneesPrepareesTabPDO($cur, $tab);
	AjouteCoureurNationAvecAnnee($conn, $nb_coureurs, GetCodeCIO($conn, $pays), $annee_debut);
	Commit($conn);
}

// Ajoute un coureur à la base de données sans renseigner son année de naissance et son année de première participation
function AjouterCoureurSansAnnees($conn, $nom, $prenom, $pays){
	$nb_coureurs = GetNbCoureurs($conn);
	$nb_coureurs++;
	
	// requête pour ajouter le coureur à la base sans spécifier son année de naissance ni son année de première participation.
	$req = "insert into TDF_COUREUR (N_COUREUR, NOM, PRENOM, COMPTE_ORACLE, DATE_INSERT)
			values ($nb_coureurs, :nom, :prenom, 'pphp2a_09', sysdate)";

	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':nom'=>$nom,
		':prenom'=>$prenom,
	);

	majDonneesPrepareesTabPDO($cur, $tab);

	AjouteCoureurNationSansAnnee($conn, $nb_coureurs, GetCodeCIO($conn, $pays));
	Commit($conn);
}

function Commit($conn){
	$req = "commit";
	majDonneesPDO($conn, $req);
}
?>