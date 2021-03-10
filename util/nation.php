<?php
/**
 * Insère un menu déroulant contenant les pays présents dans la bdd.
 */
function InsertionMenuDeroulantPays($conn){
	$req = "select nom from tdf_nation order by nom";
	LireDonneesPDO1($conn, $req, $donnees);

	echo "<select name='cou_pays' id='addPays'>";
	echo "	<option value='' selected disabled>-- Sélectionnez une option --</option>\n";
	for($i = 0; $i < count($donnees); $i++){
		echo "	<option value='" .strtolower($donnees[$i]['NOM']) ."'>" .$donnees[$i]['NOM'] ."</option>\n";
	}
	echo "</select>";
}
function InsertionMenuDeroulantPaysModif($conn, $pays){
	$req = "select nom from tdf_nation order by nom";
	LireDonneesPDO1($conn, $req, $donnees);

	echo "<select name='cou_pays' id='modifPays'>";
	for($i = 0; $i < count($donnees); $i++){
		if($donnees[$i]['NOM'] == $pays){
			echo "	<option value='" .strtolower($donnees[$i]['NOM']) ."' selected>" .$donnees[$i]['NOM'] ."</option>\n";
		}
		echo "	<option value='" .strtolower($donnees[$i]['NOM']) ."'>" .$donnees[$i]['NOM'] ."</option>\n";
	}
	echo "</select>";
}
?>