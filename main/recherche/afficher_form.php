<?php
include('../../util/util_chap11.php');
include('../../util/pdo_oracle.php');
include("../../util/ajoutdb.php");

$login = 'pphp2a_09';
$mdp = 'Poupou09';
$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";

$conn = OuvrirConnexionPDO($db, $login,$mdp);


if(!empty($_GET['sort'])){
	$sort = $_GET['sort'];
}

if(!empty($_GET['sortDesc'])){
	$sortDesc = $_GET['sortDesc'];
}

$desc = $sortDesc == 'true' ? " desc" : "";


$reqDebut = "SELECT cou.nom, prenom, annee_naissance, annee_prem, nat.nom as nom_pays FROM tdf_coureur cou
		left join tdf_app_nation ap on cou.n_coureur = ap.n_coureur
		left join tdf_nation nat on ap.code_cio = nat.code_cio
		where (trim(lower(cou.nom||' '||prenom)) like trim(lower(:nom)) || '%' 
		or trim(lower(prenom||' '||cou.nom)) like trim(lower(:nom)) || '%')
		and ap.annee_debut >= all
		(
			select annee_debut from tdf_app_nation ap2
			where ap.n_coureur = n_coureur
		)";

$reqContient = "SELECT cou.nom, prenom, annee_naissance, annee_prem, nat.nom as nom_pays FROM tdf_coureur cou
		left join tdf_app_nation ap on cou.n_coureur = ap.n_coureur
		left join tdf_nation nat on ap.code_cio = nat.code_cio
		where (trim(lower(cou.nom||' '||prenom)) like '%' || trim(lower(:nom)) || '%' 
		or trim(lower(prenom||' '||cou.nom)) like '%' || trim(lower(:nom)) || '%')
		and ap.annee_debut >= all
		(
			select annee_debut from tdf_app_nation ap2
			where ap.n_coureur = n_coureur
		)";

switch ($sort) {
	case "prenom" :
		$orderBy = 'prenom'.$desc.', nom'.$desc;
		break;
	case "naissance" :
		$orderBy = 'annee_naissance'.$desc.', nom, prenom';
		break;
	case "prem" :
		$orderBy = 'annee_prem'.$desc.', nom, prenom';
		break;
	case "nation" :
		$orderBy = 'nom_pays'.$desc.', nom, prenom';
		break;
	case "nom" :
	default :
		$orderBy = 'nom'.$desc.', prenom'.$desc;
		break;
}


baseTable($sort, $desc);

// Si trié sur nom ou prénom, affiche d'abord ceux qui commencent par la recherche
if ($sort == "nom" || $sort == "prenom") {
	rechercheNomDebut();
	rechercheNomSaufDebut();
}
else {
	rechercheNom();
}

echo "</table>";

function rechercheNom() {
	global $reqContient, $orderBy;

	recherche($reqContient . " order by " . $orderBy);
}

function rechercheNomDebut() {
	global $reqDebut, $orderBy;

	recherche($reqDebut . " order by " . $orderBy);
}

function rechercheNomSaufDebut() {
	global $reqDebut, $reqContient, $orderBy;

	recherche($reqContient . " MINUS " . $reqDebut . " order by " . $orderBy);
}

function recherche($req) {
	global $conn;

	$cur = preparerRequetePDO($conn, $req);

	// Remplacement des paramètres
	if(!empty($_GET['nom'])){
		ajouterParamPDO($cur, ':nom', $_GET['nom']);
	}
	else {
		ajouterParamPDO($cur, ':nom', '');
	}

	// Exécution requête
	$nb = LireDonneesPDOPreparee($cur, $donnee);
	AfficherDonneeAvecBoutons($donnee);
}

function AfficherDonneeAvecBoutons($donnees){
	foreach($donnees as $ligne){// Impossible de placer les apostrophes dans le html sans tout casser (il y en a déjà trop)
		// Conversion en +, qui est reconverti dans le js
		$nom = preg_replace("/'/", "+", $ligne['NOM']);
		$prenom = preg_replace("/'/", "+", $ligne['PRENOM']);
		$pays = preg_replace("/'/", "+", $ligne['NOM_PAYS']);


		echo "<tr>";

		foreach($ligne as $valeur){
			echo "<td onclick='infosCoureur(\"$nom\", \"$prenom\")'>$valeur</td>";
		}

		echo "<td><button onclick='envoiForm(\"$nom\", \"$prenom\", \"$pays\")'><i class='fa fa-pencil'></i></button></td>";
		echo "<td><button onclick='supprimerCoureur(\"$nom\", \"$prenom\")'><i class='fa fa-trash'></button></td>";
		echo "</tr>";
	}
}

function flecheOrder($sortTerm) {
	global $sort, $desc;

	if ($sort == $sortTerm) {
		return "<i class='activeSortIcon fa fa-sort-" . ($desc != '' ? "desc" : "asc") . "'></i>";
	}
	else {
		return "<i class='fa fa-sort'></i>";
	}
}

function baseTable($sort, $desc) {
	echo "<table id='tabRecherche' class='tabList'>";
	echo "
		<th onclick='setSortTerm(\"nom\")'><span>Nom".flecheOrder('nom')."</span></th>
		<th onclick='setSortTerm(\"prenom\")'><span>Prénom".flecheOrder('prenom')."</span></th>
		<th onclick='setSortTerm(\"naissance\")'><span>Naissance".flecheOrder('naissance')."</span></th>
		<th onclick='setSortTerm(\"prem\")'><span>Première participation".flecheOrder('prem')."</span></th>
		<th onclick='setSortTerm(\"nation\")'><span>Nationalité".flecheOrder('nation')."</span></th>
		<th colspan='2'><span></span></th>
	";
}
?>