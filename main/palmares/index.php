<?php
include('../../util/util_chap11.php');
include('../../util/pdo_oracle.php');
include("../../util/ajoutdb.php");

$login = 'pphp2a_09';
$mdp = 'Poupou09';
$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";

$conn = OuvrirConnexionPDO($db, $login,$mdp);

$nom = $_GET['nom'];
$prenom = $_GET['prenom'];
$n_coureur = GetNbCoureurSpecifique($conn, $nom, $prenom);

include("index.html");

if($n_coureur != -1){
	affichage();
}
else{
	echo "
	<div id='erreur'>
		<p>Le coureur n'existe pas !</p>
	</div>";
}


echo "</div></div></body></html>";

function affichage(){
	// Infos
	$infos = getInfos();

	echo "<h2>" . $infos['PRENOM']. " " . $infos['NOM'] . "</h2>";

	// Distance parcourue
	$dist = getDistance();
	echo "<div class='distance'><h2>Distance parcourue</h2>$dist km</div>";

	
	echo "<div id='tabs'>";

	// Participations aux tours
	$anneesTDF = getAnneesTours();
	$titres = array('Année', 'Dossard');
	afficheTab($anneesTDF, $titres, "Participations au Tour");

	// Classement
	$classement = getClassement();
	$titres = array('Année', 'Rang d\'arrivée');
	afficheTab($classement, $titres, "Classement");

	// Etapes gagnées
	$etapes = getEtapesGagnees();
	$abandons = getAbandons();
	$titres = array ('Année', 'Étape');
	afficheTab($etapes, $titres, "Étapes gagnées");

	// Abandons
	$abandons = getAbandons();
	$titres = array ('Année', 'Étape', 'Libellé', 'Commentaire');
	afficheTab($abandons, $titres, "Abandons");

	// Sales Tricheries
	$salesTricheries = getSalesTricheries();
	$titres = array ('Année');
	afficheTab($salesTricheries, $titres, "SALES TRICHERIES");

	echo "</div>";
}

function getDistance(){
	global $conn;
	global $n_coureur;

	$req = "select sum(distance) as dist
	from tdf_parti_coureur
	join tdf_etape using(annee)
	where n_coureur = :n_coureur";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':n_coureur'=>$n_coureur,
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	
	LireDonneesPDOPreparee($cur, $donnees);
	return $donnees[0]['DIST'];
}

function getInfos(){
	global $conn;
	global $n_coureur;

	$req = "SELECT NOM, PRENOM, ANNEE_NAISSANCE, ANNEE_PREM
	from tdf_coureur
	where n_coureur = :n_coureur";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':n_coureur'=>$n_coureur,
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	
	LireDonneesPDOPreparee($cur, $donnees);
	return $donnees[0];
}

function getAnneesTours(){
	global $conn;
	global $n_coureur;

	$req = "SELECT annee, n_dossard 
	from tdf_parti_coureur 
	where n_coureur = :n_coureur 
	order by annee desc";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':n_coureur'=>$n_coureur,
	);
	majDonneesPrepareesTabPDO($cur, $tab);

	LireDonneesPDOPreparee($cur, $donnees);
	return $donnees;
}

function getClassement(){
	global $conn;
	global $n_coureur;

	$req = "SELECT ANNEE, RANG_ARRIVEE
	from tdf_classements_generaux 
	where n_coureur = :n_coureur
	order by annee desc";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':n_coureur'=>$n_coureur,
	);
	majDonneesPrepareesTabPDO($cur, $tab);

	LireDonneesPDOPreparee($cur, $donnees);
	return $donnees;
}

function getEtapesGagnees(){
	global $conn;
	global $n_coureur;

	$req = "select annee, n_etape
	from tdf_coureur
	join tdf_temps using(n_coureur)
	join tdf_etape using(n_etape, annee, n_comp)
	where rang_arrivee = 1 and n_coureur = :n_coureur
	order by annee desc, n_etape";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':n_coureur'=>$n_coureur,
	);
	majDonneesPrepareesTabPDO($cur, $tab);

	LireDonneesPDOPreparee($cur, $donnees);
	return $donnees;
}

function getAbandons(){
	global $conn;
	global $n_coureur;

	$req = "SELECT annee, n_etape, libelle, ab.commentaire 
	from tdf_abandon ab
	join tdf_typeaban using(c_typeaban)
	where n_coureur = :n_coureur";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':n_coureur'=>$n_coureur,
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	
	LireDonneesPDOPreparee($cur, $donnees);
	return $donnees;
}

function getSalesTricheries(){
	global $conn;
	global $n_coureur;

	$req = "SELECT annee 
	from tdf_parti_coureur 
	where n_coureur = :n_coureur and valide = 'R' 
	order by annee desc";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':n_coureur'=>$n_coureur,
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	
	LireDonneesPDOPreparee($cur, $donnees);
	return $donnees;
}

function afficheTab($donnees, $titres, $nomTab){
	if(!empty($donnees)) {
		echo "<div class='statList'><h3>$nomTab</h3><table class='tabList'>
		<tr>";

		foreach ($titres as $titre) {
			echo "<th><span>$titre</span></th>";
		}

		echo "</tr>";

		foreach ($donnees as $ligne) {
			echo "<tr>";

			foreach($ligne as $valeur){
				if($valeur != "NULL")
					echo "<td>" .$valeur."</td>";
			}

			echo "</tr>";
		}

		echo "</table></div>";
	}
}
?>