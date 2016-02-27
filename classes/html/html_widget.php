<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}
	
/**
 * Classe de gestion de tous les widgets
 *
 * @auteurs		Philippe Gilles
 */
class medit_html_widget {
	
	const HTML_PANNEAU_OUEST = 0;
	const HTML_PANNEAU_EST = 1;
	const HTML_PANNEAU_SUD = 2;

	private $id_article = 0;
	private $id_version = 0;
	
	public function __construct($id_article, $id_version) {
		$this->id_article = $id_article;
		$this->id_version = $id_version;
	}
	
	public function afficher_widgets($panneau) {
		$liste_widgets = $this->liste_widgets($panneau);
		foreach ($liste_widgets as $widget) {
			switch ($widget) {
				case _MEDIT_HTML_ID_PANNEAU_COMMENTAIRES:
					$html = medit_html_widget_commentaires::Generer_panneau($this->id_article, $this->id_version);
					break;
				case _MEDIT_HTML_ID_PANNEAU_SOMMAIRE:
					$html = medit_html_widget_sommaire::Generer_panneau($this->id_version);
					break;
				case _MEDIT_HTML_ID_PANNEAU_MOTS_CLES:
					$html = medit_html_widget_mots_cles::Generer_panneau("...");
					break;
				case _MEDIT_HTML_ID_PANNEAU_JOURNAL:
					$html = medit_html_widget_journal::Generer_panneau($this->id_article);
					break;
				default:
					$html = "";
			}
			echo $html;
		}
	}

	/**
	 * Code HTML d'une option sélectionnée si la valeur correspond à la valeur actuelle
	 *
	 * @portée	privée
	 * @param	valeur actuelle
	 * @param	valeur de l'option
	 * @param	label porté par l'option
	 * @retour	html
	 */
	public static function Generer_select_option($valeur_actuelle, $valeur, $label) {
		$selected = ($valeur == $valeur_actuelle)?"selected=\"selected\"":"";
		$html = "<option value=\"{$valeur}\" {$selected}>".$label."</option>";
		return $html;
	}
	
	/**
	 * Code HTML d'une checkbox sélectionnée si la valeur correspond à la valeur actuelle
	 *
	 * @portée	privée
	 * @param	valeur actuelle
	 * @param	classe appliquée à la checkbox
	 * @param	fonction JS appelée lorsque la checkbox est cliquée
	 * @retour	html
	 */
	public static function Generer_checkbox($valeur_actuelle, $classe, $callback) {
		$checked = ($valeur_actuelle)?"checked=\"checked\"":"";
		$html = "<input class=\"{$classe}\" type=\"checkbox\" {$checked} onchange=\"{$callback}(this);\">";
		return $html;
	}
	
	private function liste_widgets($panneau) {
		$config_defaut_ouest = array(_MEDIT_HTML_ID_PANNEAU_COMMENTAIRES);
		$config_defaut_est = array(_MEDIT_HTML_ID_PANNEAU_SOMMAIRE, _MEDIT_HTML_ID_PANNEAU_MOTS_CLES);
		$config_defaut_sud = array(_MEDIT_HTML_ID_PANNEAU_JOURNAL);
		$cookie = $this->lire_cookie_sortable();
		$liste = null;
		switch ($panneau) {
			case self::HTML_PANNEAU_OUEST:
				if (($cookie) && (isset($cookie[self::HTML_PANNEAU_OUEST]))) {
					$liste = explode(",", $cookie[self::HTML_PANNEAU_OUEST]);
				}
				else {
					$liste = $config_defaut_ouest;
				}
				break;
			case self::HTML_PANNEAU_EST:
				if (($cookie) && (isset($cookie[self::HTML_PANNEAU_EST]))) {
					$liste = explode(",", $cookie[self::HTML_PANNEAU_EST]);
				}
				else {
					$liste = $config_defaut_est;
				}
				break;
			case self::HTML_PANNEAU_SUD:
				if (($cookie) && (isset($cookie[self::HTML_PANNEAU_SUD]))) {
					$liste = explode(",", $cookie[self::HTML_PANNEAU_SUD]);
				}
				else {
					$liste = $config_defaut_sud;
				}
				break;
			default:
				break;
		}
		return $liste;
	}
	
	private function lire_cookie_sortable() {
		$ret = null;
		if (isset($_COOKIE[medit_config::HTML_COOKIE_SORTABLE])) {
			$str_cookie = $_COOKIE[medit_config::HTML_COOKIE_SORTABLE];
			if (strlen($str_cookie) > 0) {
				$tab = explode("|", $str_cookie);
				if (count($tab) == 3) {
					$ret = $tab;
				}
			}
		}
		return $ret;
	}
}