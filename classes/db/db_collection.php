<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_collection
 *
 * @collections		Philippe Gilles
 */
class medit_db_collection extends medit_db_tuple {

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_collection	Identifiant de la collection
	 * @retour	vide
	 */
    public function __construct($id_collection) {
		$this->declarer("liste_articles", medit_db_tuple::DB_TUPLE_LISTE);
		parent::__construct($id_collection);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		$sql = "SELECT id_article FROM ".table("article");
		$requete = medit_db::Executer_requete($sql, array("id_collection" => $this->id));
		$liste = array();
		while ($tuple = $requete->fetch()) {$liste[] = $tuple["id_article"];}
		$this->ecrire_liste_articles($liste);
		parent::charger();
	}
}