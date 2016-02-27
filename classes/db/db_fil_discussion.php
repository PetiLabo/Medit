<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_fil_discussion
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_fil_discussion extends medit_db_tuple {

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_fil_discussion	Identifiant du fil_discussion
	 * @retour	vide
	 */
    public function __construct($id_fil_discussion) {
		$this->declarer("horodatage", medit_db_tuple::DB_TUPLE_HORODATAGE);
		$this->declarer("fk_auteur", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("fk_article", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		parent::__construct($id_fil_discussion);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		$sql = "SELECT UNIX_TIMESTAMP(horodatage) AS 'horodatage', fk_auteur, fk_article ";
		$sql .= "FROM ".table("fil_discussion")." ";
		$sql .= "WHERE id_fil_discussion = :id_fil_discussion";
		$requete = medit_db::Executer_requete($sql, array("id_fil_discussion" => $this->id));
		if ($tuple = $requete->fetch()) {
			$this->ecrire_horodatage($tuple["horodatage"]);
			$this->ecrire_fk_auteur($tuple["fk_auteur"]);
			$this->ecrire_fk_article($tuple["fk_article"]);
		}
		parent::charger();
	}
	
	/**
	 * Liste des commentaires attachés au fil de discussion
	 *
	 * @portée	public
	 * @retour	liste des commentaires
	 */
	public function lire_liste_commentaires() {
		$sql = "SELECT id_commentaire ";
		$sql .= "FROM ".table("commentaire")." ";
		$sql .= "WHERE fk_fil_discussion = :id_fil_discussion";
		$requete = medit_db::Executer_requete($sql, array("id_fil_discussion" => $this->id));
		$liste = array();
		while ($tuple = $requete->fetch()) {
			$liste[] = (int) $tuple["id_commentaire"];
		}
		return $liste;
	}
	
	/**
	 * Liste des paragraphes attachés au fil de discussion
	 * TODO : Prendre en compte l'ordre des paragraphes selon la version
	 *
	 * @portée	public
	 * @retour	liste des paragraphes
	 */
	public function lire_liste_paragraphes() {
		$sql = "SELECT fk_paragraphe ";
		$sql .= "FROM ".table("discussion")." ";
		$sql .= "WHERE fk_fil_discussion = :id_fil_discussion";
		$requete = medit_db::Executer_requete($sql, array("id_fil_discussion" => $this->id));
		$liste = array();
		while ($tuple = $requete->fetch()) {
			$liste[] = (int) $tuple["fk_paragraphe"];
		}
		return $liste;
	}
}