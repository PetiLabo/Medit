<?php
	// Configuration PHP notamment pour les sessions
	ini_set("display_errors", "1");
	ini_set("session.use_trans_sid", "0");
	ini_set("session.use_only_cookies", "1");
	ini_set("session.gc_probability", "1");
	ini_set("session.gc_divisor", "25");

	// Définition de la version Medit
	define("_MEDIT_VERSION", "0.0.1");
	
	// Constantes pour les identifiants des panneaux
	define("_MEDIT_HTML_ID_PANNEAU_ARTICLE", "panneau_article");
	define("_MEDIT_HTML_ID_PANNEAU_COMMENTAIRES", "panneau_commentaires");
	define("_MEDIT_HTML_ID_PANNEAU_SOMMAIRE", "panneau_sommaire");
	define("_MEDIT_HTML_ID_PANNEAU_MOTS_CLES", "panneau_mots_cles");
	define("_MEDIT_HTML_ID_PANNEAU_JOURNAL", "panneau_journal");
	
	// Constantes pour la gestion des droits
	define("_MEDIT_DROIT_LIRE", "0");
	define("_MEDIT_DROIT_EDITER", "1");
	define("_MEDIT_DROIT_COMMENTER", "2");
	define("_MEDIT_DROIT_ANNOTER", "4");

	// Inclusion préalable des scripts de base (après : autoload)
	require_once(_MEDIT_RACINE."/classes/autoload.php");
	require_once(_MEDIT_RACINE."/classes/fonctions.php");
	
	// Création de l'instance de gestion des langues
	$_MEDIT_LANGUE = new medit_lang(medit_config::LANG_MEDIT);

	// Initialisation des accès à la BDD
	medit_db::Init();
	
	// Démarrage de la session PHP
	$id_session = medit_uc_session::Ouvrir_session();
	if (strlen($id_session) == 0) {die($_MEDIT_LANGUE->erreur_ouverture_session());}
