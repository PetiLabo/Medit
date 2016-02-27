<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_journal
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table_journal extends medit_db_table {
	const DB_JOURNAL_CONNEXION = 1;
	const DB_JOURNAL_DECONNEXION = 2;
	const DB_JOURNAL_VERROUILLAGE_PARAGRAPHE = 3;
	const DB_JOURNAL_DEVERROUILLAGE_PARAGRAPHE = 4;
	const DB_JOURNAL_EDITION_PARAGRAPHE = 5;
	const DB_JOURNAL_ANNOTATION_PARAGRAPHE = 6;
	const DB_JOURNAL_CREATION_FIL_DISCUSSION = 7;
	const DB_JOURNAL_SUPPRESSION_FIL_DISCUSSION = 8;
	const DB_JOURNAL_REPONSE_COMMENTAIRE = 9;
	const DB_JOURNAL_MODIFICATION_COMMENTAIRE = 10;
	const DB_JOURNAL_SUPPRESSION_COMMENTAIRE = 11;

	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();

	/**
	 * Création d'un nouvel événement dans le journal
	 *
	 * @portée	publique
	 * @param	type d'événement
	 * @param	identifiant de l'auteur
	 * @retour	identifiant du nouvel événement
	 */
	public static function Inserer($type_evenement, $id_auteur, $id_article = 0, $id_contextuel = 0) {
		$sql = "INSERT INTO ".table("journal")."(type_evenement, horodatage, fk_auteur, fk_article, fk_contextuel) ";
		$sql .= "VALUES (:type_evenement, NOW(), :fk_auteur, :fk_article, :fk_contextuel)";
		$id_journal = medit_db::Inserer_requete($sql, array("type_evenement" => $type_evenement, "fk_auteur" => $id_auteur, "fk_article" => $id_article, "fk_contextuel" => $id_contextuel));
		return $id_journal;
	}

	public static function Lire_dernier_id_journal() {
		$max_id_journal = 0;
		$sql = "SELECT MAX(id_journal) AS 'max_id_journal'";
		$sql .= "FROM ".table("journal");
		$requete = medit_db::Executer_requete($sql, null);
		if ($tuple = $requete->fetch()) {
			$max_id_journal = (int) $tuple["max_id_journal"];
		}
		return $max_id_journal;
	}

	/**
	 * Garbage collector des événements du journal
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Purger() {
		$sql = "DELETE FROM ".table("journal")." ";
		$sql .= "WHERE horodatage < (NOW() - INTERVAL 1 MONTH)";
		medit_db::Executer_requete($sql, null);
	}
}