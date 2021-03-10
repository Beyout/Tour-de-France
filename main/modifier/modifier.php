<?php
include("../../util/util_chap11.php");
include("../../util/pdo_oracle.php");
include("../../util/verification.php");
include("../../util/ajoutdb.php");
include("../../util/modifier_fonc.php");
include("../../verifStrings/verifStrings.php");

$login = 'pphp2a_09';
$mdp = 'Poupou09';
$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";

$conn = OuvrirConnexionPDO($db, $login,$mdp);

$_POST['cou_nom'] = $_GET['nom'];
$_POST['cou_prenom'] = $_GET['prenom'];
$_POST['cou_annee_naissance'] = $_GET['naiss'];
$_POST['cou_annee_prem'] = $_GET['anPrem'];
$_POST['cou_pays'] = $_GET['pays'];
$_POST['cou_annee_debut'] = $_GET['debut'];
$ancienNom = $_GET['ancienNom'];
$ancienPrenom = $_GET['ancienPrenom'];
$ancienPays = $_GET['ancienPays'];

// echo "<pre>" .print_r($_GET) ."</pre>";

$erreur = false;
VerificationChampsAvecAnneeModif($erreur, $sans_annee, $nom, $prenom, $annee_n, $annee_prem, $pays, $annee_debut);
if(!$erreur){ 
	if(VérificationCoureurDBModif($conn, $nom, $prenom, $ancienNom, $ancienPrenom)){ //si il n'y a pas de coureur avec ces infos sur les nouvelles données
		// echo "nomM : $nom prenomM : $prenom annee_n : $annee_n annee_prem : $annee_prem  pays : $pays<br>";

		if($_GET['motif'] == "correction"){
			UpdateCoureurCorrection($conn, $nom, $prenom, $ancienNom, $ancienPrenom, $annee_n, $annee_prem, $pays, $ancienPays, $annee_debut);
			echo "Correction effectuée";
		}
		else if($_GET['motif'] == "changement"){
			if(strtoupper($ancienPays) == strtoupper($pays)){
				$erreur = true;
				// TODO (même pays)
			}
			else {
				UpdateCoureurChangement($conn, $nom, $prenom, $ancienNom, $ancienPrenom, $annee_n, $annee_prem, $pays, $ancienPays, $annee_debut);
				echo "Changement effectué";
			}
		}
	}
	else {
		echo "Un coureur a déjà ce nom";
	}
}
?>