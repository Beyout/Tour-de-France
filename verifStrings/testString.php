<?php

include("verifStrings.php");

$prenomsCorrects = array(
	"Ébé-ébé",
	"ébé-ébé",
	"ébé-Ébé",
	"éÉé-Ébé",
	"'éÉ'é-É'bé'",
	"DE LA TRUC",
	"ùùùùùùùùùùùùùùùùùùùù",
	"pied-de-biche",
	"A' ' b",
	"A '' b",
	"A'",
	"x",
	"çççç ççç ÇÇÇÇ ÇÇÇ ",
	"àâäéèêëïîôöùûüÿç",
	"ÀÂÄÉÈÊËÏÎÔÖÙÛÜŸÇ"
);

$prenomsConvertibles = array(
	"'éæé-É'bé'",
	"'éæé-É'Ŭé'",
	"-péron-de - la   branche-",
	"bénard     ébert",
	"ÆøœŒøñ",
	"Æ'-'nO",
	"a-a-a--a",
	"a----aaaa----aa-------a"
);

$prenomsInterdits = array(
	"'é !é-É'Ŭé'",
	"éé’’éé--uù  gg",
	"Éééé--gg--gg",
	"DE LA TR€UC",
	"ééééééééééééééééééééééééééééééééééééééééééééééé",
	"Ferdinand--SaintMalo ALAnage",
	"Ferdinand--SaintMalo-ALAnage",
	"aa--bb--cc",
	"'",
	"\a",
	"\\a",
	"b\\a",
	"b\a",
	"-",
	"NULL"
);

$nomsConvertibles = array(
	"Ébé-ébé",
	"ébé-ébé",
	"ébé-Ébé",
	"éÉé-Ébé",
	"'éÉ'é-É'bé'",
	"'éæé-É'bé'",
	"'éæé-É'Ŭé'",
	"DE LA TRUC",
	"ùùùùùùùùùùùùùùùùùùùù",
	"-péron-de - la   branche-",
	"pied-de-biche",
	"Ferdinand--SaintMalo ALAnage",
	"Ferdinand--SaintMalo-ALAnage",
	"A' ' b",
	"A'",
	"x",
	"A '' b",
	"bénard     ébert",
	"ÆøœŒøñ",
	"Æ'-'nO",
	"çççç ççç ÇÇÇÇ ÇÇÇ ",
	"àâäéèêëïîôöùûüÿç",
	"ÀÂÄÉÈÊËÏÎÔÖÙÛÜŸÇ",
	"aa---aa"
);

$nomsInterdits = array(
	"lebocq6",
	"LEBOCQ6",
	"aa--bb--cc",
	"éé’’éé--uù  gg",
	"'",
	"\a",
	"\\a",
	"b\\a",
	"b\a",
	"-",
	"NULL",
	"a----aaaa----aa-------a"
);

echo "<pre>";
echo "Prénoms corrects :<br>";

foreach ($prenomsCorrects as $mot) {
	echo "$mot : " . (verifierPrenom($mot) ? convertirPrenom($mot) : "incorrect");
	echo "<br>";
}

echo "<br><br>Prénoms convertibles :<br><br>";

foreach ($prenomsConvertibles as $mot) {
	echo "$mot : " . (verifierPrenom($mot) ? convertirPrenom($mot) : "incorrect");
	echo "<br>";
}

echo "<br><br>Prénoms incorrects<br><br>";

foreach ($prenomsInterdits as $mot) {
	echo "$mot : " . (verifierPrenom($mot) ? convertirPrenom($mot) : "incorrect");
	echo "<br>";
}



echo "<br><br><br>Noms convertibles :<br><br>";

foreach ($nomsConvertibles as $mot) {
	echo "$mot : " . (verifierNom($mot) ? convertirNom($mot) : "incorrect");
	echo "<br>";
}

echo "<br><br>Noms incorrects<br><br>";

foreach ($nomsInterdits as $mot) {
	echo "$mot : " . (verifierNom($mot) ? convertirNom($mot) : "incorrect");
	echo "<br>";
}

echo "</pre>";

?>