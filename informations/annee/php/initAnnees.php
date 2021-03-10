<?php
include('../../../util/pdo_oracle.php');

$login = 'pphp2a_09';
$mdp = 'Poupou09';
$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";


$conn = OuvrirConnexionPDO($db, $login,$mdp);

$req = "SELECT annee FROM TDF_ANNEE order by annee desc";

LireDonneesPDO3($conn, $req, $donnee);

foreach($donnee as $ligne) {
	$annee = $ligne['ANNEE'];
	echo "<option value='" . $annee . "'>" . $annee . "</option>";
}

?>