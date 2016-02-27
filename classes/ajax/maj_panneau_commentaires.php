<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");
	
	// Récupération de l'id connecté */
	$est_connecte = medit_uc_identification::Est_connecte();
	$id_connecte = ($est_connecte)?((int) medit_uc_identification::Id_connecte()):0;

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

	// Récupération du dernier identifiant journalisé
	$id_journal_debut = (int) post("id_journal");
	$id_journal_fin = (int) medit_db_table_journal::Lire_dernier_id_journal();
	
	// Création des listes
	$liste_creation_fils = array();
	$liste_creation_commentaires = array();
	if ($id_journal_debut > 0) {
		for ($id_journal = 1 + $id_journal_debut; $id_journal <= $id_journal_fin; $id_journal++) {
			$journal = medit_db_table_journal::Construire($id_journal);
			$fk_auteur = $journal->lire_fk_auteur();
			// On exclue les événements concernant l'auteur connecté */
			if ($fk_auteur == $id_connecte) {continue;}
			$fk_article = $journal->lire_fk_article();
			// On exclue les événements ne concernant pas l'article demandé */
			if ($fk_article != $id_article) {continue;}
			$type_evenement = $journal->lire_type_evenement();
			switch ($type_evenement) {
				case medit_db_table_journal::DB_JOURNAL_CREATION_FIL_DISCUSSION:
					$liste_creation_fils[] = $journal->lire_fk_contextuel();
					break;
				case medit_db_table_journal::DB_JOURNAL_REPONSE_COMMENTAIRE:
					$id_commentaire = $journal->lire_fk_contextuel();
					$commentaire = medit_db_table_commentaire::Construire($id_commentaire);
					$fk_fil_discussion = $commentaire->lire_fk_fil_discussion();
					$fk_commentaire = $commentaire->lire_fk_commentaire();
					$liste_creation_commentaires[] = array("id_fil_discussion" => $fk_fil_discussion, "id_commentaire" => $id_commentaire, "fk_commentaire" => $fk_commentaire);
					break;
				default:
					break;
			}
		}
	}

	echo json_encode(array("id_journal" => $id_journal_fin, "liste_creation_fils" => $liste_creation_fils, "liste_creation_commentaires" => $liste_creation_commentaires));