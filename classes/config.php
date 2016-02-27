<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de stockage des paramètres de configuration
 *
 * @auteurs		Philippe Gilles
 */
class medit_config {

	/**
	 * RAZ constructeur pour classe de config
	 *
	 * @portée	privée
	 * @retour	vide
	 */
    private function __construct() {}

	/*
	|--------------------------------------------------------------------------
	| Langue de l'interface
	|--------------------------------------------------------------------------
	*/
	const LANG_MEDIT = "en";
	const LANG_FORMAT_DATE = "d/m/Y";
	const LANG_FORMAT_HEURE = "H:i";
	const LANG_FORMAT_HEURE_LONGUE = "H:i:s";

	/*
	|--------------------------------------------------------------------------
	| Informations pour la connexion à la base de données
	|--------------------------------------------------------------------------
	*/
	const DB_SERVEUR = "localhost";
	const DB_NOM_BASE = "medit_base";
	const DB_UTILISATEUR = "root";
	const DB_MOT_DE_PASSE = "";
	const DB_PREFIXE_TABLE = "test_";

	/*
	|--------------------------------------------------------------------------
	| Paramètres pour la gestion des sessions (temps exprimé en secondes)
	|--------------------------------------------------------------------------
	*/
	const UC_PREFIXE_SESSION = "test_";
	const UC_TIMEOUT_SESSION = 14400;
	const UC_URL_SORTIE_SESSION = "index.php";

	/*
	|--------------------------------------------------------------------------
	| Cookies pour la sauvegarde de la configuration des panneaux
	|--------------------------------------------------------------------------
	*/
	const HTML_COOKIE_LAYOUT = "medit_ui_layout";
	const HTML_COOKIE_SORTABLE = "medit_ui_sortable";
	const HTML_COOKIE_FILS = "medit_ui_fils";

	/*
	|--------------------------------------------------------------------------
	| Temps de rafraichissement de l'affichage dans les panneaux (en secondes)
	|--------------------------------------------------------------------------
	*/
	const JS_INTERVALLE_ARTICLE = 6;
	const JS_INTERVALLE_COMMENTAIRES = 4;
	const JS_INTERVALLE_JOURNAL = 4;
}