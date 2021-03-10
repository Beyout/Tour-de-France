<?php
include('../util/util_chap11.php');
include('../util/pdo_oracle.php');
include('../util/verification.php');

$login = 'pphp2a_09';
$mdp = 'Poupou09';
$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";

$conn = OuvrirConnexionPDO($db, $login,$mdp);

include('stats.html');

function AffichageStats(){
	// Distance totale
	$dist = getDistanceTotale();
	echo "<div class='stat'><h2>Longueur totale du Tour</h2><span class='number'>$dist km</span></div>";

	// Etape la plus longue
	$etLongue = getEtapeLongue();
	$annee = $etLongue['ANNEE'];
	$etape = $etLongue['N_ETAPE'];
	$villeD = $etLongue['VILLE_D'];
	$villeA = $etLongue['VILLE_A'];
	$dist = $etLongue['DISTANCE'];
	$date = $etLongue['DATETAPE'];
	
	echo "<div class='stat'><h2>Étape la plus longue</h2>
	Tour $annee<br>
	étape n°$etape<br>
	$villeD - $villeA<br>
	$date<br>
	<span class='number'>$dist km</span>
	</div>";

	// Etape la plus courte
	$etCourte = getEtapeCourte();
	$annee = $etCourte['ANNEE'];
	$etape = $etCourte['N_ETAPE'];
	$villeD = $etCourte['VILLE_D'];
	$villeA = $etCourte['VILLE_A'];
	$dist = $etCourte['DISTANCE'];
	$date = $etCourte['DATETAPE'];
	
	echo "<div class='stat'><h2>Étape la plus courte</h2>
	Tour $annee<br>
	étape n°$etape<br>
	$villeD - $villeA<br>
	$date<br>
	<span class='number'>$dist km</span>
	</div>";

	// Nombre de coureurs
	$nbC = getNombreCoureurs();
	echo "<div class='stat'><h2>Nombre de coureurs ayant participé</h2><span class='number'>$nbC</span></div>";

	// Nombre d'étapes
	$nbE = getNbEtapes();
	echo "<div class='stat'><h2>Nombre total d'étapes</h2><span class='number'>$nbE</span></div>";
}

function getEtapeLongue(){
	global $conn;
	global $annee;

	$req = "select annee, n_etape, ville_d, ville_a, distance, DATETAPE
	from tdf_etape
	where distance >= all
	(
	select distance
	from tdf_etape
	)";
	LireDonneesPDO1($conn, $req, $donnees);

	return $donnees[0];
}

function getEtapeCourte(){
	global $conn;

	$req = "select annee, n_etape, ville_d, ville_a, distance, DATETAPE
	from tdf_etape
	where n_etape != -1 and distance <= all
	(
		select distance
		from tdf_etape
		where n_etape != -1
	)";
	LireDonneesPDO1($conn, $req, $donnees);

	return $donnees[0];
}

function getDistanceTotale(){
	global $conn;

	$req = "select sum(distance) as dist
	from tdf_etape";
	LireDonneesPDO1($conn, $req, $donnees);

	$dist = floatval($donnees[0]['DIST']);
	$dist = number_format($dist, 0, ',', ' ');
	return $dist;
}

function getNombreCoureurs(){
	global $conn;

	$req = "select count(*) as nb
	from tdf_coureur";
	LireDonneesPDO1($conn, $req, $donnees);

	return $donnees[0]['NB'];
}

function getNbEtapes(){
	global $conn;

	$req = "select count(*) as nb
	from tdf_etape";
	LireDonneesPDO1($conn, $req, $donnees);

	return $donnees[0]['NB'];
}


function afficheTab($donnees, $titres){
	if(!empty($donnees)){
		echo "<table class='tabList'>
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