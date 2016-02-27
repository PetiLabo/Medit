<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");

	// Récupération de l'article
	$id_fil_discussion = (int) post("id_fil_discussion");
	if ($id_fil_discussion < 1) {die($_MEDIT_LANGUE->erreur_id_fil_discussion_incorrect());}
	$fil_discussion = medit_db_table_fil_discussion::Construire($id_fil_discussion);
	if (!($fil_discussion->en_db())) {die($_MEDIT_LANGUE->erreur_id_fil_discussion_inexistant($id_fil_discussion));}

	// Ecriture du critère dans le cookie de session
	$liste_paragraphes = $fil_discussion->lire_liste_paragraphes();
	echo json_encode($liste_paragraphes);