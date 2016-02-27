<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des identifications
 *
 * @auteurs		Philippe Gilles
 */
class medit_uc_identification {
	
	public static function Connecter($id_auteur, $pseudo = null) {
		medit_uc_session::Ecrire_session_param("log", medit_uc_session::Checksum_sessid(session_id()));
		medit_uc_session::Ecrire_session_param("time", time());
		medit_uc_session::Ecrire_session_param("id_auteur", $id_auteur);
		medit_uc_session::Ecrire_session_param("pseudo", $pseudo);
	}

	public static function Deconnecter() {
		medit_uc_session::Fermer_session();
	}

	/**
	 * Teste si un utilisateur est connecté
	 *
	 * @portée	publique
	 * @retour	vrai ou faux
	 */
	public static function Est_connecte() {
		$session_ouverte = medit_uc_session::Verifier_session();
		return $session_ouverte;
	}
	
	/**
	 * Teste si l'utilisateur est connecté en tant qu'admin
	 *
	 * @portée	publique
	 * @retour	vrai ou faux
	 */
	public static function Est_admin() {
		$ret = false;
		return $ret;
	}
	
	/**
	 * Retourne l'identifiant de l'utilisateur connecté
	 *
	 * @portée	publique
	 * @retour	vrai ou faux
	 */
	public static function Id_connecte() {
		return medit_uc_session::Lire_session_param("id_auteur");
	}

	
	/**
	 * Retourne le pseudo de l'utilisateur connecté
	 *
	 * @portée	publique
	 * @retour	vrai ou faux
	 */
	public static function Pseudo_connecte() {
		return medit_uc_session::Lire_session_param("pseudo");
	}
}