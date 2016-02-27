<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_auteur
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table_auteur extends medit_db_table {
	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();

	/**
	 * Recherche d'un auteur connaissant son identifiant
	 *
	 * @portée	publique
	 * @retour	clé trouvée
	 */
	public static function Chercher_par_identifiant($login_id) {
		$ret = null;
		$sql = "SELECT id_auteur ";
		$sql .= "FROM ".table("auteur")." ";
		$sql .= "WHERE identifiant = :login_id";
		$requete = medit_db::Executer_requete($sql, array("login_id" => $login_id));
		if ($tuple = $requete->fetch()) {$ret = $tuple["id_auteur"];}
		return $ret;
	}
}