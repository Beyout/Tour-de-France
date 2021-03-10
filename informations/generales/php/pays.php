<?php
include('../../../util/util_chap11.php');
include('../../../util/pdo_oracle.php');
include('../../../util/ajoutdb.php');
include('../../../util/verification.php');

$login = 'pphp2a_09';
$mdp = 'Poupou09';
$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";

$conn = OuvrirConnexionPDO($db, $login,$mdp);

AffichagePays($conn);

function ExecutionPays($conn){
	$req = "select distinct nat.nom, annee
		from tdf_nation nat
		join tdf_etape et on nat.code_cio = et.code_cio_d or nat.code_cio = et.code_cio_a
		where nat.code_cio = code_cio_d or nat.code_cio = code_cio_a
		group by nom, annee
		order by nat.nom, annee";

	LireDonneesPDO1($conn, $req, $donnees);

	return $donnees;
}

function AffichagePays($conn){
	$donnees = ExecutionPays($conn);

	echo "<table id='tabResultats' class='tabList'>
		<tr>
			<th><span>Pays</span></th>
			<th><span>Nombre de participations</span></th>
			<th><span>Années de participations</span></th>
		</tr>";

	$ancienPays = $donnees[0]['NOM'];
	$compteur = 0;
	$annees = '';
	echo "<tr>";
	echo "<td>" .$donnees[0]['NOM'] ."</td>";

	foreach($donnees as $ligne){
		$nouveauPays = $ligne['NOM'];

		if($nouveauPays == $ancienPays){
			$compteur++;
		}
		else {
			if($ancienPays == 'FRANCE'){
				echo "<td>-</td>";
				echo "<td>Toutes</td>";
			}
			else{
				echo "<td>" .$compteur ."</td>";
				echo "<td>" .$annees ."</td>";
			}
			echo "</tr>";
			echo "<tr>";
			echo "<td>" .$ligne['NOM'] ."</td>";
			
			$compteur = 1;
			$annees = '';
		} 

		$annees = $annees .' ' .(string) $ligne['ANNEE'];
		$ancienPays = $nouveauPays;
	}

	echo "<td>" .$compteur ."</td>";
	echo "<td>" .$annees ."</td>";
	echo "</tr>";
	echo "</table>";
}
?>