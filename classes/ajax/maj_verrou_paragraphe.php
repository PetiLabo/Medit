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
	$couple_derniere_version = $article->lire_no_derniere_version();
	$id_version = $article->no_to_id_version($couple_derniere_version);
	$code_version = coder_couple_version($couple_derniere_version);
	
	// Renvoi de la liste des verrous
	$liste_verrous = medit_db_table_composition::Liste_verrous_par_version($id_version);
	echo json_encode(array("code_version" => $code_version, "liste_verrous" => $liste_verrous));
