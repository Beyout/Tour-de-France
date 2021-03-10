<?php
include("../../util/util_chap11.php");
include("../../util/util_chap9.php");
include("../../util/pdo_oracle.php");
include("../../util/verification.php");
include("../../util/ajoutdb.php");
include("../../verifStrings/verifStrings.php");

$conn = OuvrirConnexionPDO('oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8', 'pphp2a_09', 'Poupou09');

$n_coureur = GetNbCoureurSpecifique($conn, $_GET['nom'], $_GET['prenom']);
if(VérificationParticipationsTDF($conn, $n_coureur)){
	echo "Impossible de supprimer ce coureur";
}
else{
	SuppressionCoureur($conn, $n_coureur);
	echo "Coureur supprimé !";
}

function SuppressionCoureur($conn, $n_coureur){
	// Suppression dans tdf_app_nation
	$req = "delete from tdf_app_nation where n_coureur = " .$n_coureur;
	majDonneesPDO($conn, $req);

	$req = "delete from tdf_coureur where n_coureur = " .$n_coureur;
	majDonneesPDO($conn, $req);

	Commit($conn);
}
?>