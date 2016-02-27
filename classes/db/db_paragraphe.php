<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des tuples de la table medit_paragraphe
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_paragraphe extends medit_db_tuple {

	/**
	 * Constructeur
	 *
	 * @portée	public
	 * @param	id_paragraphe	Identifiant du paragraphe
	 * @retour	vide
	 */
    public function __construct($id_paragraphe) {
		$this->declarer("niveau", medit_db_tuple::DB_TUPLE_ENTIER);
		$this->declarer("horodatage", medit_db_tuple::DB_TUPLE_HORODATAGE);
		$this->declarer("texte", medit_db_tuple::DB_TUPLE_CHAINE);
		$this->declarer("verrou", medit_db_tuple::DB_TUPLE_BOOLEEN);
		$this->declarer("fk_verrou", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		$this->declarer("fk_auteur", medit_db_tuple::DB_TUPLE_CLE_ETRANGERE);
		parent::__construct($id_paragraphe);
	}

	/**
	 * Chargeur
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function charger() {
		$sql = "SELECT niveau, horodatage, texte, verrou, fk_verrou, fk_auteur ";
		$sql .= "FROM ".table("paragraphe")." ";
		$sql .= "WHERE id_paragraphe = :id_paragraphe";
		$requete = medit_db::Executer_requete($sql, array("id_paragraphe" => $this->id));
		if ($tuple = $requete->fetch()) {
			$this->ecrire_niveau($tuple["niveau"]);
			$this->ecrire_horodatage($tuple["horodatage"]);
			$this->ecrire_texte($tuple["texte"]);
			$this->ecrire_verrou($tuple["verrou"]);
			$this->ecrire_fk_verrou($tuple["fk_verrou"]);
			$this->ecrire_fk_auteur($tuple["fk_auteur"]);
		}
		parent::charger();
	}
	
	/**
	 * Mettre à jour la contribution d'un paragraphe
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function modifier_texte($texte, $id_auteur) {
		// Dans la BDD
		$sql = "UPDATE ".table("paragraphe")." ";
		$sql .= "SET texte = :texte, horodatage = NOW(), fk_auteur = :id_auteur ";
		$sql .= "WHERE id_paragraphe = :id_paragraphe";
		medit_db::Executer_requete($sql, array("texte" => $texte, "id_auteur" => $id_auteur, "id_paragraphe" => $this->id));
		
		// Dans la classe
		$this->ecrire_texte($texte);
		$this->ecrire_fk_auteur($id_auteur);
		
		// Annotations
		$liste_annotations = $this->parser_liste_annotations();
		medit_db_table_annotation::Mettre_a_jour_paragraphe($this->id, $liste_annotations);
	}

	/**
	 * Déverrouillage du paragraphe
	 *
	 * @portée	public
	 * @retour	vide
	 */
	public function deverrouiller() {
		// Dans la BDD
		$sql = "UPDATE ".table("paragraphe")." ";
		$sql .= "SET verrou = 0, fk_verrou = 0 ";
		$sql .= "WHERE id_paragraphe = :id_paragraphe";
		medit_db::Executer_requete($sql, array("id_paragraphe" => $this->id));

		// Dans la classe
		$this->ecrire_verrou(false);
		$this->ecrire_fk_verrou(0);
	}
	
	/**
	 * Retourne la liste des annotateurs d'un paragraphe
	 *
	 * @portée	public
	 * @retour	liste des identifiants des annotateurs
	 */
	public function lire_liste_annotateurs() {
		$sql = "SELECT DISTINCT fk_auteur ";
		$sql .= "FROM ".table("annotation")." ";
		$sql .= "WHERE fk_paragraphe = :id_paragraphe";
		$requete = medit_db::Executer_requete($sql, array("id_paragraphe" => $this->id));
		$liste = array();
		while ($tuple = $requete->fetch()) {
			$fk_auteur = (int) $tuple["fk_auteur"];
			$liste[] = $fk_auteur;
		}
		return $liste;
	}
	
	/**
	 * Retourne la liste des commentateurs d'un paragraphe
	 *
	 * @portée	public
	 * @retour	liste des identifiants des annotateurs
	 */
	public function lire_liste_commentateurs() {
		$sql = "SELECT DISTINCT fk_auteur ";
		$sql .= "FROM ".table("discussion")." INNER JOIN ".table("commentaire")." ON ".table("discussion").".fk_fil_discussion = ".table("commentaire").".fk_fil_discussion ";
		$sql .= "WHERE fk_paragraphe = :id_paragraphe";
		$requete = medit_db::Executer_requete($sql, array("id_paragraphe" => $this->id));
		$liste = array();
		while ($tuple = $requete->fetch()) {
			$fk_auteur = (int) $tuple["fk_auteur"];
			$liste[] = $fk_auteur;
		}
		return $liste;
	}
	
	/**
	 * Retourne la liste des types d'annotation d'un paragraphe
	 *
	 * @portée	public
	 * @retour	liste des identifiants des types d'annotation
	 */
	public function lire_liste_types_annotation() {
		$sql = "SELECT DISTINCT fk_type_annotation ";
		$sql .= "FROM ".table("annotation")." ";
		$sql .= "WHERE fk_paragraphe = :id_paragraphe";
		$requete = medit_db::Executer_requete($sql, array("id_paragraphe" => $this->id));
		$liste = array();
		while ($tuple = $requete->fetch()) {
			$fk_type_annotation = (int) $tuple["fk_type_annotation"];
			$liste[] = $fk_type_annotation;
		}
		return $liste;
	}
	
	/**
	 * Déduit des spans HTML les infos à stocker dans la table annotations
	 *
	 * @portée	public
	 * @retour	liste des infos sur les annotations du paragraphe
	 */	
	private function parser_liste_annotations() {
		$liste = array();
		$auteur_annotation = "id_auteur_";
		$classe_annotation = "style_annotation";
		$dom_texte = new DOMDocument();
		
		// Chargement avec erreurs désactivées pour ne pas perturber le flux de sortie
		libxml_use_internal_errors(true);
		$html = sprintf("<?xml encoding=\"UTF-8\"><div>%s</div>", $this->lire_texte());
		@$dom_texte->loadHTML($html);
		libxml_clear_errors();
		$dom_texte->encoding = "UTF-8";

		$liste_spans = $dom_texte->getElementsByTagName("span");
		foreach ($liste_spans as $span) {
			// On vérifie qu'on est dans une classe de type annotation
			$classe = $span->getAttribute("class");
			if (!(strncmp($classe, $classe_annotation, strlen($classe_annotation)))) {
				$pos_classe_annotation = strpos($classe, $classe_annotation."_");
				if ($pos_classe_annotation !== false) {
					$pos_id_annotation = 1 + $pos_classe_annotation + strlen($classe_annotation);
					$id_annotation = (int) substr($classe, $pos_id_annotation);
					if ($id_annotation > 0) {
						$auteur = $span->getAttribute("name");
						if (!(strncmp($auteur, $auteur_annotation, strlen($auteur_annotation)))) {
							$pos_id_auteur = strlen($auteur_annotation);
							$id_auteur = (int) substr($auteur, $pos_id_auteur);
							if ($id_auteur > 0) {
								$texte = $span->nodeValue;
								$liste[] = array("id_auteur" => $id_auteur, "id_annotation" => $id_annotation, "texte" => $texte);
							}
						}
					}
				}
			}
		}
		return $liste;
	}
}