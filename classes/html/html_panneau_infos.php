<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}
	
/**
 * Classe d'affichage du panneau d'informations
 *
 * @auteurs		Philippe Gilles
 */
class medit_html_panneau_infos {
	
	public static function Generer_panneau() {
		$html = "<div class=\"d_barre_outils_info\">";
		$html .= self::Generer_panneau_mode_edition();
		$html .= self::Generer_panneau_connexion();
		$html .= "</div>\n";
		return $html;
	}
	
	public static function Generer_panneau_mode_edition() {
		global $_MEDIT_LANGUE;

		// Vérification des droits
		$edition_disabled = "";$label_disabled = "";
		$autorisation_mode_edition = medit_uc_autorisation::Lire_autorisation_connexion(medit_uc_autorisation::DB_DROIT_MODE_EDITION);
		if (!($autorisation_mode_edition)) {
			medit_uc_mode::Ecrire_mode(medit_uc_mode::UC_MODE_PAR_DEFAUT);
			$edition_disabled = " disabled=\"disabled\"";
			$label_disabled = " s_label_radio_disabled";
		}

		// Positionnement du bouton radio en fonction du mode
		$mode = medit_uc_mode::Lire_mode();
		if ($mode == medit_uc_mode::UC_MODE_EDITION) {
			$checked_edition = " checked=\"checked\"";
			$checked_historique = "";
		}
		else {
			$checked_edition = "";
			$checked_historique = " checked=\"checked\"";
		}

		$html = "<p class=\"p_outil_info p_outil_info_mode\">";
		$html .= "<a href=\"index.php\" title=\"".$_MEDIT_LANGUE->retour_page_accueil()."\">";
		$html .= "<img id=\"logo_medit\" src=\""._MEDIT_RACINE."/images/logo-medit-4.png\" alt=\"Logo Medit\" /></a>";
		$html .= "<input id=\"i_radio_edition\" class=\"i_radio_mode\" type=\"radio\" name=\"mode\" value=\"edition\"".$checked_edition.$edition_disabled.">";
		$html .= "<span class=\"s_label_radio_mode".$label_disabled."\">".$_MEDIT_LANGUE->label_mode_edition()."</span>";
		$html .= "<input id=\"i_radio_historique\" class=\"i_radio_mode\" type=\"radio\" name=\"mode\" value=\"historique\"".$checked_historique.">";
		$html .= "<span class=\"s_label_radio_mode\">".$_MEDIT_LANGUE->label_mode_historique()."</span>";
		$html .= "</p>\n";
		return $html;
	}
	
	public static function Generer_panneau_connexion() {
		global $_MEDIT_LANGUE;

		$est_connecte = medit_uc_identification::Est_connecte();
		if ($est_connecte) {
			$label_connexion = medit_uc_identification::Pseudo_connecte();
			$id_bouton = "a_deconnexion";
			$classe_bouton = "fa-sign-out";
			$title_bouton = "title=\"".$_MEDIT_LANGUE->se_deconnecter()."\"";
			$balise_config = "a";
			$href_config = "href=\"\"";
			$title_config = "title=\"".$_MEDIT_LANGUE->configuration()."\"";
		}
		else {
			$id_bouton = "a_connexion";
			$label_connexion = $_MEDIT_LANGUE->deconnecte();
			$classe_bouton = "fa-sign-in";
			$title_bouton = "title=\"".$_MEDIT_LANGUE->se_connecter()."\"";
			$balise_config = "span";$href_config = "";$title_config = "";
		}
		$html = "<p class=\"p_outil_info p_outil_info_connexion\">";
		$html .= "<span id=\"s_label_connexion\">".$label_connexion."</span>";
		$html .= "<a id=\"{$id_bouton}\" class=\"fa {$classe_bouton} a_bouton_connexion\" href=\"\" {$title_bouton} onclick=\"return false;\"></a>";
		$html .= "<{$balise_config} class=\"fa fa-cog s_bouton_configuration\" {$href_config} {$title_config}></{$balise_config}>";
		$html .= "</p>\n";
		return $html;
	}
}