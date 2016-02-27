<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");

	// Récupération de l'article
	$id_article = (int) post("id_article");
	if ($id_article < 1) {die($_MEDIT_LANGUE->erreur_id_article_incorrect());}
	$article = medit_db_table_article::Construire($id_article);
	if (!($article->en_db())) {die($_MEDIT_LANGUE->erreur_id_article_inexistant($id_article));}
	
	// Récupération des infos sur la dernière version de l'article
	$couple_derniere_version = $article->lire_no_derniere_version();
	list($no_derniere_version, $no_derniere_sous_version) = $couple_derniere_version;
	$id_version = $article->no_to_id_version($couple_derniere_version);
	$version = medit_db_table_version::Construire($id_version);
	
	// On ne peut pas créer une version si on est déconnecté
	$est_connecte = medit_uc_identification::Est_connecte();
	if (!($est_connecte)) {
		$erreur = $_MEDIT_LANGUE->erreur_connexion_creation_version();
		echo json_encode(array("succes" => false, "erreur" => $erreur));
		exit;
	}
	$id_connecte = medit_uc_identification::Id_connecte();

	// Récupération de la nouvelle version de l'article
	$no_nouvelle_version = (int) post("no_nouvelle_version");
	if (($no_derniere_version < $no_nouvelle_version) && ($no_derniere_sous_version > 0)) {
		$version->ecrire_nouvelle_version($no_nouvelle_version, 0, $id_connecte);
	}

	echo json_encode(array("succes" => true));