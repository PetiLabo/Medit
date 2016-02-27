<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_type_annotation
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_type_annotation extends medit_db_tuple {

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_type_annotation	Identifiant du type_annotation
	 * @retour	vide
	 */
    public function __construct($id_type_annotation) {
		$this->declarer("label_annotation", medit_db_tuple::DB_TUPLE_CHAINE);
		$this->declarer("code_couleur", medit_db_tuple::DB_TUPLE_CHAINE);
		parent::__construct($id_type_annotation);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		$sql = "SELECT label_annotation, code_couleur ";
		$sql .= "FROM ".table("type_annotation")." ";
		$sql .= "WHERE id_type_annotation = :id_type_annotation";
		$requete = medit_db::Executer_requete($sql, array("id_type_annotation" => $this->id));
		if ($tuple = $requete->fetch()) {
			$this->ecrire_label_annotation($tuple["label_annotation"]);
			$this->ecrire_code_couleur($tuple["code_couleur"]);
		}
		parent::charger();
	}
}