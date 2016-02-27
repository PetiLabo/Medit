<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_version
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table_version extends medit_db_table {
	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();
	
	/**
	 * Recherche d'une version par numéro
	 *
	 * @portée	publique
	 * @retour	clé trouvée
	 */
	public static function Chercher_par_no_version($no_version) {
		$ret = null;
		$sql = "SELECT id_version ";
		$sql .= "FROM ".table("version")." ";
		$sql .= "WHERE no_version = :no_version";
		$requete = medit_db::Executer_requete($sql, array("no_version" => $no_version));
		if ($tuple = $requete->fetch()) {$ret = $tuple["id_version"];}
		return $ret;
	}
	
	/**
	 * Création d'une nouvelle version
	 *
	 * @portée	publique
	 * @retour	identifiant du nouveau tuple
	 */
	public static function Inserer($id_article, $couple_version, $id_auteur) {
		$no_version = (int) $couple_version[0];
		$no_sous_version = (int) $couple_version[1];
		$sql = "INSERT INTO ".table("version")."(no_version, no_sous_version, horodatage, fk_article, fk_auteur) ";
		$sql .= "VALUES (:no_version, :no_sous_version, NOW(), :fk_article, :fk_auteur)";
		$id_version = medit_db::Inserer_requete($sql, array("no_version" => $no_version, "no_sous_version" => $no_sous_version, "fk_article" => $id_article, "fk_auteur" => $id_auteur));
		return $id_version;
	}
}