<?php

/**
 * Préfixage des noms de table
 *
 * @portée	publique
 * @param	nom de la table
 * @retour	nom de la table préfixé
 */
function table($nom) {
	$table = "medit_".medit_config::DB_PREFIXE_TABLE.$nom;
	return $table;
}

/**
 * Distance entre un tableau de référence et un autre tableau
 * Ces deux tableaux doivent être préalablement triés
 *
 * @portée	publique
 * @param	tableau de référence
 * @param	tableau à comparer
 * @retour	distance
 */
function calculer_distance_tableaux($tableau_reference, $tableau_distant) {
	if ($tableau_reference == $tableau_distant) {return 0;}
	$nb_reference = count($tableau_reference);
	$nb_distant = count($tableau_distant);
	if ($nb_distant > $nb_reference) {
		$diff = array_diff($tableau_reference, $tableau_distant);
		$nb_diff = count($diff);
		if ($nb_diff == 0) {
			return ($nb_distant - $nb_reference);
		}
		else if ($nb_diff == $nb_reference) {
			return 99;
		}
		else {
			return (-$nb_diff);
		}
	}
	else {
		$intersect = array_intersect($tableau_reference, $tableau_distant);
		$nb_intersect = count($intersect);
		if ($nb_intersect == 0) {
			return -99;
		}
		else {
			return $nb_intersect - $nb_reference;
		}
	}
}

/**
 * Formattage d'un horodatage en fonction de son ancienneté
 *
 * @portée	publique
 * @param	horodatage sous forme de timestamp
 * @retour	horodatage sous forme de chaine de caractères
 */
function formatter_horodatage($horodatage) {
	$date_du_jour = date(medit_config::LANG_FORMAT_DATE);
	$date_horodatage = date(medit_config::LANG_FORMAT_DATE, $horodatage);
	if (strcmp($date_du_jour, $date_horodatage)) {
		$heure_horodatage = $date_horodatage." ".date(medit_config::LANG_FORMAT_HEURE, $horodatage);
	}
	else {
		$heure_horodatage = date(medit_config::LANG_FORMAT_HEURE_LONGUE, $horodatage);
	}
	return $heure_horodatage;
}

/**
 * Renvoi d'un tableau contenant les éléments du DOM recherchés
 * dans un texte HTML
 *
 * @portée	publique
 * @param	texte HTML
 * @param	tableau des balises recherchées
 * @retour	liste des balises trouvées (balise/texte)
 */
function decomposer_html($texte, $liste_recherche) {
	$dom_texte = new DOMDocument();
	
	// Chargement avec erreurs désactivées pour ne pas perturber le flux de sortie
	libxml_use_internal_errors(true);
	@$dom_texte->loadHTML("<?xml encoding=\"UTF-8\">".$texte);
	libxml_clear_errors();
	$dom_texte->encoding = "UTF-8";
	$liste_balises = array();
	parcourir_dom($dom_texte, $liste_recherche, $liste_balises);
	return $liste_balises;
}

/**
 * Fonction de service pour decomposer_dom (récursive)
 *
 * @portée	publique
 * @param	noeud DOM
 * @param	tableau des balises recherchées
 * @param	tableau des balises trouvées (par référence)
 * @retour	vide
 */
function parcourir_dom($dom_node, $liste_recherche, &$liste_balises) {
    foreach ($dom_node->childNodes as $node) {
		$balise = $node->nodeName;
		if (in_array($balise, $liste_recherche)) {
			$texte = innerHTML($node);
			if (strlen($texte) > 0) {
				$avec_id = $node->hasAttribute("id"); 
				$id = ($avec_id)?$node->getAttribute("id"):null; 
				$avec_classe = $node->hasAttribute("class"); 
				$classe = ($avec_classe)?$node->getAttribute("class"):null; 
				$liste_balises[] = array("balise" => $balise, "avec_id" => $avec_id, "id" => $id, "avec_classe" => $avec_classe, "classe" => $classe, "texte" => $texte);
			}
		}
		// Appel récursif pour descendre dans le DOM
        elseif ($node->hasChildNodes()) {
            parcourir_dom($node, $liste_recherche, $liste_balises);
        }
    }    
}

/**
 * Fonction de service pour parcourir_dom
 *
 * @portée	publique
 * @param	noeud DOM
 * @retour	innerHTML
 */
function innerHTML(DOMNode $element) { 
    $innerHTML = ""; 
    $children  = $element->childNodes;
    foreach ($children as $child)  { 
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }
    return $innerHTML; 
}

