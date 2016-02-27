<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");

	$est_connecte = medit_uc_identification::Est_connecte();
	if ($est_connecte) {
		$id_auteur = medit_uc_identification::Id_connecte();
		
		// Fermeture de la session
		medit_uc_identification::Deconnecter();
		
		// Suppression des verrous encore actifs pour l'auteur
		medit_db_table_paragraphe::Deverrouiller_auteur($id_auteur);
		
		// Journalisation
		medit_db_table_journal::Inserer(medit_db_table_journal::DB_JOURNAL_DECONNEXION, $id_auteur);

		echo json_encode(array("succes" => true, "html" => null, "erreur" => null));
	}
	else {
		$html = medit_html_panneau_infos::Generer_panneau_connexion();
		$erreur = "Echec lors de la déconnexion";
		echo json_encode(array("succes" => false, "html" => $html, "erreur" => $erreur));
	}