<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des autorisations
 *
 * @auteurs		Philippe Gilles
 */
class medit_uc_autorisation {
	const DB_DROIT_MODE_EDITION = 1;
	const DB_DROIT_EDITION = 2;
	const DB_DROIT_COMMENTAIRE = 3;
	const DB_DROIT_ANNOTATION = 4;
	const DB_DROIT_CREATION_VERSION = 5;
	const DB_DROIT_CREATION_FIL_DISCUSSION = 6;
	
	/**
	 * Teste si l'utilisateur connecté possède l'autorisation demandée
	 *
	 * @portée	publique
	 * @retour	vrai ou faux
	 */
	public static function Lire_autorisation_connexion($code_autorisation) {
		$autorisation = false;
		$est_connecte = medit_uc_identification::Est_connecte();
		if ($est_connecte) {
			$id_auteur = medit_uc_identification::Id_connecte();
			$autorisation = self::Lire_autorisation_auteur($id_auteur, $code_autorisation);
		}
		return $autorisation;
	}
	
	/**
	 * Teste si un utilisateur donné possède l'autorisation demandée
	 *
	 * @portée	publique
	 * @retour	vrai ou faux
	 */
	public static function Lire_autorisation_auteur($id_auteur, $code_autorisation) {
		$autorisation = false;
		$auteur = medit_db_table_auteur::Construire($id_auteur);
		if ($auteur->en_db()) {
			$id_profil = $auteur->lire_fk_profil();
			$profil = medit_db_table_profil::Construire($id_profil);
			switch ($code_autorisation) {
				case self::DB_DROIT_MODE_EDITION :
					$autorisation = $profil->lire_droit_mode_edition();
					break;
				case self::DB_DROIT_EDITION :
					$autorisation = $profil->lire_droit_edition();
					break;
				case self::DB_DROIT_COMMENTAIRE :
					$autorisation = $profil->lire_droit_commentaire();
					break;
				case self::DB_DROIT_ANNOTATION :
					$autorisation = $profil->lire_droit_annotation();
					break;
				case self::DB_DROIT_CREATION_VERSION :
					$autorisation = $profil->lire_droit_creation_version();
					break;
				case self::DB_DROIT_CREATION_FIL_DISCUSSION :
					$autorisation = $profil->lire_droit_creation_fil_discussion();
					break;
				default:
					$autorisation = false;
					break;
			}
		}
		return $autorisation;
	}
}