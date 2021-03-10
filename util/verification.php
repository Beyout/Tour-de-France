<?php
function GetDernièreAnnéeTour($conn){
	$req = "select max(ANNEE_PREM) from tdf_coureur";
	LireDonneesPDO1($conn, $req, $donnees);
	$annee_max = $donnees[0]["MAX(ANNEE_PREM)"];

	return $annee_max;
}

// Vérifie les champs renseignés, vérifie l'année de naissance et l'année de première participation au Tour de France.
function VerificationChampsAvecAnnee($conn, &$erreur, &$sans_annee, &$nom, &$prenom, &$annee_n, &$annee_prem, &$pays, &$annee_debut){
	$nb_erreur = 0;
	$msgs = array();

	if(!empty($_POST['cou_nom']) && verifierNom($_POST['cou_nom'])){
		$nom = convertirNom($_POST['cou_nom']);
	}
	else{
		$erreur = true;
		$nb_erreur++;
		array_push($msgs, "nom");
	}

	if(!empty($_POST['cou_prenom']) && verifierPrenom($_POST['cou_prenom'])){
		$prenom = convertirPrenom($_POST['cou_prenom']);
	}
	else{
		$erreur = true;
		$nb_erreur++;
		array_push($msgs, "prénom");
	}

	if(!empty($_POST['cou_annee_naissance']) && !empty($_POST['cou_annee_prem']) && !empty($_POST['cou_annee_debut'])){
		$sans_annee = !VérificationAnnées($conn, $_POST['cou_annee_naissance'], $_POST['cou_annee_prem'], $_POST['cou_annee_debut'], $annee_n, $annee_prem, $annee_debut, $erreur);
	}
	else {
		$sans_annee = true;
	}

	if ($sans_annee) {
		$nb_erreur += 2;
		array_push($msgs, "années");
	}

	if(!empty($_POST['cou_pays'])){
		$pays = $_POST['cou_pays'];
	}
	else{
		$erreur = true;
		$nb_erreur++;
		array_push($msgs, "nationalité");
	}

	// Erreur dans le renseignement des années
	if($erreur){
		$msgerreur = implode(", ", $msgs);
		$msgerreur = mb_strtoupper(mb_substr($msgerreur, 0, 1)) . mb_substr($msgerreur, 1);		// Maj

		if($nb_erreur > 1) {
			$msgerreur = $msgerreur . " invalides";
		} else {
			$msgerreur = $msgerreur . " invalide";
		}
		echo $msgerreur;
	}
}

// Vérifie les champs renseignés.
function VérificationChampsSansAnnee(&$erreur, &$nom, &$prenom){
	$msg="";
	if(!empty($_POST['cou_nom']) && verifierNom($_POST['cou_nom'])){
		$nom = convertirNom($_POST['cou_nom']);
	}
	else{
		$erreur = true;
		$msg = "nom";
	}

	if(!empty($_POST['cou_prenom']) && verifierPrenom($_POST['cou_prenom'])){
		$prenom = convertirPrenom($_POST['cou_prenom']);
	}
	else{
		$erreur = true;
		$msg .="; prenom";
	}

	if($erreur){
		$msgerreur = "erreur dans les cases ".$msg;
		echo $msgerreur;
	}
}

// Vérifie les champs renseignés, vérifie l'année de naissance et l'année de première participation au Tour de France.
function VerificationChampsAvecAnneeModif(&$erreur, &$sans_annee, &$nom, &$prenom, &$annee_n, &$annee_prem, &$pays, &$annee_debut){
	$nb_erreur = 0;
	$msgs = array();

	if(!empty($_POST['cou_nom']) && verifierNom($_POST['cou_nom'])){
		$nom = convertirNom($_POST['cou_nom']);
	}
	else{
		$erreur = true;
		$nb_erreur++;
		array_push($msgs, "nom");
	}

	if(!empty($_POST['cou_prenom']) && verifierPrenom($_POST['cou_prenom'])){
		$prenom = convertirPrenom($_POST['cou_prenom']);
	}
	else{
		$erreur = true;
		$nb_erreur++;
		array_push($msgs, "prénom");
	}

	if(!empty($_POST['cou_annee_naissance']) && !empty($_POST['cou_annee_prem']) && !empty($_POST['cou_annee_debut'])){
		$sans_annee = !VérificationAnnéesModif($_POST['cou_annee_naissance'], $_POST['cou_annee_prem'], $_POST['cou_annee_debut'], $annee_n, $annee_prem, $annee_debut);
	}
	else {
		$sans_annee = true;
	}

	if ($sans_annee) {
		$nb_erreur += 2;
		array_push($msgs, "années");
	}

	if(!empty($_POST['cou_pays'])){
		$pays = $_POST['cou_pays'];
	}
	else{
		$erreur = true;
		$nb_erreur++;
		array_push($msgs, "nationalité");
	}
	
	if($erreur){
		$msgerreur = implode(", ", $msgs);
		$msgerreur = mb_strtoupper(mb_substr($msgerreur, 0, 1)) . mb_substr($msgerreur, 1);		// Maj

		if($nb_erreur > 1) {
			$msgerreur = $msgerreur . " invalides";
		} else {
			$msgerreur = $msgerreur . " invalide";
		}
		echo $msgerreur;
	}
}

