<?php
include('../../../util/util_chap11.php');
include('../../../util/pdo_oracle.php');
include('../../../util/ajoutdb.php');
include('../../../util/verification.php');

$login = 'pphp2a_09';
$mdp = 'Poupou09';
$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";

$conn = OuvrirConnexionPDO($db, $login,$mdp);

AffichagePaysParticipants($conn);

function ExecutionPaysParticipants($conn){
	$req = "select distinct nom, count(*) as participations
	from tdf_app_nation
	join tdf_nation using(code_cio)
	group by code_cio, nom
	order by nom";

	LireDonneesPDO1($conn, $req, $donnees);

	return $donnees;
}

function AffichagePaysParticipants($conn){
	$donnees = ExecutionPaysParticipants($conn);

	echo "<table id='tabResultats' class='tabList'>
		<tr>
			<th><span>Nationnalité des coureurs</span></th>
			<th><span>Coureurs ayant participés</span></th>
		</tr>";

	foreach($donnees as $ligne){
		echo "<tr>";

		foreach($ligne as $valeur){
			echo "<td>" .$valeur."</td>";
		}

		echo "</tr>";
	}

	echo "</table>";
}
?>