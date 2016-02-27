<?php

/**
 * Fonction d'autoload
 *
 * @portée	publique
 * @retour	vide
 */
function autochargement_classes($nom_de_classe) {
	$chemin = _MEDIT_RACINE."/classes/";
	$prefixe = strtolower(substr($nom_de_classe, 0, 6));
	if (strcmp($prefixe, "medit_")) {
		die("Erreur : la classe ".$nom_de_classe." porte un préfixe incorrect");
	}
	$nom_sans_prefixe = substr($nom_de_classe, 6);
	$categorie = substr($nom_sans_prefixe, 0, 2);
	if (!(strcmp($categorie, "db"))) {$chemin .= "db/";}
	elseif (!(strcmp($categorie, "ht"))) {$chemin .= "html/";}
	elseif (!(strcmp($categorie, "uc"))) {$chemin .= "uc/";}
	$classe = $chemin.$nom_sans_prefixe.".php";
    if (@file_exists($classe)) {
        @require_once($classe);
    }
	else {
		die("Impossible de charger la classe ".$nom_de_classe);
	}
}

// Activation de l'autoload
spl_autoload_register("autochargement_classes");