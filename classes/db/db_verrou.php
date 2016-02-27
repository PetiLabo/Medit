<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_verrou
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_verrou extends medit_db_tuple {

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_verrou	Identifiant de la verrou
	 * @retour	vide
	 */
    public function __construct($id_verrou) {
		$this->declarer("type_verrou", medit_db_tuple::DB_TUPLE_ENTIER);
		$this->declarer("horodatage", medit_db_tuple::DB_TUPLE_HORODATAGE);
		$this->declarer("suspension", medit_db_tuple::DB_TUPLE_HORODATAGE);
		$this->declarer("fk_auteur", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		parent::__construct($id_verrou);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		$sql = "SELECT type_verrou, horodatage, suspension, fk_auteur ";
		$sql .= "FROM ".table("verrou")." ";
		$sql .= "WHERE id_verrou = :id_verrou";
		$requete = medit_db::Executer_requete($sql, array("id_verrou" => $this->id));
		if ($tuple = $requete->fetch()) {
			$this->ecrire_type_verrou($tuple["type_verrou"]);
			$this->ecrire_horodatage($tuple["horodatage"]);
			$this->ecrire_suspension($tuple["suspension"]);
			$this->ecrire_fk_auteur($tuple["fk_auteur"]);
		}
		parent::charger();
	}

	/**
	 * Suspension du verrou
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function suspendre() {
		// Côté BDD
		$sql = "UPDATE ".table("verrou")." ";
		$sql .= "SET suspension = NOW() ";
		$sql .= "WHERE id_verrou = :id_verrou";
		medit_db::Executer_requete($sql, array("id_verrou" => $this->id));
	
		// Côté classe
		$this->ecrire_suspension(time());
	}
}