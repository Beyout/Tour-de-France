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
	AffichageAbandons($conn, $annee);
}
else {
	echo "
	<div id='erreur'>
		<p>L'année est mauvaise !</p>
	</div>";
}

function ExecutionAbandons($conn, $annee){
	$req = "SELECT distinct n_etape, N_DOSSARD, NOM, PRENOM, NOM_PAYS, NOM_SPO, typ.libelle, ab.commentaire
	from TDF_DOSSARD_EQUIPE dos
	join TDF_NATIO nat using(n_coureur, annee)
	join TDF_ABANDON ab using(n_coureur, annee)
	join TDF_TYPEABAN typ using(c_typeaban)
	where annee = :annee and annee_debut <= annee and (annee_fin > annee or annee_fin is null) and n_coureur in
	(
		select n_coureur from TDF_ABANDON
	)
	order by n_etape, n_dossard";

	$cur = preparerRequetePDO($conn, $req);
	
	$tab = array (
		':annee'=>$annee
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);

	return $donnees;
}

function AffichageAbandons($conn, $annee){
	$donnees = ExecutionAbandons($conn, $annee);

	echo "<table id='tabResultats' class='tabList'>
		<tr>
			<th><span>Numéro Etape</span></th>
			<th><span>Numéro Dossard</span></th>
			<th><span>Nom</span></th>
			<th><span>Prénom</span></th>
			<th><span>Nationalité</span></th>
			<th><span>Nom Sponsor</span></th>
			<th><span>Type abandon</span></th>
			<th><span>Commentaire</span></th>
		</tr>";

	foreach($donnees as $ligne){
		echo "<tr>";

		foreach($ligne as $valeur){
			if($valeur == "NULL")
				$valeur = "";
			echo "<td>" .$valeur."</td>";
		}

		echo "</tr>";
	}

	echo "</table>";
}
?>