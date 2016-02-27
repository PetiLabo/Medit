<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_discussion
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_discussion extends medit_db_tuple {

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_discussion	Identifiant de la discussion
	 * @retour	vide
	 */
    public function __construct($id_discussion) {
		$this->declarer("fk_paragraphe", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("fk_fil_discussion", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		parent::__construct($id_discussion);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		// Chargement des champs de la table discussion
		$sql = "SELECT fk_paragraphe, fk_fil_discussion ";
		$sql .= "FROM ".table("discussion")." ";
		$sql .= "WHERE id_discussion = :id_discussion";
		$requete = medit_db::Executer_requete($sql, array("id_discussion" => $this->id));
		if ($tuple = $requete->fetch()) {
			$this->ecrire_fk_paragraphe($tuple["fk_paragraphe"]);
			$this->ecrire_fk_fil_discussion($tuple["fk_fil_discussion"]);
		}
		// Appel de la méthode parente
		parent::charger();
	}
}