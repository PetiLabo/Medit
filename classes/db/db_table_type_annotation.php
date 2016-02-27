<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_type_annotation
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table_type_annotation extends medit_db_table {
	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();
	
	/**
	 * Liste de tous les types d'annotation en BDD
	 * TODO : Réfléchir à l'ordre dans lequel la liste est triée
	 *
	 * @portée	publique
	 * @retour	liste des identifiants des types d'annotation
	 */	
	public static function Liste_types_annotation() {
		$liste = array();
		$sql = "SELECT id_type_annotation ";
		$sql .= "FROM ".table("type_annotation")." ";
		$sql .= "ORDER BY label_annotation";
		$requete = medit_db::Executer_requete($sql, null);
		while ($tuple = $requete->fetch()) {
			$liste[] = (int) $tuple["id_type_annotation"];
		}
		return $liste;
	}
}