<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");

	// Récupération des paramètres
	$login_id = post("i_login_id");
	$login_mdp = post("i_login_mdp");
	if ((strlen($login_id) == 0) || (strlen($login_mdp) == 0)) {
		echo json_encode(array("succes" => false, "message" => $_MEDIT_LANGUE->message_erreur_connexion()));
		exit;
	}
	
	// Recherche de l'identifiant en base de données
	$id_auteur = medit_db_table_auteur::Chercher_par_identifiant($login_id);
	if (strlen($id_auteur) == 0) {
		echo json_encode(array("succes" => false, "message" => $_MEDIT_LANGUE->message_erreur_connexion()));
		exit;
	}
	
	// Vérification que l'utilisateur n'est pas déjà connecté
	$est_connecte = medit_uc_identification::Est_connecte();
	if ($est_connecte) {
		$id_connecte = medit_uc_identification::Id_connecte();
		if (!(strcmp($id_connecte, $id_auteur))) {
			echo json_encode(array("succes" => false, "message" => $_MEDIT_LANGUE->message_erreur_connexion()));
			exit;
		}
	}

	// Vérification du mot de passe
	$auteur = medit_db_table_auteur::Construire($id_auteur);
	$mdp = $auteur->lire_mot_de_passe();
	if (strcmp($mdp, $login_mdp)) {
		echo json_encode(array("succes" => false, "message" => $_MEDIT_LANGUE->message_erreur_connexion()));
		exit;
	}

	// Connexion
	medit_uc_identification::Connecter($id_auteur, $auteur->lire_pseudo());
	
	// Journalisation
	medit_db_table_journal::Inserer(medit_db_table_journal::DB_JOURNAL_CONNEXION, $id_auteur);

	// Retour succès
	echo json_encode(array("succes" => true, "message" => null));