<?php
include('../../util/util_chap11.php');
include('../../util/pdo_oracle.php');
include("../../util/ajoutdb.php");

$login = 'pphp2a_09';
$mdp = 'Poupou09';
$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";


$conn = OuvrirConnexionPDO($db, $login,$mdp);


if(!empty($_GET['nom'])){
	$nom = $_GET['nom'];
}
else {
	$nom = "";
}

if(!empty($_GET['prenom'])){
	$prenom = $_GET['prenom'];
}
else {
	$prenom = "";
}

$reqAnnee = "SELECT annee_naissance, annee_prem FROM tdf_coureur
		where nom = :nom and prenom = :prenom";

$req = "SELECT nat.nom as nom_pays, ap.annee_debut FROM tdf_coureur cou
		join tdf_app_nation ap on cou.n_coureur = ap.n_coureur
		join tdf_nation nat on ap.code_cio = nat.code_cio
		where cou.nom = :nom and prenom = :prenom
		order by ap.annee_debut desc";


$cur = preparerRequetePDO($conn, $reqAnnee);
ajouterParamPDO($cur, ':nom', $nom);
ajouterParamPDO($cur, ':prenom', $prenom);
LireDonneesPDOPreparee($cur, $donnee);

$anneeNaiss = $donnee[0]["ANNEE_NAISSANCE"];
$anneePrem = $donnee[0]["ANNEE_PREM"];



$cur = preparerRequetePDO($conn, $req);
ajouterParamPDO($cur, ':nom', $nom);
ajouterParamPDO($cur, ':prenom', $prenom);
LireDonneesPDOPreparee($cur, $donnee);

$nations = "<div class='centerFlex'><label class='block'>Nationalités :</label><table id='nationsTable'>";

foreach ($donnee as $ligne) {
	$l = "<tr>";
	foreach ($ligne as $info) {
		$l .= "<td>$info</td>";
	}
	$l .= "</tr>";

	$nations .= $l;
}

$nations .= "</table></div>";


echo "
<div class='rel'>
	<h2>Infos Coureur</h2>
	<label>$prenom</label>
	<label>$nom</label>
	<br>
	<a id='palmaresButt' href=\"main/palmares/index.php?nom=$nom&prenom=$prenom\">Palmarès</a>
	<br>
	<label>Né en $anneeNaiss</label>
	<label>Première participation en $anneePrem</label>
	<br>
	$nations
	<div class='fermeButt' onclick='montrerInfos(false)'>X</div>
</div>
";

?>