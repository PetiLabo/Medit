<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");

	// Récupération de l'article
	$id_article = (int) post("id_article");
	if ($id_article < 1) {die($_MEDIT_LANGUE->erreur_id_article_incorrect());}
	$article = medit_db_table_article::Construire($id_article);
	if (!($article->en_db())) {die($_MEDIT_LANGUE->erreur_id_article_inexistant($id_article));}

	// Récupération de la nouvelle dimension
	$nouvelle_dimension = (int) post("dimension");
	medit_uc_filtre::Ecrire_critere_filtre($id_article, medit_uc_filtre::UC_FILTRE_JOURNAL_DIMENSION, $nouvelle_dimension);

	// Récupération du dernier identifiant journalisé
	$id_journal_fin = (int) post("id_journal");
	if ($id_journal_fin < 1) {$id_journal_fin = (int) medit_db_table_journal::Lire_dernier_id_journal();}
	$id_journal_debut = max(0, $id_journal_fin - $nouvelle_dimension);

	// Création de la liste des événements à journaliser
	$html = medit_html_widget_journal::Generer_liste_evenements($id_article, $id_journal_debut, $id_journal_fin);
	echo json_encode(array("id_journal" => $id_journal_fin, "html" => $html));
