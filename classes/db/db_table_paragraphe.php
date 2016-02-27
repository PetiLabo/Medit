<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_paragraphe
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table_paragraphe extends medit_db_table {
	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();
	
	/**
	 * Création d'un nouveau paragraphe
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Inserer($niveau, $texte, $fk_auteur, $verrou = false, $fk_verrou = 0) {
		$sql = "INSERT INTO ".table("paragraphe")."(niveau, horodatage, texte, verrou, fk_verrou, fk_auteur) ";
		$sql .= "VALUES (:niveau, NOW(), :texte, :verrou, :fk_verrou, :fk_auteur)";
		$id_paragraphe = medit_db::Inserer_requete($sql, array("niveau" => $niveau, "texte" => $texte, "verrou" => (($verrou)?1:0), "fk_verrou" => $fk_verrou, "fk_auteur" => $fk_auteur));
		return $id_paragraphe;
	}

	/**
	 * Déverrouillage de tous les paragraphes verrouillés par un auteur
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Deverrouiller_auteur($id_auteur) {
		// Dans la BDD
		$sql = "UPDATE ".table("paragraphe")." ";
		$sql .= "SET verrou = 0 ";
		$sql .= "WHERE fk_verrou IN (SELECT id_verrou FROM ".table("verrou")."  WHERE fk_auteur = :id_auteur)";
		medit_db::Executer_requete($sql, array("id_auteur" => $id_auteur));
		$sql = "UPDATE ".table("verrou")." ";
		$sql .= "SET suspension = NOW() ";
		$sql .= "WHERE fk_auteur = :id_auteur";
		medit_db::Executer_requete($sql, array("id_auteur" => $id_auteur));

		// Dans la classe
		foreach (self::$Liste_tuples as $paragraphe) {
			$id_verrou = $paragraphe->lire_fk_verrou();
			$verrou = medit_db_table_verrou::Construire($id_verrou);
			if ($verrou) {
				$fk_auteur = $verrou->lire_fk_auteur();
				if ($id_auteur == $fk_auteur) {
					$paragraphe->ecrire_verrou(false);
					$verrou->ecrire_suspension(time());
				}
			}
		}
	}

	/**
	 * Verrouillage d'une liste de paragraphes
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Verrouiller($liste_a_verrouiller, $id_verrou) {
		// Dans la BDD
		$sql = "UPDATE ".table("paragraphe")." ";
		$sql .= "SET verrou = 1, fk_verrou = :id_verrou ";
		$sql .= "WHERE id_paragraphe = ".implode($liste_a_verrouiller, " OR id_paragraphe = ");
		medit_db::Executer_requete($sql, array("id_verrou" => $id_verrou));

		// Dans la classe
		foreach ($liste_a_verrouiller as $id_paragraphe) {
			if (isset(self::$Liste_tuples[$id_paragraphe])) {
				$paragraphe = self::$Liste_tuples[$id_paragraphe];
				$paragraphe->ecrire_verrou(true);
				$paragraphe->ecrire_fk_verrou($id_verrou);
			}
		}
	}

	/**
	 * Déverrouillage d'une liste de paragraphes
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Deverrouiller($liste_a_deverrouiller, $id_verrou) {
		// Dans la BDD
		$sql = "UPDATE ".table("paragraphe")." ";
		$sql .= "SET verrou = 0 ";
		$sql .= "WHERE id_paragraphe = ".implode($liste_a_deverrouiller, " OR id_paragraphe = ");
		medit_db::Executer_requete($sql, null);

		// Dans la classe
		foreach ($liste_a_deverrouiller as $id_paragraphe) {
			if (isset(self::$Liste_tuples[$id_paragraphe])) {
				$paragraphe = self::$Liste_tuples[$id_paragraphe];
				$paragraphe->ecrire_verrou(false);
			}
		}
	}
	
	/**
	 * Purge des paragraphes portant un verrou suspendu
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Purger($id_verrou) {
		$liste = array();
		$sql = "SELECT id_paragraphe ";
		$sql .= "FROM ".table("paragraphe")." ";
		$sql .= "WHERE fk_verrou = :id_verrou";
		$requete = medit_db::Executer_requete($sql, array("id_verrou" => $id_verrou));
		while ($tuple = $requete->fetch()) {
			$liste[] = (int) $tuple["id_paragraphe"];
		}
		$nb_liste = count($liste);
		if ($nb_liste > 0) {
			// Dans la BDD
			$sql = "UPDATE ".table("paragraphe")." ";
			$sql .= "SET verrou = 0, fk_verrou = 0 ";
			$sql .= "WHERE id_paragraphe = ".implode($liste, " OR id_paragraphe = ");
			medit_db::Executer_requete($sql, null);

			// Dans la classe
			foreach ($liste as $id_paragraphe) {
				if (isset(self::$Liste_tuples[$id_paragraphe])) {
					$paragraphe = self::$Liste_tuples[$id_paragraphe];
					$paragraphe->ecrire_verrou(false);
					$paragraphe->ecrire_fk_verrou(0);
				}
			}
		}
	}
	
	/**
	 * Liste des paragraphes par verrou et toutes versions confondues
	 * TODO : Actuellement inutiliséee, voir si on garde cette méthode
	 *
	 * @portée	publique
	 * @param	identifiant du verrou
	 * @retour	liste de paragraphes
	 */	
	public static function Liste_paragraphes_par_verrou($id_verrou) {
		$liste = array();
		$sql = "SELECT id_paragraphe ";
		$sql .= "FROM ".table("paragraphe")." ";
		$sql .= "WHERE verrou = 1 AND fk_verrou = :id_verrou";
		$requete = medit_db::Executer_requete($sql, array("id_verrou" => $id_verrou));
		while ($tuple = $requete->fetch()) {
			$liste[] = (int) $tuple["id_paragraphe"];
		}
		return $liste;
	}
}