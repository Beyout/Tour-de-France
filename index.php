<!DOCTYPE html>
<html lang="fr">

	<head>
		<meta charset="UTF-8" />
		<link rel="stylesheet" href="styles/recherche.css?v=1.1"/>
		<link rel="stylesheet" href="styles/navbar.css?v=1.1"/>
		<link rel="stylesheet" href="styles/liste.css?v=1.1"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<title>Tour de France</title>
		<link rel="icon" href="main/tdf.png">
		<script type="text/javascript" src="main/recherche/afficher.js"></script>
		<script type="text/javascript" src="main/ajouter/ajouter.js"></script>
		<script type="text/javascript" src="main/modifier/modifier.js"></script>
		<script type="text/javascript" src="main/supprimer/supprimer.js"></script>
		<script type="text/javascript" src="main/palmares/palmares.js"></script>
	</head>

	<?php
	include("util/util_chap11.php");
	include("util/util_chap9.php");
	include("util/pdo_oracle.php");
	include("util/nation.php");
	include("util/verification.php");
	include("util/init_views.php");
	include("util/ajoutdb.php");

	initViews();

	$login = 'pphp2a_09';
	$mdp = 'Poupou09';
	$db = "oci:dbname=kiutoracle18.unicaen.fr:1521/info.kiutoracle18.unicaen.fr;charset=AL32UTF8";
	$conn = OuvrirConnexionPDO($db, $login,$mdp);

	include("main/recherche/pageRecherche.html");
	?>

</html>