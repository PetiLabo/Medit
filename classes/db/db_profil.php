<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_profil
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_profil extends medit_db_tuple {

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_profil	Identifiant du profil
	 * @retour	vide
	 */
    public function __construct($id_profil) {
		$this->declarer("nom_profil", medit_db_tuple::DB_TUPLE_CHAINE);
		$this->declarer("droit_mode_edition", medit_db_tuple::DB_TUPLE_BOOLEEN);
		$this->declarer("droit_edition", medit_db_tuple::DB_TUPLE_BOOLEEN);
		$this->declarer("droit_commentaire", medit_db_tuple::DB_TUPLE_BOOLEEN);
		$this->declarer("droit_annotation", medit_db_tuple::DB_TUPLE_BOOLEEN);
		$this->declarer("droit_creation_version", medit_db_tuple::DB_TUPLE_BOOLEEN);
		$this->declarer("droit_creation_fil_discussion", medit_db_tuple::DB_TUPLE_BOOLEEN);
		parent::__construct($id_profil);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		$sql = "SELECT nom_profil, droit_mode_edition, droit_edition, droit_commentaire, droit_annotation, droit_creation_version, droit_creation_fil_discussion ";
		$sql .= "FROM ".table("profil")." ";
		$sql .= "WHERE id_profil = :id_profil";
		$requete = medit_db::Executer_requete($sql, array("id_profil" => $this->id));
		if ($tuple = $requete->fetch()) {
			$this->ecrire_nom_profil($tuple["nom_profil"]);
			$this->ecrire_droit_mode_edition($tuple["droit_mode_edition"]);
			$this->ecrire_droit_edition($tuple["droit_edition"]);
			$this->ecrire_droit_commentaire($tuple["droit_commentaire"]);
			$this->ecrire_droit_annotation($tuple["droit_annotation"]);
			$this->ecrire_droit_creation_version($tuple["droit_creation_version"]);
			$this->ecrire_droit_creation_fil_discussion($tuple["droit_creation_fil_discussion"]);
		}
		parent::charger();
	}
}