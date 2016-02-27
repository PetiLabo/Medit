<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_article
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table_article extends medit_db_table {
	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();
	
	
	/**
	 * Accesseur vers la liste des versions de la barre d'outils
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	 public static function Lire_infos_version($id_article, $code_version) {
		global $_MEDIT_LANGUE;

		// Récupération de l'article
		$article = self::Construire($id_article);
		if (!($article->en_db())) {return null;}
		
		// Récupération de la version
		$couple_version = decoder_couple_version($code_version);
		$id_version = (int) $article->no_to_id_version($couple_version);
		if ($id_version < 1) {return null;}
		$version = medit_db_table_version::Construire($id_version);
		$horodatage = $version->lire_horodatage();
		$id_auteur = $version->lire_fk_auteur();
		$auteur = medit_db_table_auteur::Construire($id_auteur);
		$pseudo = ($auteur->en_db())?$auteur->lire_pseudo():$_MEDIT_LANGUE->pseudo_inconnu();
		$date = date(medit_config::LANG_FORMAT_DATE, $horodatage);
		$heure = date(medit_config::LANG_FORMAT_HEURE, $horodatage);
		return array("horodatage" => $horodatage, "date" => $date, "heure" => $heure, "pseudo" => $pseudo);
	}
}