<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_journal
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_journal extends medit_db_tuple {

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_journal	Identifiant du journal
	 * @retour	vide
	 */
    public function __construct($id_journal) {
		$this->declarer("type_evenement", medit_db_tuple::DB_TUPLE_ENTIER);
		$this->declarer("horodatage", medit_db_tuple::DB_TUPLE_HORODATAGE);
		$this->declarer("fk_auteur", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("fk_article", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("fk_contextuel", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		parent::__construct($id_journal);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		$sql = "SELECT type_evenement, UNIX_TIMESTAMP(horodatage) AS 'horodatage', fk_auteur, fk_article, fk_contextuel ";
		$sql .= "FROM ".table("journal")." ";
		$sql .= "WHERE id_journal = :id_journal";
		$requete = medit_db::Executer_requete($sql, array("id_journal" => $this->id));
		if ($tuple = $requete->fetch()) {
			$this->ecrire_type_evenement($tuple["type_evenement"]);
			$this->ecrire_horodatage($tuple["horodatage"]);
			$this->ecrire_fk_auteur($tuple["fk_auteur"]);
			$this->ecrire_fk_article($tuple["fk_article"]);
			$this->ecrire_fk_contextuel($tuple["fk_contextuel"]);
		}
		parent::charger();
	}
}