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
	
	// Constitution de la liste des paragraphes à discuter */
	$liste_paragraphes = array_post("liste_paragraphes");
	$liste_a_discuter = array();
	foreach ($liste_paragraphes as $id_html_paragraphe) {
		$type_paragraphe = substr($id_html_paragraphe, 0, 2);
		if (!(strcmp($type_paragraphe, "p_"))) {
			$id_paragraphe = (int) substr($id_html_paragraphe, 2);
			if ($id_paragraphe > 0) {
				$liste_a_discuter[] = $id_paragraphe;
			}
		}
	}
	
	// On vérifie s'il existe déjà un fil de discussion sur cette liste
	$liste_recherche = medit_db_table_fil_discussion::Chercher_liste_paragraphes($liste_a_discuter);
	$id_doublon_egal = 0;$id_doublon_negatif = 0;$id_doublon_positif = 0;
	foreach ($liste_recherche as $recherche) {
		$distance = $recherche["distance"];
		$id_doublon = $recherche["id"];
		if ($distance == 0) {
			$id_doublon_egal = $id_doublon;
		}
		else if ($distance > 0) {
			$id_doublon_positif = $id_doublon;
		}
		else {
			$id_doublon_negatif = $id_doublon;
		}
	}

	echo json_encode(array("succes" => true, "liste_a_discuter" => $liste_a_discuter, "id_doublon_egal" => $id_doublon_egal, "id_doublon_negatif" => $id_doublon_negatif, "id_doublon_positif" => $id_doublon_positif));
