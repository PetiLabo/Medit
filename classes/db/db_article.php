<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_article
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_article extends medit_db_tuple {
	private $multiplicateur_sous_version = 10000;
	private $no_derniere_version = array(0, 0);
	private $tab_no_to_id_version = array();

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_article	Identifiant de l'article
	 * @retour	vide
	 */
    public function __construct($id_article) {
		$this->declarer("nom", medit_db_tuple::DB_TUPLE_CHAINE);
		$this->declarer("liste_versions", medit_db_tuple::DB_TUPLE_LISTE);
		parent::__construct($id_article);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		// Chargement des champs de la table article
		$sql = "SELECT nom ";
		$sql .= "FROM ".table("article")." ";
		$sql .= "WHERE id_article = :id_article";
		$requete = medit_db::Executer_requete($sql, array("id_article" => $this->id));
		if ($tuple = $requete->fetch()) {$this->ecrire_nom($tuple["nom"]);}

		// Chargement de la liste des versions de l'article
		$sql = "SELECT id_version, no_version, no_sous_version ";
		$sql .= "FROM ".table("version")." ";
		$sql .= "WHERE fk_article = :id_article ";
		$sql .= "ORDER BY no_version, no_sous_version";
		$requete = medit_db::Executer_requete($sql, array("id_article" => $this->id));
		$liste = array();
		while ($tuple = $requete->fetch()) {
			$id_version = (int) $tuple["id_version"];
			$no_version = (int) $tuple["no_version"];
			$no_sous_version = (int) $tuple["no_sous_version"];
			$couple_version = array($no_version, $no_sous_version);
			$liste[] = $id_version;
			$this->ajouter_version($couple_version, $id_version);
		}
		$this->no_derniere_version = $couple_version;
		$this->ecrire_liste_versions($liste);
		
		// Appel de la méthode parente
		parent::charger();
	}

	/**
	 * Liste des auteurs ayant contribué à l'article
	 *
	 * @portée	public
	 * @retour	liste des auteurs
	 */
	public function lire_liste_auteurs() {
		$sql = "SELECT DISTINCT id_auteur, pseudo ";
		$sql .= "FROM ".table("version")." INNER JOIN ".table("auteur")." ON fk_auteur = id_auteur ";
		$sql .= "WHERE fk_article = :id_article ";
		$sql .= "ORDER BY pseudo";
		$requete = medit_db::Executer_requete($sql, array("id_article" => $this->id));
		$liste = array();
		while ($tuple = $requete->fetch()) {
			$id_auteur = (int) $tuple["id_auteur"];
			$pseudo = $tuple["pseudo"];
			$liste[$id_auteur] = $pseudo;
		}
		return $liste;
	}

	/**
	 * Liste des auteurs ayant annoté l'article
	 *
	 * @portée	public
	 * @retour	liste des auteurs
	 */
	public function lire_liste_annotateurs() {
		$sql = "SELECT DISTINCT id_auteur, pseudo ";
		$sql .= "FROM (((".table("version")." INNER JOIN ".table("composition")." ON fk_version = id_version) ";
		$sql .= "INNER JOIN ".table("paragraphe")." ON ".table("composition").".fk_paragraphe = id_paragraphe) ";
		$sql .= "INNER JOIN ".table("annotation")." ON ".table("annotation").".fk_paragraphe = id_paragraphe) ";
		$sql .= "INNER JOIN ".table("auteur")." ON ".table("annotation").".fk_auteur = id_auteur ";
		$sql .= "WHERE fk_article = :id_article ";
		$sql .= "ORDER BY pseudo";
		$requete = medit_db::Executer_requete($sql, array("id_article" => $this->id));
		$liste = array();
		while ($tuple = $requete->fetch()) {
			$id_auteur = (int) $tuple["id_auteur"];
			$pseudo = $tuple["pseudo"];
			$liste[$id_auteur] = $pseudo;
		}
		return $liste;
	}

	/**
	 * Liste des auteurs ayant annoté l'article
	 *
	 * @portée	public
	 * @retour	liste des auteurs
	 */
	public function lire_liste_types_annotation() {
		$sql = "SELECT DISTINCT id_type_annotation, label_annotation ";
		$sql .= "FROM (((".table("version")." INNER JOIN ".table("composition")." ON fk_version = id_version) ";
		$sql .= "INNER JOIN ".table("paragraphe")." ON ".table("composition").".fk_paragraphe = id_paragraphe) ";
		$sql .= "INNER JOIN ".table("annotation")." ON ".table("annotation").".fk_paragraphe = id_paragraphe) ";
		$sql .= "INNER JOIN ".table("type_annotation")." ON ".table("annotation").".fk_type_annotation = id_type_annotation ";
		$sql .= "WHERE ".table("version").".fk_article = :id_article ";
		$sql .= "ORDER BY label_annotation";
		$requete = medit_db::Executer_requete($sql, array("id_article" => $this->id));
		$liste = array();
		while ($tuple = $requete->fetch()) {
			$id_type_annotation = (int) $tuple["id_type_annotation"];
			$label_annotation = $tuple["label_annotation"];
			$liste[$id_type_annotation] = $label_annotation;
		}
		return $liste;
	}

	/**
	 * Liste des auteurs ayant commenté l'article
	 *
	 * @portée	public
	 * @retour	liste des auteurs
	 */
	public function lire_liste_commentateurs() {
		$sql = "SELECT DISTINCT id_auteur, pseudo ";
		$sql .= "FROM ((((".table("version")." INNER JOIN ".table("composition")." ON fk_version = id_version) ";
		$sql .= "INNER JOIN ".table("paragraphe")." ON ".table("composition").".fk_paragraphe = id_paragraphe) ";
		$sql .= "INNER JOIN ".table("discussion")." ON ".table("discussion").".fk_paragraphe = id_paragraphe) ";
		$sql .= "INNER JOIN ".table("commentaire")." ON ".table("commentaire").".fk_fil_discussion = ".table("discussion").".fk_fil_discussion) ";
		$sql .= "INNER JOIN ".table("auteur")." ON ".table("commentaire").".fk_auteur = id_auteur ";
		$sql .= "WHERE fk_article = :id_article ";
		$sql .= "ORDER BY pseudo";
		$requete = medit_db::Executer_requete($sql, array("id_article" => $this->id));
		$liste = array();
		while ($tuple = $requete->fetch()) {
			$id_auteur = (int) $tuple["id_auteur"];
			$pseudo = $tuple["pseudo"];
			$liste[$id_auteur] = $pseudo;
		}
		return $liste;
	}

	/**
	 * Insertion d'une nouvelle sous_version de l'article
	 *
	 * @portée	publique
	 * @param	identifiant de l'auteur de cette sous-version
	 * @retour	couple no_version/no_sous_version créé
	 */	
	public function inserer_nouvelle_sous_version($id_auteur) {
		$this->verifier_chargement();
		$couple_version = $this->no_derniere_version;
		// TODO : Vérifier qu'on ne dépasse pas la valeur du multiplicateur
		$couple_version[1] += 1;
		$id_nouvelle_version = (int) medit_db_table_version::Inserer($this->id, $couple_version, $id_auteur);
		if ($id_nouvelle_version != medit_db_tuple::DB_TUPLE_NULL_ID) {
			$this->ajouter_version($couple_version, $id_nouvelle_version);
			$liste = $this->lire_liste_versions();
			$liste[] = $id_nouvelle_version;
			$this->ecrire_liste_versions($liste);
			$this->no_derniere_version = $couple_version;
		}
		return $couple_version;
	}

	/**
	 * Conversion d'un couple no_version/no_sous_version en id_version
	 *
	 * @portée	publique
	 * @param	couple no_version/no_sous_version
	 * @retour	identifiant de la version correspondante
	 */
	public function no_to_id_version($couple_version) {
		$this->verifier_chargement();
		$no_version = (int) $couple_version[0];
		$no_sous_version = (int) $couple_version[1];
		$index = (int) ((($this->multiplicateur_sous_version) * $no_version) + $no_sous_version);
		if (isset($this->tab_no_to_id_version[$index])) {
			return (int) $this->tab_no_to_id_version[$index];
		}
		else {
			return medit_db_tuple::DB_TUPLE_NULL_ID;
		}
	}
	
	/**
	 * Accesseurs pour le couple correspondant à la dernière version de l'article
	 *
	 * @portée	publique
	 * @retour	couple no_version/no_sous_version
	 */
	public function lire_no_derniere_version() {
		$this->verifier_chargement();
		return $this->no_derniere_version;
	}
	public function lire_version_precedente($id_version) {
		$this->verifier_chargement();
		$liste = $this->lire_liste_versions();
		$index = array_search($id_version, $liste);
		if (($index === false) || ($index == 0)) {return 0;}
		$index_precedent = $index - 1;
		$id_precedent = $liste[$index_precedent];
		return $id_precedent;
	}
	public function lire_version_suivante($id_version) {
		$this->verifier_chargement();
		$liste = $this->lire_liste_versions();
		$index = array_search($id_version, $liste);
		if ($index === false) {return 0;}
		$index_suivant = $index + 1;
		if ($index_suivant == count($liste)) {return 0;}
		$id_suivant = $liste[$index_suivant];
		return $id_suivant;
	}

	/**
	 * Ajout d'une version dans le tableau de conversion
	 *
	 * @portée	privée
	 * @param	couple no_version/no_sous_version
	 * @param	identifiant de la version
	 * @retour	vide
	 */
	private function ajouter_version($couple_version, $id_version) {
		$no_version = (int) $couple_version[0];
		$no_sous_version = (int) $couple_version[1];
		$index = (int) ((($this->multiplicateur_sous_version) * $no_version) + $no_sous_version);
		$this->tab_no_to_id_version[$index] = $id_version;
	}
}