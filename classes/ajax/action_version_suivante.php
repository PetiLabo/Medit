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
	$id_version = (int) $article->no_to_id_version($couple_version);

	// Identification de la version précédente
	$id_version_suivante = (int) $article->lire_version_suivante($id_version);
	if ($id_version_suivante == 0) {
		echo json_encode(array("succes" => false));
	}
	else {
		$version = medit_db_table_version::Construire($id_version_suivante);
		$couple_version = array($version->lire_no_version(), $version->lire_no_sous_version());
		echo json_encode(array("succes" => true, "code_version" => coder_couple_version($couple_version)));
	}