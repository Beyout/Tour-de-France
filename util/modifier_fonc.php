<?php
/**
 * Modifie le coureur dans tdf_coureur avec les années
 * @return n_coureur
 */
function UpdateCoureurAvecAnnees($conn, $nom, $prenom, $ancienNom, $ancienPrenom, $annee_n, $annee_prem){
	$n_coureur = getN_coureur($conn, $ancienNom, $ancienPrenom);

	// mise à jour du coureur
	$req ="UPDATE tdf_coureur 
	SET nom = :nom, prenom = :prenom, annee_naissance = :annee_n, annee_prem = :annee_prem
	WHERE N_COUREUR = '$n_coureur'";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array(
		':nom' => $nom,
		':prenom' => $prenom,
		':annee_n' => $annee_n,
		'annee_prem' => $annee_prem,
	);

	majDonneesPrepareesTabPDO($cur, $tab);

	return $n_coureur;
}

/**
 * Modifie le coureur dans tdf_coureur sans les années
 * @return n_coureur
 */
function UpdateCoureurSansAnnees($conn, $nom, $prenom, $ancienNom, $ancienPrenom){
	$n_coureur = getN_coureur($conn, $ancienNom, $ancienPrenom);

	// mise à jour du coureur
	$req ="UPDATE tdf_coureur 
	SET nom = :nom, prenom = :prenom
	WHERE N_COUREUR = '$n_coureur'";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array(
		':nom' => $nom,
		':prenom' => $prenom,
	);

	majDonneesPrepareesTabPDO($cur, $tab);

	return $n_coureur;
}

function UpdateCoureur($conn, $nom, $prenom, $ancienNom, $ancienPrenom, $annee_n, $annee_prem){
	if(empty($annee_n) || empty($annee_prem) || empty($annee_debut)){
		return UpdateCoureurSansAnnees($conn, $nom, $prenom, $ancienNom, $ancienPrenom);
	}
	else {
		return UpdateCoureurAvecAnnees($conn, $nom, $prenom, $ancienNom, $ancienPrenom, $annee_n, $annee_prem);
	}
}
function UpdateCoureurCorrection($conn, $nom, $prenom, $ancienNom, $ancienPrenom, $annee_n, $annee_prem, $pays, $ancienPays, $annee_debut){
	$n_coureur = UpdateCoureur($conn, $nom, $prenom, $ancienNom, $ancienPrenom, $annee_n, $annee_prem);

	// Mise à jour de la nationalité du coureur
	if(!empty($annee_debut)){
		$ancienCIO = GetCodeCIO($conn, $ancienPays);

		$req = "UPDATE TDF_APP_NATION
		SET CODE_CIO = :cio, ANNEE_DEBUT = :annee_debut
		WHERE N_COUREUR = $n_coureur and CODE_CIO like '$ancienCIO'";
		// echo "<br>$req";
		$cur = preparerRequetePDO($conn, $req);
	
		$cio = GetCodeCIO($conn, $pays);
		$tab = array (
			':cio'=>$cio,
			':annee_debut'=>$annee_debut,
		);
	}
	else{
		$req = "UPDATE TDF_APP_NATION
		SET CODE_CIO = :cio
		WHERE N_COUREUR = $n_coureur";
		// echo "<br>$req";
		$cur = preparerRequetePDO($conn, $req);
	
		$cio = GetCodeCIO($conn, $pays);
		$tab = array (
			':cio'=>$cio,
		);
	}

	majDonneesPrepareesTabPDO($cur, $tab);
}

function UpdateCoureurChangement($conn, $nom, $prenom, $ancienNom, $ancienPrenom, $annee_n, $annee_prem, $pays, $ancienPays, $annee_debut){
	$n_coureur = UpdateCoureur($conn, $nom, $prenom, $ancienNom, $ancienPrenom, $annee_n, $annee_prem);

	// Mise à jour de la nationalité du coureur
		// Fin de l'ancienne nationalité
	$ancienCIO = GetCodeCIO($conn, $ancienPays);

	$req = "UPDATE tdf_app_nation
	SET ANNEE_FIN = :annee_fin
	WHERE N_COUREUR = $n_coureur and CODE_CIO like '$ancienCIO'";
	$cur = preparerRequetePDO($conn, $req);

	$cio = GetCodeCIO($conn, $pays);

	if(!empty($annee_debut)){
		$tab = array (
			':annee_fin'=>$annee_debut,
		);
	}
	else {
		$tab = array (
			':annee_fin'=>"to_char(sysdate, 'YYYY')",
		);
	}

	majDonneesPrepareesTabPDO($cur, $tab);

		// Début de la nouvelle nationalité
	$req = "INSERT INTO tdf_app_nation (N_COUREUR, CODE_CIO, ANNEE_DEBUT, COMPTE_ORACLE, DATE_INSERT)
	VALUES ($n_coureur, :cio, :annee_debut, 'pphp2a_09', sysdate)";
	$cur = preparerRequetePDO($conn, $req);

	if(!empty($annee_debut)){
		$tab = array (
			':cio'=>$cio,
			':annee_debut'=>$annee_debut,
		);
	}
	else {
		$tab = array (
			':cio'=>$cio,
			':annee_debut'=>"to_char(sysdate, 'YYYY')",
		);
	}

	majDonneesPrepareesTabPDO($cur, $tab);
}

function getN_coureur($conn, $nomCoureur, $prenomCoureur){
	$reqN_coureur = "SELECT n_coureur FROM tdf_coureur WHERE nom like :nom AND prenom like :prenom";

	$cur = preparerRequetePDO($conn, $reqN_coureur);

	$tab = array(
		':nom' => $nomCoureur,
		':prenom' => $prenomCoureur,
	);

	majDonneesPrepareesTabPDO($cur, $tab);

	LireDonneesPDOPreparee($cur, $val_retour);
	return $val_retour[0]['N_COUREUR'];
}

// Remplis les années de naissance, première participation et début de nationnalité et la nationnalité du coureur dont on connaît le nom, le prénom et le pays.
function GetInfosCoureur($conn, $nom, $prenom, &$annee_n, &$annee_prem, $ancienPays, &$annee_debut){
	$cio = GetCodeCIO($conn, $ancienPays);

	$req = "select annee_naissance, annee_prem, nat.nom, annee_debut from tdf_coureur cou
	join tdf_app_nation ap on cou.n_coureur = ap.n_coureur
	join tdf_nation nat on ap.code_cio = nat.code_cio
	where cou.nom = :nom and cou.prenom = :prenom and ap.code_cio = :cio";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':nom'=>$nom,
		':prenom'=>$prenom,
		':cio'=>$cio,
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);

	$annee_n = $donnees[0]['ANNEE_NAISSANCE'];
	$annee_prem = $donnees[0]['ANNEE_PREM'];
	$annee_debut = $donnees[0]['ANNEE_DEBUT'];
}
?>