<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_annotation
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_annotation extends medit_db_tuple {

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_annotation	Identifiant de l'annotation
	 * @retour	vide
	 */
    public function __construct($id_annotation) {
		$this->declarer("fk_type_annotation", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("fk_paragraphe", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("fk_auteur", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("texte", medit_db_tuple::DB_TUPLE_CHAINE);
		parent::__construct($id_annotation);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		$sql = "SELECT fk_type_annotation, fk_paragraphe, fk_auteur, texte ";
		$sql .= "FROM ".table("annotation")." ";
		$sql .= "WHERE id_annotation = :id_annotation";
		$requete = medit_db::Executer_requete($sql, array("id_annotation" => $this->id));
		if ($tuple = $requete->fetch()) {
			$this->ecrire_fk_type_annotation($tuple["fk_type_annotation"]);
			$this->ecrire_fk_paragraphe($tuple["fk_paragraphe"]);
			$this->ecrire_fk_auteur($tuple["fk_auteur"]);
			$this->ecrire_texte($tuple["texte"]);
		}
		parent::charger();
	}
}