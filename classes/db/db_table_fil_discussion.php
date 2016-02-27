<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_fil_discussion
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table_fil_discussion extends medit_db_table {
	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();
	
	/**
	 * Création d'un nouveau fil de discussion
	 *
	 * @portée	publique
	 * @param	tableau des identifiants de paragraphes à discuter
	 * @param	identifiant du créateur du fil de discussion
	 * @retour	identifiant du nouveau fil de discussion
	 */
	public static function Inserer($liste_a_discuter, $id_auteur, $id_article) {
		$sql = "INSERT INTO ".table("fil_discussion")."(horodatage, fk_auteur, fk_article) ";
		$sql .= "VALUES (NOW(), :id_auteur, :id_article)";
		$id_fil_discussion = medit_db::Inserer_requete($sql, array("id_auteur" => $id_auteur, "id_article" => $id_article));
		if ($id_fil_discussion > 0) {
			foreach($liste_a_discuter as $id_paragraphe) {
				$sql = "INSERT INTO ".table("discussion")."(fk_paragraphe, fk_fil_discussion) ";
				$sql .= "VALUES (:id_paragraphe, :id_fil_discussion)";
				medit_db::Inserer_requete($sql, array("id_paragraphe" => $id_paragraphe, "id_fil_discussion" => $id_fil_discussion));
			}
		}
		return $id_fil_discussion;
	}
	
	/**
	 * Suppression d'un fil de discussion et des tuples dépendants
	 *
	 * @portée	publique
	 * @param	identifiant du fil à supprimer
	 * @retour	vide
	 */
	public static function Supprimer($id_fil_discussion) {
		// Suppression préalable des commentaires
		$sql = "DELETE FROM ".table("commentaire")." ";
		$sql .= "WHERE fk_fil_discussion = :id_fil_discussion";
		medit_db::Executer_requete($sql, array("id_fil_discussion" => $id_fil_discussion));
		
		// Suppression préalable de la liste des paragraphes discutés
		$sql = "DELETE FROM ".table("discussion")." ";
		$sql .= "WHERE fk_fil_discussion = :id_fil_discussion";
		medit_db::Executer_requete($sql, array("id_fil_discussion" => $id_fil_discussion));

		// Suppression du fil lui-même
		self::Detruire($id_fil_discussion);
	}

	/**
	 * Cherche un fil de discussion portant sur une liste de paragraphes donnée
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Chercher_liste_paragraphes($liste_a_rechercher) {
		$id_distance_nulle = 0;
		$id_distance_positive_min = 0;
		$distance_positive_min = 99;
		$id_distance_negative_min = 0;
		$distance_negative_min = -99;
		sort($liste_a_rechercher);
		$sql = "SELECT id_fil_discussion ";
		$sql .= "FROM ".table("fil_discussion");
		$requete = medit_db::Executer_requete($sql, null);
		while (($tuple = $requete->fetch()) && ($id_distance_nulle == 0)) {
			$id_fil_discussion = $tuple["id_fil_discussion"];
			$fil_discussion = self::Construire($id_fil_discussion);
			$liste_paragraphes = $fil_discussion->lire_liste_paragraphes();
			$distance = calculer_distance_tableaux($liste_a_rechercher, $liste_paragraphes);
			if ($distance == 0) {
				$id_distance_nulle = $id_fil_discussion;
			}
			else if ($distance > 0) {
				if ($distance < $distance_positive_min) {
					$id_distance_positive_min = $id_fil_discussion;
					$distance_positive_min = $distance;
				}
			}
			else {
				if ($distance > $distance_negative_min) {
					$id_distance_negative_min = $id_fil_discussion;
					$distance_negative_min = $distance;
				}
			}
		}
		$ret = array();
		if ($id_distance_nulle > 0) {
			$ret[] = array("distance" => 0, "id" => $id_distance_nulle);
		}
		else {
			if (($id_distance_negative_min > 0) && ($id_distance_positive_min > 0)) {
				if ((-$distance_negative_min) > $distance_positive_min) {
					$id_distance_negative_min = 0;
				}
				else if ((-$distance_negative_min) < $distance_positive_min) {
					$id_distance_positive_min = 0;
				}
			}
			if ($id_distance_negative_min > 0) {
				$ret[] = array("distance" => $distance_negative_min, "id" => $id_distance_negative_min);
			}
			if ($id_distance_positive_min > 0) {
				$ret[] = array("distance" => $distance_positive_min, "id" => $id_distance_positive_min);
			}
		}
		return $ret;
	}
}