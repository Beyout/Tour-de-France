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
	AffichageClassement($conn, $annee);
}
else {
	echo "
	<div id='erreur'>
		<p>L'année est mauvaise !</p>
	</div>";
}

/**
 * Execute la requête récupérant les infos pour afficher le classement général par année
 * @return classement
 */
function ExecutionClassement($conn, $annee){
	$req_classement = "SELECT dos.N_DOSSARD, cl.NOM, cl.PRENOM, nat.NOM as NOM_PAYS, cl.TEMPS_TOTAL, dos.NOM_SPO, valide
		from tdf_classement cl
		left join TDF_DOSSARD_EQUIPE dos using(n_coureur)
		join tdf_app_nation ap using(n_coureur)
		join tdf_nation nat using(code_cio)
		where cl.annee = :annee and cl.annee = dos.annee
		and ap.annee_debut <= cl.ANNEE and (ap.annee_fin > cl.ANNEE or ap.annee_fin is null)
		order by cl.TEMPS_TOTAL";
	
	$cur = preparerRequetePDO($conn, $req_classement);
	
	$tab = array (
		':annee'=>$annee,
	);
	majDonneesPrepareesTabPDO($cur, $tab);
	LireDonneesPDOPreparee($cur, $donnees);
	return $donnees;
}

/**
 * Affiche le classement général par année
 */
function AffichageClassement($conn, $annee){
	$donnees = ExecutionClassement($conn, $annee);

	echo "<table id='tabResultats' class='tabList'>
		<tr>
			<th><span>Rang</span></th>
			<th><span>Numéro Dossard</span></th>
			<th><span>Nom</span></th>
			<th><span>Prénom</span></th>
			<th><span>Nationalité</span></th>
			<th><span>Temps</span></th>
			<th><span>Nom Sponsor</span></th>
		</tr>";

	foreach($donnees as $compteur => $ligne){
		echo "<tr>";

		if($ligne['VALIDE'] == 'R'){
			$classement = '-';
		}
		else{
			$classement = $compteur + 1;
		}

		echo "<td>" .$classement ."</td>";

		foreach($ligne as $i => $valeur){
			if($i == "TEMPS_TOTAL"){
				$tab = array (
					'heures' => sprintf("%02d", floor($valeur / 3600)),
					'minutes' => sprintf("%02d", floor(($valeur % 3600) / 60)),
					'secondes' => sprintf("%02d", floor($valeur % 60)),
				);

				$valeur = implode(":", $tab);
			}

			if($i != "VALIDE"){
				echo "<td>" .$valeur."</td>";
			}
		}

		echo "</tr>";
	}

	echo "</table>";
}
?>