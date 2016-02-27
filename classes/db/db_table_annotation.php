<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_annotation
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table_annotation extends medit_db_table {
	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();

	
	/**
	 * Création d'une nouvelle annotation
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Inserer($id_type_annotation, $id_paragraphe, $id_auteur, $texte) {
		$sql = "INSERT INTO ".table("annotation")."(fk_type_annotation, fk_paragraphe, fk_auteur, texte) ";
		$sql .= "VALUES (:id_type_annotation, :id_paragraphe, :id_auteur, :texte)";
		$id_annotation = medit_db::Inserer_requete($sql, array("id_type_annotation" => $id_type_annotation, "id_paragraphe" => $id_paragraphe, "id_auteur" => $id_auteur, "texte" => $texte));
		return $id_annotation;
	}

	/**
	 * Mise à jour des annotations liées à un paragraphe donné
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Mettre_a_jour_paragraphe($id_paragraphe, $liste_annotations) {
		// Suppression des anciennes annotations
		$sql = "SELECT id_annotation ";
		$sql .= "FROM ".table("annotation")." ";
		$sql .= "WHERE fk_paragraphe = :id_paragraphe";
		$requete = medit_db::Executer_requete($sql, array("id_paragraphe" => $id_paragraphe));
		while ($tuple = $requete->fetch()) {
			$id_annotation = (int) $tuple["id_annotation"];
			self::Detruire($id_annotation);
		}
		// Création des nouvelles annotations
		foreach($liste_annotations as $tab_annotation) {
			$id_auteur = $tab_annotation["id_auteur"];
			$id_type_annotation = $tab_annotation["id_annotation"];
			$texte = $tab_annotation["texte"];
			self::Inserer($id_type_annotation, $id_paragraphe, $id_auteur, $texte);
		}
	}
}