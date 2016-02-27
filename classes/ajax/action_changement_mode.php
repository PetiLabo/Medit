<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");

	// Récupération de l'article
	$erreur = true;
	$mode = strtolower(post("mode"));
	if (!(strcmp($mode, "edition"))) {
		$est_connecte = medit_uc_identification::Est_connecte();
		if ($est_connecte) {
			$nouveau_mode = medit_uc_mode::UC_MODE_EDITION;
			$erreur = false;
		}
	}
	elseif (!(strcmp($mode, "historique"))) {
		$nouveau_mode = medit_uc_mode::UC_MODE_HISTORIQUE;
		$erreur = false;
	}
	if (!($erreur)) {
		medit_uc_mode::Ecrire_mode($nouveau_mode);
		echo json_encode(array("succes" => true, "mode" => $nouveau_mode));
	}
	else {
		$mode = medit_uc_mode::Lire_mode();
		$erreur = $_MEDIT_LANGUE->erreur_mode_non_autorise();
		echo json_encode(array("succes" => false, "mode" => $mode, "erreur" => $erreur));
	}
