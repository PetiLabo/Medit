<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}
	
/**
 * Classe d'affichage du widget journal
 *
 * @auteurs		Philippe Gilles
 */
class medit_html_widget_journal {
	private static $Liste_dimensions = array(3, 5, 8, 10, 15);

	public static function Generer_panneau($id_article) {
		$html = "<div id=\""._MEDIT_HTML_ID_PANNEAU_JOURNAL."\" class=\"medit_widget\">\n";
		$html .= self::Generer_barre_journal($id_article);
		$html .= self::Generer_zone_journal();
		$html .= "</div>\n";
		return $html;
	}

	public static function Generer_barre_journal($id_article) {
		global $_MEDIT_LANGUE;

		$html = "<div id=\"barre_outils_journal\" class=\"barre_outils_panneau\">";
		$html .= "<p class=\"p_outil_info p_outil_journal_poignee\"><a class=\"fa fa-arrows icone_poignee_deplacement\"></a></p>\n";
		$html .= "<p class=\"p_outil_info p_outil_journal_boutons\">";
		$html .= "<span class=\"s_outil_titre_journal\">".$_MEDIT_LANGUE->titre_journal()."</span>";
		$html .= "<span class=\"fa fa-arrows-v s_icone_dimension_journal\"></span>";
		// Dimension
		$dimension_selectionnee = self::Lire_dimension_journal($id_article);
		$html .= "<select id=\"s_outil_dimension_journal\">";
		foreach (self::$Liste_dimensions as $dimension) {
			$attr_selected = ($dimension == $dimension_selectionnee)?" selected=\"selected\"":"";
			$html .= "<option".$attr_selected.">{$dimension}</option>";
		}
		$html .= "</select>";
		// Article seulement
		$html .= "<span class=\"s_label_article_journal\">".$_MEDIT_LANGUE->article_uniquement()."</span>";
		$html .= "<input id=\"i_outil_case_article_journal\" type=\"checkbox\" />";
		$html .= "</p>\n";
		$html .= "</div>\n";
		return $html;
	}
	
	
	public static function Generer_zone_journal($contenu = "") {
		$html = "<div id=\"zone_panneau_journal\" class=\"cadre_journal\">";
		$html .= $contenu;
		$html .= "</div>\n";
		return $html;
	}

	public static function Generer_liste_evenements($id_article, $id_journal_debut, $id_journal_fin) {
		global $_MEDIT_LANGUE;

		$html = "";
		for ($id_journal = 1+$id_journal_debut;$id_journal <= $id_journal_fin;$id_journal += 1) {
			$journal = medit_db_table_journal::Construire($id_journal);
			$type_evenement = $journal->lire_type_evenement();
			$horodatage = $journal->lire_horodatage();
			$id_auteur = $journal->lire_fk_auteur();
			$auteur = medit_db_table_auteur::Construire($id_auteur);
			$pseudo = ($auteur->en_db())?$auteur->lire_pseudo():$_MEDIT_LANGUE->pseudo_inconnu();
			$journaliser = true;
			switch($type_evenement) {
				case medit_db_table_journal::DB_JOURNAL_CONNEXION:
					$fa_icone = "fa-sign-in";
					$str_evenement = $_MEDIT_LANGUE->evenement_connexion();
					break;
				case medit_db_table_journal::DB_JOURNAL_DECONNEXION:
					$fa_icone = "fa-sign-out";
					$str_evenement = $_MEDIT_LANGUE->evenement_deconnexion();
					break;
				case medit_db_table_journal::DB_JOURNAL_VERROUILLAGE_PARAGRAPHE:
					$fa_icone = "fa-lock";
					$str_evenement = $_MEDIT_LANGUE->evenement_verrouillage_paragraphe();
					break;
				case medit_db_table_journal::DB_JOURNAL_DEVERROUILLAGE_PARAGRAPHE:
					$fa_icone = "fa-unlock";
					$str_evenement = $_MEDIT_LANGUE->evenement_deverrouillage_paragraphe();
					break;
				case medit_db_table_journal::DB_JOURNAL_EDITION_PARAGRAPHE:
					$fa_icone = "fa-pencil";
					$str_evenement = $_MEDIT_LANGUE->evenement_edition_paragraphe();
					break;
				case medit_db_table_journal::DB_JOURNAL_ANNOTATION_PARAGRAPHE:
					$fa_icone = "fa-file-text";
					$str_evenement = $_MEDIT_LANGUE->evenement_annotation_paragraphe();
					break;
				case medit_db_table_journal::DB_JOURNAL_CREATION_FIL_DISCUSSION:
					$fa_icone = "fa-commenting";
					$str_evenement = $_MEDIT_LANGUE->evenement_creation_fil_discussion();
					break;
				case medit_db_table_journal::DB_JOURNAL_REPONSE_COMMENTAIRE:
					$fa_icone = "fa-reply";
					$str_evenement = $_MEDIT_LANGUE->evenement_reponse_commentaire();
					break;
				default:
					$journaliser = false;
					break;
			}
			if ($journaliser) {
				$heure_horodatage = formatter_horodatage($horodatage);
				$html .= "<p class=\"evenement_journal\"><span class=\"horodatage_journal\">{$heure_horodatage}</span> - {$pseudo} - <span class=\"fa {$fa_icone} icone_journal\"></span> - {$str_evenement}</p>";
			}
		}
		return $html;
	}
	
	public static function Lire_dimension_journal($id_article) {
		$ret = medit_uc_filtre::Lire_critere_filtre($id_article, medit_uc_filtre::UC_FILTRE_JOURNAL_DIMENSION);
		if (!(in_array($ret, self::$Liste_dimensions))) {$ret = self::$Liste_dimensions[0];}
		return $ret;
	}
}