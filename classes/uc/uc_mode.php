<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des modes de l'interface
 *
 * @auteurs		Philippe Gilles
 */
class medit_uc_mode {

	const UC_MODE_EDITION = 1;
	const UC_MODE_HISTORIQUE = 2;
	const UC_MODE_PAR_DEFAUT = 2;

	/**
	 * Accesseur au mode
	 *
	 * @portée	publique
	 * @retour	mode
	 */
	public static function Lire_mode() {
		$mode = (int) medit_uc_session::Lire_session_param("mode_article");
		if ($mode == 0) {$mode = self::UC_MODE_PAR_DEFAUT; }
		return $mode;
	}
	
	/**
	 * Modifieur du mode
	 *
	 * @portée	publique
	 * @param	mode
	 * @retour	vide
	 */
	public static function Ecrire_mode($mode) {
		medit_uc_session::Ecrire_session_param("mode_article", $mode);
	}
}