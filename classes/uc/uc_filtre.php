<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des filtres appliqués au mode historique
 *
 * @auteurs		Philippe Gilles
 */
class medit_uc_filtre {
	const UC_FILTRE_JOURNAL_DIMENSION = "dimension_journal";
	const UC_FILTRE_HISTORIQUE_EDITEUR = "historique_editeur";
	const UC_FILTRE_HISTORIQUE_ANNOTATION_ACTIF = "historique_annotation_actif";
	const UC_FILTRE_HISTORIQUE_ANNOTATEUR = "historique_annotateur";
	const UC_FILTRE_HISTORIQUE_TYPE_ANNOTATION = "historique_type_annotation";
	const UC_FILTRE_HISTORIQUE_COMMENTAIRE_ACTIF = "historique_commentaire_actif";
	const UC_FILTRE_HISTORIQUE_COMMENTATEUR = "historique_commentateur";
	
	private static $Liste_criteres = array(self::UC_FILTRE_JOURNAL_DIMENSION, self::UC_FILTRE_HISTORIQUE_EDITEUR, self::UC_FILTRE_HISTORIQUE_ANNOTATION_ACTIF, self::UC_FILTRE_HISTORIQUE_ANNOTATEUR, self::UC_FILTRE_HISTORIQUE_TYPE_ANNOTATION, self::UC_FILTRE_HISTORIQUE_COMMENTAIRE_ACTIF, self::UC_FILTRE_HISTORIQUE_COMMENTATEUR);

	/**
	 * Modifieur des critères
	 *
	 * @portée	publique
	 * @param	identifiant de l'article
	 * @param	nom du critere
	 * @param	valeur du critère
	 * @retour	true si écriture ok, false sinon
	 */
	public static function Ecrire_critere_filtre($id_article, $critere, $valeur) {
		$ret = false;
		$suffixe = (int) $id_article;
		if ((in_array($critere, self::$Liste_criteres)) && ($suffixe > 0)) {
			$parametre = $critere."_".$suffixe;
			$ret_session = medit_uc_session::Ecrire_session_param($parametre, (int) $valeur);
			$ret = ($ret_session !== null);
		}
		return $ret;
	}
	
	/**
	 * Accesseur aux critères
	 *
	 * @portée	publique
	 * @param	identifiant de l'article
	 * @param	nom du critere
	 * @retour	valeur du critère
	 */
	public static function Lire_critere_filtre($id_article, $critere) {
		$valeur = 0;
		$suffixe = (int) $id_article;
		if ((in_array($critere, self::$Liste_criteres)) && ($suffixe > 0)) {
			$parametre = $critere."_".$suffixe;
			$valeur = (int) medit_uc_session::Lire_session_param($parametre);
		}
		return $valeur;
	}

	/**
	 * Accesseur à la liste des critères pour un article donné
	 *
	 * @portée	publique
	 * @param	identifiant de l'article
	 * @retour	liste des critères avec leur valeur
	 */
	public static function Lire_liste_criteres($id_article) {
		$liste = array();
		foreach (self::$Liste_criteres as $critere) {
			$valeur = self::Lire_critere_filtre($id_article, $critere);
			$liste[$critere] = $valeur;
		}
		return $liste;
	}
}