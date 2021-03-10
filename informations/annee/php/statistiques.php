<?php
include('../../../util/util_chap11.php');
include('../../../util/pdo_oracle.php');
include('../../../util/ajoutdb.php');
include('../../../util/verification.php');

$login = 'pphp2a_09';
$mdp = 'Poupou09';
$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";

$conn = OuvrirConnexionPDO($db, $login,$mdp);

$annee = $_GET['annee'];

if(VérificationAnnéeClassement($conn, $annee)){
	AffichageStats();
}
else {
	echo "
	<div id='erreur'>
		<p>L'année est mauvaise !</p>
	</div>";
}

function AffichageStats(){
	// Distance totale
	$dist = getDistanceTotale();
	echo "<div class='stat'><h2>Longueur totale</h2><span class='number'>$dist km</span></div>";

	// Etape la plus longue
	$etLongue = getEtapeLongue();
	$etape = $etLongue['N_ETAPE'];
	$villeD = $etLongue['VILLE_D'];
	$villeA = $etLongue['VILLE_A'];
	$dist = $etLongue['DISTANCE'];
	$date = $etLongue['DATETAPE'];
	
	echo "<div class='stat'><h2>Étape la plus longue</h2>
	étape n°$etape<br>
	$villeD - $villeA<br>
	$date<br>
	<span class='number'>$dist km</span>
	</div>";

	// Etape la plus courte
	$etCourte = getEtapeCourte();
	$etape = $etCourte['N_ETAPE'];
	$villeD = $etCourte['VILLE_D'];
	$villeA = $etCourte['VILLE_A'];
	$dist = $etCourte['DISTANCE'];
	$date = $etCourte['DATETAPE'];

	echo "<div class='stat'><h2>Étape la plus courte</h2>
	étape n°$etape<br>
	$villeD - $villeA<br>
	$date<br>
	<span class='number'>$dist km</span>
	</div>";

	// Nombre de jeunes et de nouveaux
	$n_jeune = getJeune();
	$n_nouveau = getNouveau();
	echo "<div class='stat'><h2>Nombre de jeunes</h2><span class='number'>$n_jeune</span></div>";
	echo "<div class='stat'><h2>Nombre de nouveaux</h2><span class='number'>$n_nouveau</span></div>";

	// Pays participants
	$pays = getPays();
	$titres = array ('Pays participants');
	afficheTab($pays, $titres);
}

function getEtapeLongue(){
	global $conn;
	global $annee;

	$req = "select n_etape, ville_d, ville_a, distance, DATETAPE
	from tdf_etape
	where annee = :annee and distance >= all
	(
	select distance
	from tdf_etape
	where annee = :annee
	)";

	$cur = preparerRequetePDO($conn, $req);
		
	$tab = array (
		':annee'=>$annee
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);

	return $donnees[0];
}

function getEtapeCourte(){
	global $conn;
	global $annee;

	$req = "select n_etape, ville_d, ville_a, distance, DATETAPE
	from tdf_etape
	where annee = :annee and distance <= all
	(
	select distance
	from tdf_etape
	where annee = :annee
	)";

	$cur = preparerRequetePDO($conn, $req);
		
	$tab = array (
		':annee'=>$annee
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);

	return $donnees[0];
}

function getDistanceTotale(){
	global $conn;
	global $annee;

	$req = "select sum(distance) as dist
	from tdf_etape
	where annee = :annee";

	$cur = preparerRequetePDO($conn, $req);
		
	$tab = array (
		':annee'=>$annee
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);

	return $donnees[0]['DIST'];
}

function getNouveau(){
	global $conn;
	global $annee;

	$req = "SELECT count(*) as N_NOUVEAU
	from tdf_parti_coureur par
	join tdf_coureur cou using(n_coureur)
	where annee = :annee and annee = cou.annee_prem";

	$cur = preparerRequetePDO($conn, $req);
		
	$tab = array (
		':annee'=>$annee
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);

	return $donnees[0]['N_NOUVEAU'];
}

function getJeune(){
	global $conn;
	global $annee;

	$req = "SELECT count(*) as N_JEUNES
	from tdf_parti_coureur
	where annee = :annee and jeune like 'o'";

	$cur = preparerRequetePDO($conn, $req);
		
	$tab = array (
		':annee'=>$annee
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);

	return $donnees[0]['N_JEUNES'];
}

function getPays(){
	global $conn;
	global $annee;

	$req = "SELECT distinct nat.nom
	from tdf_parti_coureur
	join tdf_coureur cou using(n_coureur)
	join tdf_app_nation using(n_coureur)
	join tdf_nation nat using(code_cio)
	where annee = :annee";

	$cur = preparerRequetePDO($conn, $req);
		
	$tab = array (
		':annee'=>$annee
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);

	return $donnees;
}

function afficheTab($donnees, $titres){
	if(!empty($donnees)){
		echo "<table class='tabList smallTab'>
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

		echo "</table>";
	}
}
?>