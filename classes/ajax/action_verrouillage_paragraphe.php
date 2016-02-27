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
	
	// Récupération de la version de l'article
	$couple_derniere_version = $article->lire_no_derniere_version();
	$id_version = $article->no_to_id_version($couple_derniere_version);

	// Récupération du type de verrou
	$type_verrou = (int) post("type_verrou");
	if ($type_verrou < 1) {die($_MEDIT_LANGUE->erreur_type_verrou_incorrect());}
	
	// Constitution de la liste des paragraphes à verrouiller */
	$nb_a_verrouiller = 0;
	$liste_a_generer = array();
	$liste_a_verrouiller = array();
	$liste_de_listes_a_verrouiller = array();
	$liste_paragraphes = array_post("liste_paragraphes");
	foreach ($liste_paragraphes as $id_paragraphe) {
		$type_paragraphe = substr($id_paragraphe, 0, 2);
		if (!(strcmp($type_paragraphe, "f_"))) {
			// On passe par-dessus une zone verrouillée
			$no_verrou = (int) substr($id_paragraphe, 2);
			$liste_verrouillee = medit_db_table_composition::Liste_paragraphes_par_verrou_et_par_version($no_verrou, $id_version);
			foreach ($liste_verrouillee as $id_verrouille) {$liste_a_generer[] = $id_verrouille;}
			if (count($liste_a_verrouiller) > 0) {
				$liste_de_listes_a_verrouiller[] = $liste_a_verrouiller;
				unset($liste_a_verrouiller);$liste_a_verrouiller = array();
			}
		}
		elseif (!(strcmp($type_paragraphe, "p_"))) {
			// On passe sur un paragraphe
			$no_paragraphe = (int) substr($id_paragraphe, 2);
			$paragraphe = medit_db_table_paragraphe::Construire($no_paragraphe);
			if ($paragraphe->en_db()) {
				$liste_a_generer[] = $no_paragraphe;
				$verrou = $paragraphe->lire_verrou();
				if (!($verrou)) {
					$liste_a_verrouiller[] = $no_paragraphe;
					$nb_a_verrouiller += 1;
				}
				else {
					if (count($liste_a_verrouiller) > 0) {
						$liste_de_listes_a_verrouiller[] = $liste_a_verrouiller;
						unset($liste_a_verrouiller);$liste_a_verrouiller = array();
					}
				}
			}
		}
	}
	if (count($liste_a_verrouiller) > 0) {
		$liste_de_listes_a_verrouiller[] = $liste_a_verrouiller;
		unset($liste_a_verrouiller);$liste_a_verrouiller = array();
	}
	if ($nb_a_verrouiller > 0) {
		// On verrouille liste par liste
		foreach ($liste_de_listes_a_verrouiller as $liste_a_verrouiller) {
			// Création du verrou
			$id_verrou = medit_db_table_verrou::Inserer($type_verrou, $id_auteur);
			
			// Verrouillage des paragraphes
			medit_db_table_paragraphe::Verrouiller($liste_a_verrouiller, $id_verrou);
		}

		// Journalisation de l'événement
		medit_db_table_journal::Inserer(medit_db_table_journal::DB_JOURNAL_VERROUILLAGE_PARAGRAPHE, $id_auteur, $id_article, $id_verrou);
		
		// Préparation du code HTML correspondant à la contribution
		$html = medit_html_panneau_article::Generer($id_article, $couple_derniere_version, $liste_a_generer, $id_auteur);
		echo json_encode(array("succes" => true, "html" => $html, "liste_paragraphes" => $liste_paragraphes));
	}
	else {
		echo json_encode(array("succes" => false, "erreur" => "Rien à verrouiller :-("));
	}
