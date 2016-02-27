<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_version
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_version extends medit_db_tuple {
	
	private $tab_sommaire = array();
	private $tab_id_paragraphe_to_sommaire = array();

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_version	Identifiant de la version
	 * @retour	vide
	 */
    public function __construct($id_version) {
		$this->declarer("no_version", medit_db_tuple::DB_TUPLE_ENTIER);
		$this->declarer("no_sous_version", medit_db_tuple::DB_TUPLE_ENTIER);
		$this->declarer("horodatage", medit_db_tuple::DB_TUPLE_HORODATAGE);
		$this->declarer("fk_article", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("fk_auteur", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		parent::__construct($id_version);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		// Chargement des champs de la table version
		$sql = "SELECT no_version, no_sous_version, UNIX_TIMESTAMP(horodatage) AS 'horodatage', fk_article, fk_auteur ";
		$sql .= "FROM ".table("version")." ";
		$sql .= "WHERE id_version = :id_version";
		$requete = medit_db::Executer_requete($sql, array("id_version" => $this->id));
		if ($tuple = $requete->fetch()) {
			$this->ecrire_no_version($tuple["no_version"]);
			$this->ecrire_no_sous_version($tuple["no_sous_version"]);
			$this->ecrire_horodatage($tuple["horodatage"]);
			$this->ecrire_fk_article($tuple["fk_article"]);
			$this->ecrire_fk_auteur($tuple["fk_auteur"]);
		}
		
		// Chargement du sommaire associé
		// Ce sommaire est stable pour une version donnée : 
		// toute modification de niveau ou d'ordre entraîne la création d'une nouvelle version
		$sql = "SELECT id_paragraphe, niveau, texte ";
		$sql .= "FROM ".table("composition")." INNER JOIN ".table("paragraphe")." ON fk_paragraphe = id_paragraphe ";
		$sql .= "WHERE fk_version = :id_version ";
		$sql .= "ORDER BY no_ordre";
		$requete = medit_db::Executer_requete($sql, array("id_version" => $this->id));
		$no_sommaire = -1;
		$numerotation_sommaire = array();
		$niveau_numerotation = 0;
		while ($tuple = $requete->fetch()) {
			$id_paragraphe = (int) $tuple["id_paragraphe"];
			$niveau = (int) $tuple["niveau"];
			$texte = $tuple["texte"];
			if ($niveau > 0) {
				$no_sommaire += 1;
				if ($niveau == $niveau_numerotation) {
					$numerotation_sommaire[$niveau] += 1;
				}
				else if ($niveau > $niveau_numerotation) {
					for ($cpt = $niveau_numerotation + 1;$cpt < $niveau;$cpt += 1) {
						$numerotation_sommaire[$cpt] = 0;
					}
					$numerotation_sommaire[$niveau] = 1;
					$niveau_numerotation = $niveau;
				}
				else {
					for ($cpt = $niveau + 1;$cpt <= $niveau_numerotation;$cpt += 1) {
						unset($numerotation_sommaire[$cpt]);
					}
					$numerotation_sommaire[$niveau] += 1;
					$niveau_numerotation = $niveau;
				}
				$str_numerotation = $this->tab_to_str_numerotation($numerotation_sommaire);
				$str_titre = $this->strip_texte($texte, 25);
				$this->tab_sommaire[$no_sommaire] = array("id_paragraphe" => $id_paragraphe, "niveau" => $niveau, "numerotation" => $str_numerotation, "texte" => $str_titre);
			}
			$this->tab_id_paragraphe_to_sommaire[$id_paragraphe] = $no_sommaire;
		}
		
		// Appel de la méthode parente
		parent::charger();
	}

	/**
	 * Accesseur au sommaire
	 *
	 * @portée	public
	 * @retour	sommaire
	 */
	public function lire_sommaire() {
		$this->verifier_chargement();
		return $this->tab_sommaire;
	}

	/**
	 * Retourne la liste des fils de discussions attachés à une version
	 *
	 * @portée	public
	 * @retour	sommaire
	 */
	public function lire_fils_discussion() {
		$sql = "SELECT DISTINCT fk_fil_discussion ";
		$sql .= "FROM (".table("composition")." INNER JOIN ".table("paragraphe")." ON ".table("composition").".fk_paragraphe = id_paragraphe) ";
		$sql .= "INNER JOIN ".table("discussion")." ON ".table("discussion").".fk_paragraphe = id_paragraphe ";
		$sql .= "WHERE fk_version = :id_version ";
		$sql .= "ORDER BY no_ordre";
		$requete = medit_db::Executer_requete($sql, array("id_version" => $this->id));
		$liste = array();
		while ($tuple = $requete->fetch()) {
			$liste[] = (int) $tuple["fk_fil_discussion"];
		}
		return $liste;
	}
	
	/**
	 * Changement des numéros de version et sous_version
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function ecrire_nouvelle_version($no_version, $no_sous_version, $id_auteur) {
		/* Côté BDD */
		$sql = "UPDATE ".table("version")." ";
		$sql .= "SET no_version = :no_version, no_sous_version = :no_sous_version, horodatage = NOW(), fk_auteur = :id_auteur ";
		$sql .= "WHERE id_version = :id_version";
		medit_db::Executer_requete($sql, array("id_version" => $this->id, "no_version" => $no_version, "no_sous_version" => $no_sous_version, "id_auteur" => $id_auteur));

		/* Côté classe */
		$this->ecrire_no_version($no_version);
		$this->ecrire_no_sous_version(0);
		$this->ecrire_horodatage(time());
		$this->ecrire_fk_auteur($id_auteur);
	}
	
	/**
	 * Fonction de service pour la numérotation des sommaires
	 *
	 * @portée	privée
	 * @param	numérotation sous forme de tableau
	 * @retour	numérotation sous forme de chaîne de caractères
	 */
	private function tab_to_str_numerotation($tab_numerotation) {
		$ret = "";
		foreach ($tab_numerotation as $numero) {
			if (strlen($ret) > 0) {$ret .= ".";}
			$ret .= $numero;
		}
		return $ret;
	}
	
	/**
	 * Fonction de service pour le tronquage des textes des titres
	 *
	 * @portée	privée
	 * @param	numérotation sous forme de tableau
	 * @retour	numérotation sous forme de chaîne de caractères
	 */
	private function strip_texte($texte, $lng_max) {
		$ret = trim(strip_tags($texte));
		if (strlen($ret) > $lng_max) {
			$ret = substr($ret, 0, ($lng_max - 5))."[...]";
		}
		return $ret;
	}
}