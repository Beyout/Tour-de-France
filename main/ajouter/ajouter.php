<?php
include("../../util/util_chap11.php");
include("../../util/util_chap9.php");
include("../../util/pdo_oracle.php");
include("../../util/verification.php");
include("../../util/ajoutdb.php");
include("../../verifStrings/verifStrings.php");

$erreur = true;
$sans_annee = false;

$erreur = false;

$conn = OuvrirConnexionPDO('oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8', 'pphp2a_09', 'Poupou09');

$_POST['cou_nom'] = $_GET['nom'];
$_POST['cou_prenom'] = $_GET['prenom'];
$_POST['cou_annee_naissance'] = $_GET['annee_n'];
$_POST['cou_annee_prem'] = $_GET['annee_prem'];
$_POST['cou_pays'] = $_GET['pays'];
$_POST['cou_annee_debut'] = $_GET['debut'];

VerificationChampsAvecAnnee($conn, $erreur, $sans_annee, $nom, $prenom, $annee_n, $annee_prem, $pays, $annee_debut);

if(!$erreur){
	if(VérificationCoureurDB($conn, $nom, $prenom)){
		if(!$sans_annee){
			AjouterCoureurAvecAnnees($conn, $nom, $prenom, $annee_n, $annee_prem, $pays, $annee_debut);
			$coureur = "$nom $prenom $annee_n $annee_prem $pays $annee_debut";
			// echo "<br>$coureur";
		}
		else{
			AjouterCoureurSansAnnees($conn, $nom, $prenom, $pays);
			$coureur = "$nom $prenom $pays";
			// echo "<br>$coureur";
		}
		echo "Coureur ajouté";
	}
	else{
		echo "Coureur déjà présent";
	}
}
?>