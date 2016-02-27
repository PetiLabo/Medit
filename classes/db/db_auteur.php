<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_auteur
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_auteur extends medit_db_tuple {

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_auteur	Identifiant de l'auteur
	 * @retour	vide
	 */
    public function __construct($id_auteur) {
		$this->declarer("identifiant", medit_db_tuple::DB_TUPLE_CHAINE);
		$this->declarer("pseudo", medit_db_tuple::DB_TUPLE_CHAINE);
		$this->declarer("prenom", medit_db_tuple::DB_TUPLE_CHAINE);
		$this->declarer("nom", medit_db_tuple::DB_TUPLE_CHAINE);
		$this->declarer("email", medit_db_tuple::DB_TUPLE_CHAINE);
		$this->declarer("mot_de_passe", medit_db_tuple::DB_TUPLE_CHAINE);
		$this->declarer("derniere_connexion", medit_db_tuple::DB_TUPLE_HORODATAGE);
		$this->declarer("fk_profil", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		parent::__construct($id_auteur);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		$sql = "SELECT pseudo, prenom, nom, email, mot_de_passe, derniere_connexion, fk_profil ";
		$sql .= "FROM ".table("auteur")." ";
		$sql .= "WHERE id_auteur = :id_auteur";
		$requete = medit_db::Executer_requete($sql, array("id_auteur" => $this->id));
		if ($tuple = $requete->fetch()) {
			$this->ecrire_pseudo($tuple["pseudo"]);
			$this->ecrire_prenom($tuple["prenom"]);
			$this->ecrire_nom($tuple["nom"]);
			$this->ecrire_email($tuple["email"]);
			$this->ecrire_mot_de_passe($tuple["mot_de_passe"]);
			$this->ecrire_derniere_connexion($tuple["derniere_connexion"]);
			$this->ecrire_fk_profil($tuple["fk_profil"]);
		}
		parent::charger();
	}
}