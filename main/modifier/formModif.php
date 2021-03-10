<?php
include("../../util/util_chap11.php");
include("../../util/pdo_oracle.php");
include("../../util/verification.php");
include("../../util/ajoutdb.php");
include("../../util/modifier_fonc.php");
include("../../util/nation.php");
include("../../verifStrings/verifStrings.php");

$login = 'pphp2a_09';
$mdp = 'Poupou09';
$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";

$conn = OuvrirConnexionPDO($db, $login,$mdp);

// Envoi du récapitulatif et du formulaire
$ancienNom = $_GET['nom'];
$ancienPrenom = $_GET['prenom'];
$ancienPays = $_GET['pays'];

GetInfosCoureur($conn, $ancienNom, $ancienPrenom, $annee_n, $annee_prem, $ancienPays, $annee_debut);

$form1 =
"
<div id='formModif' name='form_modif'>
	<div class='rel'>
		<h2>Modifications</h2>
		<label for='nom_modif'>Nom : </label>
		<input type='text' name='cou_nom_modif' id='nomModif' value=\"$ancienNom\" required>
		<label for='prenom'>Prénom : </label>
		<input type='text' name='cou_prenom_modif' id='prenomModif' value=\"$ancienPrenom\" required>
		<label for='annee_naissance_modif'>Année de naissance : </label>
		<input type='text' name='cou_annee_naissance_modif' id='naissanceModif' value='$annee_n' maxlength='4' size='2' class='annee'>
		<label for='annee_prem_modif'>Année de première participation : </label>
		<input type='text' name='cou_annee_prem_modif' id='premModif' value='$annee_prem' maxlength='4' size='2' class='annee'>
		<label>Nationalité</label>
";

$ancienNom = preg_replace("/'/", "+", $ancienNom);
$ancienPrenom = preg_replace("/'/", "+", $ancienPrenom);

$form2 =
"
		<label for='add_annee_debut'>Année de début de nationalité</label>
		<input type='text' name='cou_annee_debut' id='debutModif' value='$annee_debut' maxlength='4' size='4'>
		<input type='radio' id='radioCorrec' name='motif' value='correction' class='popupInline' checked>
		<label for='radioCorrec' class='popupInline'>Correction</label>
		<p>Correction de la nationalité</p>
		<input type='radio' id='radioChange' name='motif' value='changement' class='popupInline'>
		<label for='radioChange' class='popupInline'>Changement</label>
		<p>Ajout d'une nationalité</p>
		<div class='centerFlex'>
			<div class='popupConfirm' onclick='modifierForm(\"$ancienNom\", \"$ancienPrenom\", \"$ancienPays\");'>Modifier</div>
		</div>
		<div class='fermeButt' onclick='montrerModif(false)'>X</div>
		<div id='modifResult'></div>
	</div>
</div>
";

echo $form1;
InsertionMenuDeroulantPaysModif($conn, $ancienPays);
echo $form2;
?>