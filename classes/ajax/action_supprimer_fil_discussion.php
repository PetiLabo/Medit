<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");

	// Récupération du fil de discussion à supprimer
	$id_fil_discussion = (int) post("id_fil_discussion");
	if ($id_fil_discussion < 1) {die($_MEDIT_LANGUE->erreur_id_fil_discussion_incorrect());}
	medit_db_table_fil_discussion::Supprimer($id_fil_discussion);

	// Ecriture du critère dans le cookie de session
	echo json_encode(array("succes" => true, "id_fil_discussion" => $id_fil_discussion));