<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_composition
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table_composition extends medit_db_table {
	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();
	
	/**
	 * Création d'une nouvelle composition
	 *
	 * @portée	publique
	 * @param	identifiant de la version
	 * @param	identifiant du paragraphe
	 * @param	numéro d'ordre du paragraphe dans la composition
	 * @retour	identifiant de la composition
	 */
	public static function Inserer($id_version, $id_paragraphe, $no_ordre) {
		$sql = "INSERT INTO ".table("composition")."(fk_version, fk_paragraphe, no_ordre) ";
		$sql .= "VALUES (:fk_version, :fk_paragraphe, :no_ordre)";
		$id_composition = medit_db::Inserer_requete($sql, array("fk_version" => $id_version, "fk_paragraphe" => $id_paragraphe, "no_ordre" => $no_ordre));
		return $id_composition;
	}

	/**
	 * Liste ordonnée des paragraphes composant une version donnée
	 *
	 * @portée	publique
	 * @param	identifiant de la version
	 * @retour	liste des identifiants de paragraphes
	 */
	public static function Liste_paragraphes_par_version($id_version) {
		$liste = array();
		$sql = "SELECT fk_paragraphe ";
		$sql .= "FROM ".table("composition")." ";
		$sql .= "WHERE fk_version = :id_version ";
		$sql .= "ORDER BY no_ordre";
		$requete = medit_db::Executer_requete($sql, array("id_version" => $id_version));
		while ($tuple = $requete->fetch()) {$liste[] = $tuple["fk_paragraphe"];}
		return $liste;
	}
	
	/**
	 * Liste des verrous actifs pour une version d'article donnée
	 *
	 * @portée	publique
	 * @param	identifiant de la version
	 * @retour	liste des verrous avec le pseudo et la liste de paragraphes associés
	 */
	public static function Liste_verrous_par_version($id_version) {
		$liste = array();
		$sql = "SELECT id_verrou, id_paragraphe, pseudo ";
		$sql .= "FROM ((".table("composition")." INNER JOIN ".table("paragraphe")." ON fk_paragraphe = id_paragraphe) ";
		$sql .= "INNER JOIN ".table("verrou")." ON fk_verrou = id_verrou) ";
		$sql .= "LEFT JOIN ".table("auteur")." ON ".table("verrou").".fk_auteur = id_auteur ";
		$sql .= "WHERE verrou = 1 AND fk_version = :id_version ";
		$sql .= "ORDER BY no_ordre";
		$requete = medit_db::Executer_requete($sql, array("id_version" => $id_version));
		while ($tuple = $requete->fetch()) {
			$id_verrou = $tuple["id_verrou"];
			$id_paragraphe = $tuple["id_paragraphe"];
			$pseudo = $tuple["pseudo"];
			if (!(isset($liste[$id_verrou]))) {
				$liste[$id_verrou] = array("pseudo" => $pseudo, "liste_paragraphes" => array($id_paragraphe));
			}
			else {
				$liste[$id_verrou]["liste_paragraphes"][] = $id_paragraphe;
			}
		}
		return $liste;
	}

	/**
	 * Liste ordonnée des paragraphes verrouillés dans l'ordre de la version donnée
	 *
	 * @portée	publique
	 * @param	identifiant du verrou
	 * @param	identifiant de la version
	 * @retour	liste des identifiants de paragraphes
	 */
	public static function Liste_paragraphes_par_verrou_et_par_version($id_verrou, $id_version) {
		$liste = array();
		$sql = "SELECT id_paragraphe ";
		$sql .= "FROM ".table("composition")." INNER JOIN ".table("paragraphe")." ON fk_paragraphe = id_paragraphe ";
		$sql .= "WHERE fk_verrou = :id_verrou AND fk_version = :id_version ";
		$sql .= "ORDER BY no_ordre";
		$requete = medit_db::Executer_requete($sql, array("id_verrou" => $id_verrou, "id_version" => $id_version));
		while ($tuple = $requete->fetch()) {
			$liste[] = $tuple["id_paragraphe"];
		}
		return $liste;
	}
}