// Vérifie si le coureur est déjà dans la base de données, retourne vrai s'il n'y a pas de coureurs avec ce nom et prénom, faux sinon.
function VérificationCoureurDB($conn, $nom, $prenom){
	// echo "tab";
	$req = 'select nom, prenom from tdf_coureur 
	where upper(nom) like upper(:nom) and upper(prenom) like upper(:prenom)';
	$cur = preparerRequetePDO($conn, $req);

	$tab = array(
		':nom' => $nom,
		':prenom' => $prenom,
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	$nb = LireDonneesPDOPreparee($cur, $donnees);
	// Il ne doit y avoir aucun résultat.
	return ($nb == 0);
}

// Vérifie si le coureur modifié est dans la base, retourne vrai s'il n'y est pas ou si c'est lui-même, faux sinon
function VérificationCoureurDBModif($conn, $nom, $prenom, $ancienNom, $ancienPrenom){
	// On récupère les numéros de coureur du coureur actuel et du potentiellement coureur futur au cas-où l'utilisateur insère un coureur qui est déjà dans la bdd
	$ancienNCoureur = GetNbCoureurSpecifique($conn, $ancienNom, $ancienPrenom);
	$nouveauNCoureur = GetNbCoureurSpecifique($conn, $nom, $prenom);

	return (VérificationCoureurDB($conn, $nom, $prenom) || $ancienNCoureur == $nouveauNCoureur);
}
// Vérifie si le coureur a participé au TDF, retourne vrai si oui, faux sinon.
function VérificationParticipationsTDF($conn, $n_coureur){
	$req = "select count(n_coureur) from tdf_parti_coureur 
    where n_coureur = '$n_coureur'";
	LireDonneesPDO1($conn,$req, $donnees);

	$participation = true;

	if($donnees[0]['COUNT(N_COUREUR)'] == 0){
		$participation = false;
	}
	return $participation;
}

/**
 * Vérifie que l'année est correct et inférieure à la dernière année du TDF
 * @return 
 * 		true : année correcte et inférieure à la dernière année du tdf
 * 		false sinon
 */
function VérificationAnnéeClassement($conn, $annee){
	if(!empty($annee) && $annee <= GetDernièreAnnéeTour($conn))
		return true;
	else
		return false;
}

/**
 * Vérifie que les années sont correctes
 *  @return
 * 		false : données incorrectes
 * 		true : données existantes et correctes
 */
function VérificationAnnées($conn, $post_annee_n, $post_annee_prem, $post_annee_debut, &$annee_n, &$annee_prem, &$annee_debut, &$erreur){
	if($post_annee_n <= date('Y') + 60 && $post_annee_prem >= $post_annee_n + 20 && $post_annee_debut >= $post_annee_n
	&& $post_annee_debut <= $post_annee_prem && $post_annee_prem >= GetDernièreAnnéeTour($conn) && $post_annee_n && ($post_annee_prem - $post_annee_n) <= 60){
		$annee_n = $post_annee_n;
		$annee_prem = $post_annee_prem;
		$annee_debut = $post_annee_debut;

		return true;
	}
	$erreur = true;
	return false;	
}

/**
 * Vérifie que les années sont correctes
 * Return values :
 * 		false : données incorrectes
 * 		true : données existantes et correctes
 */
function VérificationAnnéesModif($post_annee_n, $post_annee_prem, $post_annee_debut, &$annee_n, &$annee_prem, &$annee_debut){
	if($post_annee_prem >= $post_annee_n + 20 && ($post_annee_prem - $post_annee_n) <= 60 && $post_annee_debut >= $post_annee_n && $post_annee_debut <= $post_annee_prem){
		$annee_n = $post_annee_n;
		$annee_prem = $post_annee_prem;
		$annee_debut = $post_annee_debut;

		return true;
	}
	return false;	
}

function virgule(&$msg,$nb_erreur){
	if($nb_erreur>0){
		$msg.=",";
	}
}
?>