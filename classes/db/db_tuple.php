<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion générique des tuples
 * Règles de nommage des classes filles :
 * medit_db_<nom de la table>
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_tuple {
	const DB_TUPLE_NULL_ID = -1;
	const DB_TUPLE_ENTIER = 0;
	const DB_TUPLE_CHAINE = 1;
	const DB_TUPLE_HORODATAGE = 2;
	const DB_TUPLE_BOOLEEN = 3;
	const DB_TUPLE_CLE_ETRANGERE = 5;
	const DB_TUPLE_LISTE = 9;

	protected $nom_table = null;
	protected $label_id = null;
	protected $id = self::DB_TUPLE_NULL_ID;
	protected $auto_chargement = true;
	protected $est_charge = false;
	
	private $proprietes_types = array();
	private $proprietes_valeurs = array();

	/**
	 * Constructeur
	 *
	 * @portée	publique
	 * @param	id_article	Identifiant de l'article
	 * @retour	vide
	 */
    protected function __construct($id) {
		$this->id = $id;
		$nom_classe = get_class($this);
		$this->nom_table = str_replace("medit_db_", "medit_".medit_config::DB_PREFIXE_TABLE, $nom_classe);
		$this->label_id = str_replace("medit_db_", "id_", $nom_classe);
	}

	/**
	 * Test de la présence du tuple en base de données
	 *
	 * @portée	publique
	 * @param	identifiant
	 * @retour	vide
	 */
	public function supprimer() {
		medit_db::Supprimer_tuple($this->label_id, $this->id, $this->nom_table);
	}

	/**
	 * Accesseurs
	 *
	 * @portée	publique
	 * @retour	propriété
	 */
	public function lire_id() {return $this->id;}
	public function lire_auto_chargement() {return $this->auto_chargement;}
	public function lire_est_charge() {return $this->est_charge;}
	
	/**
	 * Modifieur
	 *
	 * @portée	publique
	 * @param	valeur
	 * @retour	vide
	 */
	public function ecrire_auto_chargement($valeur) {$this->auto_chargement = $valeur;}
	
	/**
	 * Test de la présence du tuple en base de données
	 *
	 * @portée	publique
	 * @param	identifiant
	 * @retour	vide
	 */
	public function en_db() {
		$en_db = medit_db::Chercher_tuple($this->label_id, $this->id, $this->nom_table);
		return $en_db;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Déclaration d'une propriété pour cet objet
	 *
	 * @portée	protégée
	 * @param	nom de la propriété
	 * @param	type de la propriété
	 * @retour	vide
	 */
	protected function declarer($propriete, $type) {
		$this->proprietes_types[$propriete] = $type;
		switch($type) {
			case self::DB_TUPLE_CHAINE:
				$this->proprietes_valeurs[$propriete] = "";
				break;
			case self::DB_TUPLE_CLE_ETRANGERE:
				$this->proprietes_valeurs[$propriete] = self::DB_TUPLE_NULL_ID;
				break;
			case self::DB_TUPLE_LISTE:
				$this->proprietes_valeurs[$propriete] = array();
				break;
			default :
				$this->proprietes_valeurs[$propriete] = (int) 0;
		}
	}
	
	/**
	 * Déclenchement du chargement des données le cas échéant
	 *
	 * @portée	protégée
	 * @retour	vide
	 */
	protected function verifier_chargement() {
		if (($this->auto_chargement) && (!($this->est_charge))) {$this->charger();}
	}

	/**
	 * Mise à jour du drapeau de chargement à l'appel de la méthode
	 *
	 * @portée	protégée
	 * @retour	vide
	 */
	protected function charger() {
		$this->est_charge = true;
	}

	/**
	 * Accesseur générique pour les propriétés déclarées
	 *
	 * @portée	protégée
	 * @param	propriété à accéder
	 * @retour	valeur
	 */
	protected function lire($propriete) {
		if (!(isset($this->proprietes_types[$propriete]))) {
			die("Erreur : Appel de l'accesseur lire_{$propriete} non déclaré");
		}
		$this->verifier_chargement();
		switch($this->proprietes_types[$propriete]) {
			case self::DB_TUPLE_CHAINE:
				return (string) $this->proprietes_valeurs[$propriete];
				break;
			case self::DB_TUPLE_LISTE:
				return (array) $this->proprietes_valeurs[$propriete];
				break;
			case self::DB_TUPLE_BOOLEEN:
				return ($this->proprietes_valeurs[$propriete])?true:false;
				break;
			default :
				return (int) $this->proprietes_valeurs[$propriete];
		}
	}

	/**
	 * Modifieur générique direct pour les propriétés déclarées
	 *
	 * @portée	protégée
	 * @param	propriété à modifier
	 * @param	nouvelle valeur
	 * @retour	vide
	 */
	protected function ecrire($propriete, $valeur) {
		if (!(isset($this->proprietes_types[$propriete]))) {
			die("Erreur : Appel du modifieur ecrire_{$propriete}  non déclaré");
		}
		switch($this->proprietes_types[$propriete]) {
			case self::DB_TUPLE_CHAINE:
				$this->proprietes_valeurs[$propriete] = (string) $valeur;
				break;
			case self::DB_TUPLE_LISTE:
				$this->proprietes_valeurs[$propriete] = (array) $valeur;
				break;
			case self::DB_TUPLE_BOOLEEN:
				$this->proprietes_valeurs[$propriete] = ($valeur)?true:false;
				break;
			default :
				$this->proprietes_valeurs[$propriete] = (int) $valeur;
		}
	}

	protected function __call($methode, $arguments) {
		if (!(strncmp($methode, "lire_", 5))) {
			$propriete = substr($methode, 5);
			return $this->lire($propriete);
		}
		elseif (!(strncmp($methode, "ecrire_", 7))) {
			$propriete = substr($methode, 7);
			$this->ecrire($propriete, $arguments[0]);
		}
		else {die("Erreur : Appel d'une méthode inexistante : {$methode}");}
	}
}