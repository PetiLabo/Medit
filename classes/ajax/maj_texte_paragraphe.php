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

	// Récupération du verrou
	$id_verrou = (int) post("id_verrou");
	if ($id_verrou < 1) {die($_MEDIT_LANGUE->erreur_id_verrou_incorrect());}
	$verrou = medit_db_table_verrou::Construire($id_verrou);
	if (!($verrou->en_db())) {die($_MEDIT_LANGUE->erreur_id_verrou_inexistant($id_verrou));}

	// TODO : Vérification de la nécessité de mettre à jour
	$est_connecte = medit_uc_identification::Est_connecte();
	$id_auteur = ($est_connecte)?((int) medit_uc_identification::Id_connecte()):0;
	$liste_paragraphes = medit_db_table_composition::Liste_paragraphes_par_verrou_et_par_version($id_verrou, $id_version);
	$html = medit_html_panneau_article::Generer($id_article, $couple_derniere_version, $liste_paragraphes, $id_auteur);
	echo json_encode(array("a_change" => true, "html" => $html, "id_verrou" => $id_verrou));
