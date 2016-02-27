<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_commentaire
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_commentaire extends medit_db_tuple {

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_commentaire	Identifiant du commentaire
	 * @retour	vide
	 */
    public function __construct($id_commentaire) {
		$this->declarer("niveau", medit_db_tuple::DB_TUPLE_ENTIER);
		$this->declarer("horodatage", medit_db_tuple::DB_TUPLE_HORODATAGE);
		$this->declarer("texte", medit_db_tuple::DB_TUPLE_CHAINE);
		$this->declarer("fk_commentaire", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("fk_auteur", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("fk_fil_discussion", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		parent::__construct($id_commentaire);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		$sql = "SELECT niveau, UNIX_TIMESTAMP(horodatage) AS 'horodatage', texte, fk_commentaire, fk_auteur, fk_fil_discussion ";
		$sql .= "FROM ".table("commentaire")." ";
		$sql .= "WHERE id_commentaire = :id_commentaire";
		$requete = medit_db::Executer_requete($sql, array("id_commentaire" => $this->id));
		if ($tuple = $requete->fetch()) {
			$this->ecrire_niveau($tuple["niveau"]);
			$this->ecrire_horodatage($tuple["horodatage"]);
			$this->ecrire_texte($tuple["texte"]);
			$this->ecrire_fk_commentaire($tuple["fk_commentaire"]);
			$this->ecrire_fk_auteur($tuple["fk_auteur"]);
			$this->ecrire_fk_fil_discussion($tuple["fk_fil_discussion"]);
		}
		parent::charger();
	}
}