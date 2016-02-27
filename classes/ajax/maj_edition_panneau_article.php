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
	$couple_version = $article->lire_no_derniere_version();
	$code_version = coder_couple_version($couple_version);
	$id_version = $article->no_to_id_version($couple_version);

	// Récupération des infos de connexion
	$est_connecte = medit_uc_identification::Est_connecte();
	$id_auteur = ($est_connecte)?((int) medit_uc_identification::Id_connecte()):0;

	// Affichage des paragraphes pour cette version de l'article
	$liste_paragraphes = medit_db_table_composition::Liste_paragraphes_par_version($id_version);
	$html = medit_html_panneau_article::Generer($id_article, $couple_version, $liste_paragraphes, $id_auteur);
	echo json_encode(array("code_version" => $code_version, "html" => $html));