<?php
/**
 * Créer toutes les vues
 */
function initViews(){
	$login = '';
	$mdp = '';
	$db = "";

	$conn = OuvrirConnexionPDO($db, $login,$mdp);

	dossardEquipe($conn);
	classement($conn);
	natio($conn);
	villeD($conn);
	villeA($conn);
}

/**
 * Créer la vue TDF_DOSSARD_EQUIPE qui stocke le numéro identifiant, numéro de dossard et le nom d'équipe des coureurs et les années pour le tri
 */
function dossardEquipe($conn){
	$view_dossard_equipe = "CREATE OR REPLACE VIEW TDF_DOSSARD_EQUIPE AS
	SELECT N_COUREUR, pa.N_DOSSARD, spon.NOM as NOM_SPO, ANNEE
	from TDF_PARTI_COUREUR pa
	join TDF_SPONSOR spon on pa.N_EQUIPE = spon.N_EQUIPE and pa.N_SPONSOR = spon.N_SPONSOR";
	majDonneesPDO($conn, $view_dossard_equipe);
	Commit($conn);
}

/**
 * Créer la vue TDF_CLASSEMENT qui stocke le numéro, nom, prénom, temps total de tous les coureurs et les années pour le tri
 */
function classement($conn){
	$view_classement = "CREATE OR REPLACE VIEW TDF_CLASSEMENT AS
		select annee, n_coureur, nom, prenom,TEMPS_TOTAL, valide from 
		(
			select annee, n_coureur, nom, prenom, sum(total_seconde) +nvl(difference,0) as TEMPS_TOTAL, valide
			from tdf_coureur
			join tdf_temps using(n_coureur)
			join tdf_parti_coureur using(n_coureur, annee)
			left join tdf_temps_difference using(n_coureur,annee)
			where (n_coureur,annee) not in
			(
				select n_coureur,annee from tdf_abandon
			)
			and valide='O'
			group by annee,n_coureur, nom, prenom , difference, valide
			union
			select annee, n_coureur, substr(nom,1,2)||'----', substr(prenom,1,2)||'----', sum(total_seconde) +nvl(difference,0) as TEMPS_TOTAL, valide
			from tdf_coureur
			join tdf_temps using(n_coureur)
			join tdf_parti_coureur using(n_coureur, annee)
			left join tdf_temps_difference using(n_coureur,annee)
			where (n_coureur,annee) not in
			(
				select n_coureur,annee from tdf_abandon
			)
			and valide='R'
			group by annee, n_coureur, nom, prenom , difference, valide
			order by TEMPS_TOTAL
		)";
	majDonneesPDO($conn, $view_classement);
	Commit($conn);
}

/**
 * Créer la vue TDF_NATIO qui stocke le numéro, nom, prenom et nationnalité d'un coureur et l'année
 */
function natio($conn){
	$view_nation = "CREATE OR REPLACE VIEW TDF_NATIO AS
	SELECT N_COUREUR, ANNEE, ANNEE_DEBUT, ANNEE_FIN, cou.NOM, PRENOM, nat.NOM as NOM_PAYS
	from TDF_PARTI_COUREUR
	join tdf_coureur cou using(n_coureur)
	join tdf_app_nation using (n_coureur)
	join tdf_nation nat using(code_cio)";
	majDonneesPDO($conn, $view_nation);
	Commit($conn);
}

/**
 * Créer la vue TDF_VILLE_D qui stocke la ville de départ et l'année de visite
 */
function villeD($conn){
	$view_ville_d = "CREATE OR REPLACE VIEW TDF_VILLE_D AS
		SELECT VILLE_D as ville, ANNEE
		from TDF_ETAPE
		order by ville_d, annee";
	majDonneesPDO($conn, $view_ville_d);
	Commit($conn);
}

/**
 * Créer la vue TDF_VILLE_A qui stocke la ville d'arrivée et l'année de visite
 */
function villeA($conn){
	$view_ville_a = "CREATE OR REPLACE VIEW TDF_VILLE_A AS
		SELECT VILLE_A as ville, ANNEE
		from TDF_ETAPE
		order by ville_a, annee";
	majDonneesPDO($conn, $view_ville_a);
	Commit($conn);
}
?>