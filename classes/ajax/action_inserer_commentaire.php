<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");
	
	// On ne peut pas insérer un commentaire si on est déconnecté
	$est_connecte = medit_uc_identification::Est_connecte();
	if (!($est_connecte)) {
		$erreur = $_MEDIT_LANGUE->erreur_connexion_insertion_commentaire();
		echo json_encode(array("succes" => false, "erreur" => $erreur));
		exit;
	}
	$id_auteur = (int) medit_uc_identification::Id_connecte();

	// Récupération du fil de discussion
	$id_fil_discussion = (int) post("id_fil_discussion");
	if ($id_fil_discussion < 1) {die($_MEDIT_LANGUE->erreur_id_fil_discussion_incorrect());}
	$fil_discussion = medit_db_table_fil_discussion::Construire($id_fil_discussion);
	if (!($fil_discussion->en_db())) {die($_MEDIT_LANGUE->erreur_id_fil_discussion_inexistant($id_fil_discussion));}	
	
	// Récupération de l'article concerné par le fil de discussion
	$id_article = $fil_discussion->lire_fk_article();

	// Récupération du commentaire qui est répondu
	$id_commentaire = (int) post("id_commentaire");
	if ($id_commentaire == 0) {
		$niveau = 0;
	}
	else {
		$commentaire = medit_db_table_commentaire::Construire($id_commentaire);
		$niveau = 1 + (int) $commentaire->lire_niveau();
	}

	// Récupération du texte du commentaire
	$texte = (string) post("texte");

	// Création du commentaire
	$id_nouveau_commentaire = medit_db_table_commentaire::Inserer($niveau, $texte, $id_commentaire, $id_auteur, $id_fil_discussion);
	
	// Journalisation
	if ($niveau == 0) {
		medit_db_table_journal::Inserer(medit_db_table_journal::DB_JOURNAL_CREATION_FIL_DISCUSSION, $id_auteur, $id_article, $id_fil_discussion);
	}
	else {
		medit_db_table_journal::Inserer(medit_db_table_journal::DB_JOURNAL_REPONSE_COMMENTAIRE, $id_auteur, $id_article, $id_nouveau_commentaire);
	}

	// Retour : succès avec code HTML du fil de discussion
	$liste_etats_fils = medit_html_widget_commentaires::Lire_cookie_fils($id_article);
	if (isset($liste_etats_fils[$id_fil_discussion])) {
		list($etat_fil, $indentation_fil, $liste_zones_ouvertes) = $liste_etats_fils[$id_fil_discussion];
		// On retire de la liste la zone de réponse actuelle
		$index_liste = array_search($id_commentaire, $liste_zones_ouvertes);
		if ($index_liste !== false) {unset($liste_zones_ouvertes[$index_liste]);}
	}
	else {
		$etat_fil = 0;$indentation_fil = 1;$liste_zones_ouvertes = array();
	}
	$html = medit_html_widget_commentaires::Generer_fil_discussion($etat_fil, $indentation_fil, $liste_zones_ouvertes, $id_fil_discussion);
	echo json_encode(array("succes" => true, "html" => $html));