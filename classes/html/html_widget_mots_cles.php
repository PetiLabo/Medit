<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}
	
/**
 * Classe d'affichage du widget des mots clés
 *
 * @auteurs		Philippe Gilles
 */
class medit_html_widget_mots_cles {
	
	public static function Generer_panneau($contenu = "") {
		$html = "<div id=\""._MEDIT_HTML_ID_PANNEAU_MOTS_CLES."\" class=\"medit_widget\">\n";
		$html .= self::Generer_barre_mots_cles();
		$html .= self::Generer_zone_mots_cles($contenu);
		$html .= "</div>\n";
		return $html;
	}
	
	public static function Generer_barre_mots_cles() {
		global $_MEDIT_LANGUE;

		$html = "<div id=\"barre_outils_mots_cles\" class=\"barre_outils_panneau\">";
		$html .= "<p class=\"p_outil_info p_outil_mots_cles_poignee\"><a class=\"fa fa-arrows icone_poignee_deplacement\"></a></p>\n";
		$html .= "<p class=\"p_outil_info p_outil_mots_cles_boutons\">";
		$html .= "<span class=\"s_outil_titre_mots_cles\">".$_MEDIT_LANGUE->titre_mots_cles()."</span>";
		$html .= "</p>\n";
		$html .= "</div>\n";
		return $html;
	}
	
	public static function Generer_zone_mots_cles($contenu = "") {
		$html = "<div id=\"zone_panneau_mots_cles\" class=\"cadre_mots_cles\">";
		$html .= "<p>".$contenu."</p>\n";
		$html .= "</div>\n";
		return $html;
	}
}