<?php
include('../../../util/util_chap11.php');
include('../../../util/pdo_oracle.php');
include('../../../util/ajoutdb.php');
include('../../../util/verification.php');

$login = 'pphp2a_09';
$mdp = 'Poupou09';
$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";

$conn = OuvrirConnexionPDO($db, $login,$mdp);

$annee = $_GET['annee'];

if(VérificationAnnéeClassement($conn, $annee)){
	AffichageParticipants($conn, $annee);
}
else {
	echo "
	<div id='erreur'>
		<p>L'année est mauvaise !</p>
	</div>";
}

function ExecutionParticipants($conn, $annee){
	$req = "SELECT N_DOSSARD, cou.NOM, cou.PRENOM, nat.NOM as NOM_PAYS, NOM_SPO
		from TDF_DOSSARD_EQUIPE dos
		join tdf_coureur cou using(n_coureur)
		join tdf_app_nation using(n_coureur)
		join tdf_nation nat using(code_cio)
		where dos.annee = :annee
		and annee_debut <= annee and (annee_fin > annee or annee_fin is null)
		order by n_dossard";

	$cur = preparerRequetePDO($conn, $req);
	
	$tab = array (
		':annee'=>$annee
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);

	return $donnees;
}

function AffichageParticipants($conn, $annee){
	$donnees = ExecutionParticipants($conn, $annee);

	echo "<table id='tabResultats' class='tabList'>
		<tr>
			<th><span>Numéro Dossard</span></th>
			<th><span>Nom</span></th>
			<th><span>Prénom</span></th>
			<th><span>Nationalité</span></th>
			<th><span>Nom Sponsor</span></th>
		</tr>";

	foreach($donnees as $compteur => $ligne){
		echo "<tr>";

		foreach($ligne as $valeur){
			echo "<td>" .$valeur."</td>";
		}

		echo "</tr>";
	}

	echo "</table>";
}
?>