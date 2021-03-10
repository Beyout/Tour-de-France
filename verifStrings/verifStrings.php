<?php

function verifierPrenom($prenom) {
	if (!contientCaraValides($prenom)) {
		return false;
	}

	if (contientCaraInterdits($prenom)) {
		return false;
	}

	if (estInterdit($prenom)){
		return false;
	}

	if (contientDoubleTirets(trimPrenom($prenom))) {
		return false;
	}

	if (prenomTropLong($prenom))
		return false;

	return true;
}

function contientCaraValides($prenom) {
	//Regex pour toutes lettres (accents compris), tiret, backquote, apostrophe, espace (+ insécable)
	$pattern = "/^[\p{L}-'`  ]*$/u";

	return preg_match($pattern, $prenom);
}

function contientCaraInterdits($mot) {
	$pattern = "/\"|(^'$)/";

	return preg_match($pattern, $mot);
}

function contientDoubleTirets($prenom) {
	$pattern = "/(\-\-)+/";

	return preg_match($pattern, $prenom);
}

function prenomTropLong($prenom) {
	$long = mb_strlen($prenom);

	return $long > 30;
}

function nomTropLong($nom) {
	$long = mb_strlen($nom);

	return $long > 35;
}

function convertirPrenom($prenom) {
	$prenom = remplacerAccentsBizarres($prenom);

	$prenom = trimPrenom($prenom);

	$prenom = majPrenom($prenom);

	return $prenom;
}

function remplacerAccentsBizarres($str) {
	$str = preg_replace("/`/", "'", $str);
	$str = preg_replace("/æ/", "ae", $str);
	$str = preg_replace("/œ/", "oe", $str);
	$str = preg_replace("/Æ/", "AE", $str);
	$str = preg_replace("/Œ/", "OE", $str);
	$str = preg_replace("/ø/", "o", $str);
	$str = preg_replace("/Ø/", "O", $str);
	$str = preg_replace("/ñ/", "n", $str);
	$str = preg_replace("/ŭ/", "u", $str);
	$str = preg_replace("/Ŭ/", "U", $str);

	return $str;
}

function trimEspacesTirets($str) {
	// tirets au début et à la fin
	$str = preg_replace("/(^\-)|(\-$)/", "", $str);

	// espaces autour des tirets
	$str = preg_replace("/( +\- *)|( *\- +)/", "-", $str);

	// espaces trop nombreux
	$str = preg_replace("/  +/", " ", $str);

	return $str;
}

function trimNom($str) {
	$str = trimEspacesTirets($str);

	$str = preg_replace("/---+/", "--", $str);

	return $str;
}

function trimPrenom($str) {
	$str = trimEspacesTirets($str);

	$str = preg_replace("/--+/", "-", $str);

	return $str;
}

function majPrenom($prenom) {
	// Normalement c'est facile avec mb_convert_case($str, MB_CASE_TITLE)
	// Mais ça met pas de maj si y'a un ' au début du mot :(

	$nouv = "";
	$last = ' ';

	// Découpage des caractères du string
	$strArr = preg_split('//u', $prenom, null, PREG_SPLIT_NO_EMPTY);
	foreach ($strArr as $char) {
		// Si le caractère d'avant est pas une lettre -> maj
		if (preg_match("/[-'`  ]/", $last)) {
			$nouv .= mb_strtoupper($char);
		}
		else {
			$nouv .= mb_strtolower($char);
		}

		$last = $char;
	}

	return $nouv;
}

function verifierNom($nom) {
	if (!contientCaraValides($nom)) {
		return false;
	}

	if (contientCaraInterdits($nom)) {
		return false;
	}

	if (estInterdit($nom)) {
		return false;
	}
	
	if (tropDeTirets(trimNom($nom))) {
		return false;
	}
	
	if (nomTropLong($nom)) {
		return false;
	}

	return true;
}

function tropDeTirets($nom) {
	return preg_match("/(--)[^\-]+(--)/", $nom);
}

function estInterdit($mot) {
	if ($mot == "-")
		return true;
	
	if (mb_strtolower($mot) == "null")
		return true;
	
	return false;
}

function convertirNom($nom) {
	$nom = remplacerAccentsBizarres($nom);
	$nom = strtr(utf8_decode($nom), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝŸ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUYY');
	$nom = mb_strtoupper($nom);
	$nom = trimNom($nom);

	return $nom;
}

?>