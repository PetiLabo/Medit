<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_composition
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_composition extends medit_db_tuple {

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_composition	Identifiant de la composition
	 * @retour	vide
	 */
    public function __construct($id_composition) {
		$this->declarer("fk_version", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("fk_paragraphe", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("no_ordre", medit_db_tuple::DB_TUPLE_ENTIER);
		parent::__construct($id_composition);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		// Chargement des champs de la table composition
		$sql = "SELECT fk_version, fk_paragraphe, no_ordre ";
		$sql .= "FROM ".table("composition")." ";
		$sql .= "WHERE id_composition = :id_composition";
		$requete = medit_db::Executer_requete($sql, array("id_composition" => $this->id));
		if ($tuple = $requete->fetch()) {
			$this->ecrire_fk_version($tuple["fk_version"]);
			$this->ecrire_fk_paragraphe($tuple["fk_paragraphe"]);
			$this->ecrire_no_ordre($tuple["no_ordre"]);
		}
		// Appel de la méthode parente
		parent::charger();
	}
}