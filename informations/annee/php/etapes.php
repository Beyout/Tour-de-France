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
	AffichageEtapesGagnants($conn, $annee);
}
else {
	echo "
	<div id='erreur'>
		<p>L'année est mauvaise !</p>
	</div>";
}

function ExecutionEtapesGagnants($conn, $annee){
	// Catégorie commune

	$req = "SELECT  n_etape, VILLE_D, VILLE_A, to_char(DATETAPE,'dd/mm'), DISTANCE, to_char(dos.N_DOSSARD), to_char(cou.NOM) as NOM, to_char(cou.PRENOM) as PRENOM, to_char(nat.NOM) as NOM_PAYS, NOM_SPO, CAT_CODE
	from TDF_ETAPE et
	join TDF_TEMPS using(n_etape, annee)
	join TDF_DOSSARD_EQUIPE dos using(n_coureur, annee)
	join TDF_COUREUR cou using(n_coureur)
	join TDF_APP_NATION using(n_coureur)
	join TDF_NATION nat using(code_cio)
	where annee = :annee and rang_arrivee = 1 and cat_code not like 'CME'
	and annee_debut <= annee and (annee_fin > annee or annee_fin is null)
	union
	SELECT distinct  n_etape, VILLE_D, VILLE_A, to_char(DATETAPE,'dd/mm'), DISTANCE, '-', '-', '-', '-', NOM_SPO, CAT_CODE
	from TDF_ETAPE et
	join TDF_TEMPS using(n_etape, annee)
	join TDF_DOSSARD_EQUIPE dos using(n_coureur, annee)
	join TDF_COUREUR cou using(n_coureur)
	join TDF_APP_NATION using(n_coureur)
	join TDF_NATION nat using(code_cio)
	where annee = :annee and rang_arrivee = 1 and cat_code like 'CME'
	and annee_debut <= annee and (annee_fin > annee or annee_fin is null)
	order by n_etape";

	$cur = preparerRequetePDO($conn, $req);
	
	$tab = array (
		':annee'=>$annee
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);

	return $donnees;
}

function AffichageEtapesGagnants($conn, $annee){
	$donnees = ExecutionEtapesGagnants($conn, $annee);

	echo "<table id='tabResultats' class='tabList'>
		<tr>
			<th><span>Epreuve</span></th>
			<th><span>Départ</span></th>
			<th><span>Arrivée</span></th>
			<th><span>Date</span></th>
			<th><span>Distance (km)</span></th>
			<th><span>Numéro Dossard</span></th>
			<th><span>Nom</span></th>
			<th><span>Prénom</span></th>
			<th><span>Nationalité</span></th>
			<th><span>Nom Sponsor</span></th>
			<th><span>Catégorie</span></th>
		</tr>";

	foreach($donnees as $ligne){
		echo "<tr>";

		if($ligne['CAT_CODE'] == "CME"){
			$ligne['NOM'] = '-';
			$ligne['PRENOM'] = '-';
			$ligne['NOM_PAYS'] = '-';
		}
		foreach($ligne as $valeur){
			echo "<td>" .$valeur."</td>";
		}

		echo "</tr>";
	}

	echo "</table>";
}
?>