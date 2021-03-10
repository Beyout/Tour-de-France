<?php
include('../../../util/util_chap11.php');
include('../../../util/pdo_oracle.php');
include('../../../util/ajoutdb.php');
include('../../../util/verification.php');

$login = 'pphp2a_09';
$mdp = 'Poupou09';
$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";

$conn = OuvrirConnexionPDO($db, $login,$mdp);

if(!empty($_GET['ville'])){
	AffichageVilles($_GET['ville']);
}
else{
	AffichageVilles('%');
}

function ExecutionVillesDebut($ville){
	global $conn;

	$req = "SELECT ville, annee
	from tdf_ville_d
	where trim(lower(ville)) like trim(lower(:ville)) || '%'
	union
	select ville, annee
	from tdf_ville_a
	where trim(lower(ville)) like trim(lower(:ville)) || '%'
	order by ville, annee";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':ville'=>$ville,
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);

	return $donnees;
}

function ExecutionVillesContient($ville){
	global $conn;

	$req = "SELECT ville, annee
	from tdf_ville_d
	where trim(lower(ville)) like '%' || trim(lower(:ville)) || '%'
	minus
	SELECT ville, annee
	from tdf_ville_d
	where trim(lower(ville)) like trim(lower(:ville)) || '%'
	union
	select ville, annee
	from tdf_ville_a
	where trim(lower(ville)) like '%' || trim(lower(:ville)) || '%'
	minus
	select ville, annee
	from tdf_ville_a
	where trim(lower(ville)) like trim(lower(:ville)) || '%'
	order by ville, annee";
	$cur = preparerRequetePDO($conn, $req);

	$tab = array (
		':ville'=>$ville,
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);

	return $donnees;
}

function AffichageVilles($ville){
	echo "<table id='tabResultats' class='tabList'>
		<tr>
			<th><span>Ville Départ</span></th>
			<th><span>Nombre de visites</span></th>
			<th><span>Années de visites</span></th>
		</tr>";

	$donnees = ExecutionVillesDebut($ville);
	afficheTab($donnees);
	$donnees = ExecutionVillesContient($ville);
	afficheTab($donnees);
	echo "</table>";
}

function afficheTab($donnees){
	if(!empty($donnees)){
		$ancienneVille = $donnees[0]['VILLE'];
		$compteur = 0;
		$annees = '';
		echo "<tr>";
		echo "<td>" .$ancienneVille ."</td>";
	
		foreach($donnees as $ligne){
			$nouvelleVille = $ligne['VILLE'];
	
			if($nouvelleVille == $ancienneVille){
				$compteur++;
			}
			else {
				echo "<td>" .$compteur ."</td>";
				echo "<td>" .$annees ."</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>" .$ligne['VILLE'] ."</td>";
				
				$compteur = 1;
				$annees = '';
			} 

			$annees = $annees .' ' .(string) $ligne['ANNEE'];
			$ancienneVille = $nouvelleVille;
		}
	
		echo "<td>" .$compteur ."</td>";
		echo "<td>" .$annees ."</td>";
		echo "</tr>";
	}
}
?>