<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");
	
	// On ne peut pas verrouiller si on est déconnecté
	$est_connecte = medit_uc_identification::Est_connecte();
	if (!($est_connecte)) {
		$erreur = $_MEDIT_LANGUE->erreur_connexion_edition_paragraphe();
		echo json_encode(array("succes" => false, "erreur" => $erreur));
		exit;
	}
	$id_auteur = (int) medit_uc_identification::Id_connecte();

	// Récupération de l'article
	$id_article = (int) post("id_article");
	if ($id_article < 1) {die($_MEDIT_LANGUE->erreur_id_article_incorrect());}
	$article = medit_db_table_article::Construire($id_article);
	
	// Constitution de la liste des paragraphes à discuter
	$liste_a_discuter = array_post("liste_a_discuter");
	$nb_liste_a_discuter = count($liste_a_discuter);
	if ($liste_a_discuter == 0) {
		$erreur = $_MEDIT_LANGUE->erreur_liste_a_discuter_vide();
		echo json_encode(array("succes" => false, "erreur" => $erreur));
		exit;
	}

	$id_fil_discussion = (int) medit_db_table_fil_discussion::Inserer($liste_a_discuter, $id_auteur, $id_article);
	echo json_encode(array("succes" => true, "id_fil_discussion" => $id_fil_discussion));