/**
 * Fonction supprimant le span des annotations dans un code HTML
 *
 * @portée	publique
 * @param	HTML avec annotations
 * @retour	HTML sans annotations
 */
function retirer_annotations($html) {
	// TODO : Pour le moment on n'accepte pas les <a> (voir aussi config-edition.js)
	$ret = strip_tags($html, "<p><h1><h2><h3><strong><em><s><u><sup><sub>");
	return $ret;
}

/**
 * Fonctions de code et décodage entre un couple de version
 * (tableau no_version, no_sous_version) et un code de version
 * (chaine no_version.no_sous_version)
 *
 * @portée	publique
 * @param 	couple ou code de version
 * @retour	code ou couple de version
 */
function coder_couple_version($couple_version) {
	$no_version = (int) $couple_version[0];
	$no_sous_version = (int) $couple_version[1];
	$code_version = $no_version.".".$no_sous_version;
	return (string) $code_version;
}
function decoder_couple_version($code_version) {
	$pos = strpos($code_version, ".");
	if ($pos === false) {
		$no_version = (int) $code_version;
		$no_sous_version = 0;
	}
	elseif ($pos == 0) {
		$no_version = 0;
		$no_sous_version = (int) substr($code_version, 1);
	}
	else {
		$no_version = (int) substr($code_version, 0, $pos);
		$no_sous_version = (int) substr($code_version, 1+$pos);
	}
	return array($no_version, $no_sous_version);
}

/**
 * Fonctions de gestion des balises autorisées en édition
 * Permet de centraliser cette partie encore incertaine
 *
 * @portée	publique
 * @retour	variable
 */
function liste_balises_autorisees() {
	return array("h1", "h2", "h3", "h5", "p");
}
function balise_to_niveau($balise) {
	switch ($balise) {
		case "h1" :
			$niveau = 1;break;
		case "h2" :
			$niveau = 2;break;
		case "h3" :
			$niveau = 3;break;
		case "h4" :
			$niveau = 4;break;
		case "h5" :
			$niveau = -1;break;
		case "h6" :
			$niveau = -2;break;
		default:
			$niveau = 0;
	}
	return $niveau;
}
function niveau_to_balise($niveau, $edition) {
	$avec_classe = false;
	$classe = "";
	switch($niveau) {
		case -2:
			if ($edition) {$balise = "h6";}
			else {$balise = "p";$avec_classe = true;$classe = "tag_liste_niveau_2";}
			break;
		case -1:
			if ($edition) {$balise = "h5";}
			else {$balise = "p";$avec_classe = true;$classe = "tag_liste_niveau_1";}
			break;
		case 1:
			$balise = "h1";break;
		case 2:
			$balise = "h2";break;
		case 3:
			$balise = "h3";break;
		case 4:
			$balise = "h4";break;
		default:
			$balise = "p";
	}
	return array($balise, $avec_classe, $classe);
}

/**
 * Fonctions de lecture des paramètres GET et POST
 *
 * @portée	publique
 * @param	nom du paramètre
 * @retour	valeur du paramètre nettoyée
 */
function get($name) {
	if (is_null($name)) {$ret = null;}
	else if (strlen($name) == 0) {$ret = null;}
	else {
		if (isset($_GET[$name])) {
			$ret = $_GET[$name];
			if (strlen($ret) == 0) {$ret = null;}
			else {$ret = nettoyer_param($ret);}
		}
		else {$ret = null;}
	}
	return $ret;
}

function post($name) {
	$ret = null;
	if (strlen($name) > 0) {
		if (isset($_POST[$name])) {
			$param = $_POST[$name];
			if (strlen($param) > 0) {
				$ret = nettoyer_param($param);
			}
		}
	}
	return $ret;
}

function array_post($name) {
	$ret = array();
	if (strlen($name) > 0) {
		if (isset($_POST[$name])) {
			$array = $_POST[$name];
			foreach ($array as $elem) {
				$ret[] = nettoyer_param($elem);
			}
		}
	}
	return $ret;
}

/**
 * Fonction de nettoyage des paramètres GET et POST
 *
 * @portée	publique
 * @param	paramètre à nettoyer
 * @retour	paramètre nettoyé
 */
function nettoyer_param($str) {
	if (!is_null($str)) {
		// Protection contre le null byte poisonning
		$str = str_replace("\0", '', $str);
		// Traitement des magic quotes
		if (get_magic_quotes_gpc()) {$str = stripslashes($str);}
		// Suppression des espaces à gauche et à droite
		$str = trim($str);
	}
	return $str;
}