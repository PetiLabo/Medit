<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");

	// Récupération de l'article
	$id_article = (int) post("id_article");
	if ($id_article < 1) {die($_MEDIT_LANGUE->erreur_id_article_incorrect());}
	$article = medit_db_table_article::Construire($id_article);
	if (!($article->en_db())) {die($_MEDIT_LANGUE->erreur_id_article_inexistant($id_article));}
	
	// Récupération de la valeur
	$valeur = (int) post("valeur");

	// Ecriture du critère dans le cookie de session
	$ret = medit_uc_filtre::Ecrire_critere_filtre($id_article, medit_uc_filtre::UC_FILTRE_HISTORIQUE_ANNOTATION_ACTIF, $valeur);
	echo json_encode(array("succes" => $ret, "erreur" => $_MEDIT_LANGUE->erreur_ecriture_critere_filtre(medit_uc_filtre::UC_FILTRE_HISTORIQUE_ANNOTATION_ACTIF)));