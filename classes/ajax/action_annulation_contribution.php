<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");
	
	// Récupération de l'article
	$id_article = (int) post("id_article");
	if ($id_article < 1) {die($_MEDIT_LANGUE->erreur_id_article_incorrect());}
	$article = medit_db_table_article::Construire($id_article);
	if (!($article->en_db())) {die($_MEDIT_LANGUE->erreur_id_article_inexistant($id_article));}
	
	// Récupération de la version de l'article
	$code_version = (string) post("code_version");
	if (((float) $code_version) == 0) {die($_MEDIT_LANGUE->erreur_no_version_incorrect());}
	$couple_version = decoder_couple_version($code_version);
	$id_version = (int) $article->no_to_id_version($couple_version);
	if ($id_version == 0) {die($_MEDIT_LANGUE->erreur_no_version_incorrect());}
	$version = medit_db_table_version::Construire($id_version);
	if (!($version->en_db())) {die($_MEDIT_LANGUE->erreur_version_article_inexistant($code_version, $id_article));}

	// Récupération du verrou
	$id_verrou = (int) post("id_verrou");
	if ($id_verrou < 1) {die($_MEDIT_LANGUE->erreur_id_verrou_incorrect());}
	$verrou = medit_db_table_verrou::Construire($id_verrou);
	if (!($verrou->en_db())) {die($_MEDIT_LANGUE->erreur_id_verrou_inexistant($id_verrou));}

	// Récupération des paragraphes
	$liste_paragraphes = medit_db_table_composition::Liste_paragraphes_par_verrou_et_par_version($id_verrou, $id_version);
	medit_db_table_paragraphe::Deverrouiller($liste_paragraphes, $id_verrou);
	$verrou->suspendre();

	// Journalisation
	$est_connecte = medit_uc_identification::Est_connecte();
	$id_auteur = ($est_connecte)?((int) medit_uc_identification::Id_connecte()):0;
	medit_db_table_journal::Inserer(medit_db_table_journal::DB_JOURNAL_DEVERROUILLAGE_PARAGRAPHE, $id_auteur, $id_article, $id_verrou);
	
	// Retour
	$html = medit_html_panneau_article::Generer($id_article, $couple_version, $liste_paragraphes, $id_auteur);
	echo $html;
