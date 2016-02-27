<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des sessions
 *
 * @auteurs		Philippe Gilles
 */
class medit_uc_session {

	/**
	 * Méthode à documenter
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Ouvrir_session() {
		session_start();
		return session_id();
	}

	/**
	 * Méthode à documenter
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Fermer_session($url_fermeture = null) {
		$id = session_id();
		if (strlen($id) > 0) {
			$_SESSION = array();
			session_destroy();
		}
		if (strlen($url_fermeture) > 0) {
			if (file_exists($url_fermeture)) {
				header("Location: ".$url_fermeture);
				die();
			}
		}
	}

	/**
	 * Méthode à documenter
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Verifier_session() {
		$ret = false;
		$sess_id = (int) self::Lire_session_param("log");
		if (self::Checksum_sessid(session_id()) === $sess_id) {
			// Vérification du timeout
			$sess_time = self::Lire_session_param("time");
			$sess_lifetime = time() - $sess_time;
			if ($sess_lifetime <= medit_config::UC_TIMEOUT_SESSION) {
				// On réarme le timeout
				self::Ecrire_session_param("time", time());

				/* Par sécurité on regénère l'identifiant de session (si header encore vide) */
				$ret = @headers_sent();
				if (!($ret)) {
					$ret = @session_regenerate_id();
					if ($ret) {
						self::Ecrire_session_param("log", self::Checksum_sessid(session_id()));
					}
				}
			}
		}
		return $ret;
	}

	/**
	 * Méthode à documenter
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Lire_session_param($nom) {
		$nom_avec_prefixe = "medit_".(medit_config::UC_PREFIXE_SESSION).$nom;
		if (is_null($nom)) {$ret = null;}
		else if (strlen($nom) === 0) {$ret = null;}
		else {
			if (isset($_SESSION[$nom_avec_prefixe])) {
				$ret = $_SESSION[$nom_avec_prefixe];
				if (strlen($ret) === 0) {$ret = null;}
				else {$ret = str_replace("\0", '', $ret);}
			}
			else {$ret = null;}
		}
		return $ret;
	}

	/**
	 * Méthode à documenter
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Ecrire_session_param($nom, $valeur) {
		$nom_avec_prefixe = "medit_".medit_config::UC_PREFIXE_SESSION.$nom;
		if ((is_null($nom)) || (is_null($valeur))) {$ret = null;}
		else if ((strlen($nom) == 0) || (strlen($valeur) == 0)) {$ret = null;}
		else {
			$_SESSION[$nom_avec_prefixe] = $valeur;
			$ret = $valeur;
		}
		return $ret;
	}

	/**
	 * Méthode à documenter
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Supprimer_session_param($nom) {
		$nom_avec_prefixe = "medit_".medit_config::UC_PREFIXE_SESSION.$nom;
		if (strlen($nom) > 0) {unset($_SESSION[$nom_avec_prefixe]);}
	}

	/**
	 * Méthode à documenter
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Checksum_sessid($id) {
		$ret = (int) 0;
		if (!(is_null($id))) {
			for ($cpt = 0; $cpt < strlen($id); $cpt++) {
				$ret += (ord(substr($id, $cpt, 1))-32);
			}
		}
		return $ret;
	}
}