<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_verrou
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table_verrou extends medit_db_table {
	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();
	
	public static function Inserer($type_verrou, $fk_auteur) {
		$sql = "INSERT INTO ".table("verrou")."(type_verrou, horodatage, fk_auteur) ";
		$sql .= "VALUES (:type_verrou, NOW(), :fk_auteur)";
		$id_verrou = medit_db::Inserer_requete($sql, array("type_verrou" => $type_verrou, "fk_auteur" => $fk_auteur));
		return $id_verrou;
	}

	/**
	 * Garbage collector des verrous suspendus
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Purger() {
		$sql = "SELECT id_verrou ";
		$sql .= "FROM ".table("verrou")." ";
		$sql .= "WHERE suspension IS NOT NULL AND suspension < (NOW() - INTERVAL 1 HOUR)";
		$requete = medit_db::Executer_requete($sql, null);
		while ($tuple = $requete->fetch()) {
			$id_verrou = $tuple["id_verrou"];
			medit_db_table_paragraphe::Purger($id_verrou);
			self::Detruire($id_verrou);
		}
	}
}