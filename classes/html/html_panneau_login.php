<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}
	
/**
 * Classe d'affichage du panneau de login
 *
 * @auteurs		Philippe Gilles
 */
class medit_html_panneau_login {

	/**
	 * Génération du code HTML pour le panneau de login
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Generer_panneau_login() {
		global $_MEDIT_LANGUE;

		$html = "<p class=\"p_titre_login\">".$_MEDIT_LANGUE->titre_connexion()."</p>\n";
		$html .= "<p id=\"message_erreur_login\" class=\"p_erreur_login\">&nbsp;</p>\n";
		$html .= "<form class=\"f_login\" type=\"post\">";
		$html .= "<p class=\"p_champ_info\">";
		$html .= "<label class=\"fa fa-user\" for=\"i_login_id\"></label>";
		$html .= "<input id=\"i_login_id\" name=\"i_login_id\" type=\"text\" placeholder=\"".$_MEDIT_LANGUE->votre_identifiant()."\"></p>";
		$html .= "<p class=\"p_champ_info\">";
		$html .= "<label class=\"fa fa-lock\"  for=\"i_login_mdp\"></label>";
		$html .= "<input id=\"i_login_mdp\" name=\"i_login_mdp\" type=\"password\" placeholder=\"".$_MEDIT_LANGUE->votre_mot_de_passe()."\"></p>";
		$html .= "<p class=\"p_btn_login\"><input type=\"submit\" class=\"a_btn_login\" href=\"\" title=\"".$_MEDIT_LANGUE->se_connecter()."\" value=\"".$_MEDIT_LANGUE->bouton_submit()."\"></p>";
		$html .= "</form>\n";

		return $html;
	}
}