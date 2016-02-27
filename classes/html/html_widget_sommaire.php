<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}
	
/**
 * Classe d'affichage du widget sommaire
 *
 * @auteurs		Philippe Gilles
 */
class medit_html_widget_sommaire {
	
	public static function Generer_panneau($id_version) {
		$html = "<div id=\""._MEDIT_HTML_ID_PANNEAU_SOMMAIRE."\" class=\"medit_widget\">\n";
		$html .= self::Generer_barre_sommaire();
		$version = medit_db_table_version::Construire($id_version);
		$html .= self::Generer_table_des_matieres($version->lire_sommaire());
		$html .= "</div>\n";
		return $html;
	}

	public static function Generer_barre_sommaire() {
		global $_MEDIT_LANGUE;

		$html = "<div id=\"barre_outils_sommaire\" class=\"barre_outils_panneau\">";
		$html .= "<p class=\"p_outil_info p_outil_sommaire_poignee\"><a class=\"fa fa-arrows icone_poignee_deplacement\"></a></p>\n";
		$html .= "<p class=\"p_outil_info p_outil_sommaire_boutons\">";
		$html .= "<span class=\"s_outil_titre_sommaire\">".$_MEDIT_LANGUE->titre_sommaire()."</span>";
		$html .= "</p>\n";
		$html .= "</div>\n";
		return $html;
	}

	public static function Generer_table_des_matieres($tab_sommaire) {
		global $_MEDIT_LANGUE;

		$html = "<div id=\"zone_panneau_sommaire\" class=\"cadre_sommaire\">";
		foreach($tab_sommaire as $entree_sommaire) {
			$html .= "<p class=\"entree_sommaire entree_sommaire_".$entree_sommaire["niveau"]."\"><a class=\"lien_entree_sommaire\" href=\"#p_".$entree_sommaire["id_paragraphe"]."\" title=\"".$_MEDIT_LANGUE->atteindre_entree_sommaire()."\">".$entree_sommaire["numerotation"]."</a> ".$entree_sommaire["texte"]."</p>";
		}
		$html .= "</div>";
		return $html;
	}
}