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
	$couple_version = decoder_couple_version($code_version);
	$id_version = $article->no_to_id_version($couple_version);
	$version = medit_db_table_version::Construire($id_version);
	if (!($version->en_db())) {die($_MEDIT_LANGUE->erreur_version_article_inexistant($code_version, $id_article));}

	// Affichage du sommaire pour cette version de l'article
	$liste_fils_discussion = $version->lire_fils_discussion();
	$html = medit_html_widget_commentaires::Generer_zone_commentaires($id_article, $liste_fils_discussion);
	echo $html;