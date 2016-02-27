<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");

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

	// Récupération du verrou
	$id_verrou = (int) post("id_verrou");
	if ($id_verrou < 1) {die($_MEDIT_LANGUE->erreur_id_verrou_incorrect());}
	$verrou = medit_db_table_verrou::Construire($id_verrou);
	if (!($verrou->en_db())) {die($_MEDIT_LANGUE->erreur_id_verrou_inexistant($id_verrou));}
	$type_verrou = $verrou->lire_type_verrou();

	// On ne peut pas valider une contribution si on est déconnecté
	$est_connecte = medit_uc_identification::Est_connecte();
	if (!($est_connecte)) {
		$erreur = $_MEDIT_LANGUE->erreur_connexion_validation_contribution($id_verrou);
		echo json_encode(array("succes" => false, "html" => null, "erreur" => $erreur));
		exit;
	}
	$id_connecte = medit_uc_identification::Id_connecte();

	// Récupération des paragraphes
	$liste_paragraphes = medit_db_table_composition::Liste_paragraphes_par_verrou_et_par_version($id_verrou, $id_version);
	$nb_paragraphes = count($liste_paragraphes);
	if ($nb_paragraphes == 0) {
		$erreur = $_MEDIT_LANGUE->erreur_composition_incoherente($id_verrou);
		echo json_encode(array("succes" => false, "html" => null, "erreur" => $erreur));
		exit;
	}
	$id_premier_paragraphe = $liste_paragraphes[0];
	$id_dernier_paragraphe = $liste_paragraphes[$nb_paragraphes - 1];

	// Récupération du nouveau texte
	$nouveau_texte = (string) post("texte");
	$liste_balises_autorisees = liste_balises_autorisees();
	$liste_balises = decomposer_html($nouveau_texte, $liste_balises_autorisees);
	$nb_balises = count($liste_balises);

	// Parcours de la liste des balises composant le nouveau texte
	$est_nouvelle_contribution = ($nb_paragraphes != $nb_balises);
	$liste_nouvelle_composition = array();
	for ($cpt = 0;$cpt < $nb_balises;$cpt++) {
		$balise = $liste_balises[$cpt];
		$nom_balise = $balise["balise"];
		$nouveau_niveau = balise_to_niveau($nom_balise);
		$nouveau_texte = $balise["texte"];
		$avec_id = $balise["avec_id"];
		$est_nouveau_paragraphe = true;
		// S'il y a un id, on compare avec le paragraphe de même id
		if ($avec_id) {
			$html_id = $balise["id"];
			// Nettoyage de l'id : on enlève le préfixe contrib_
			$id = (int) str_replace("contrib_", "", $html_id);
			// On vérifie que c'est un id qui fait partie du verrou et que ce n'est pas un id en doublon
			if ((in_array($id, $liste_paragraphes)) && (!(in_array($id, $liste_nouvelle_composition)))) {
				$ancien_paragraphe = medit_db_table_paragraphe::Construire($id);
				// Cas d'un verrouillage en édition : on vérifie si c'est un nouveau paragraphe
				if ($type_verrou == 1) {
					$ancien_texte = retirer_annotations($ancien_paragraphe->lire_texte());
					$ancien_niveau = $ancien_paragraphe->lire_niveau();
					if (($ancien_niveau == $nouveau_niveau) && (!(strcmp($ancien_texte, $nouveau_texte)))) {
						$est_nouveau_paragraphe = false;
					}
				}
				// Cas d'un verrouillage en annotation : on change le texte sans considérer que c'est un nouveau paragraphe
				elseif ($type_verrou == 2) {
					// TODO : Le strcmp entre ancien et nouveau texte est à vérifier pour ne pas updater systématiquement
					// TODO : Pour le moment on modifie l'auteur sans créer d'annotations...
					$ancien_paragraphe->modifier_texte($nouveau_texte, $id_connecte);
					$est_nouveau_paragraphe = false;
				}
			}
		}
		// Création du nouveau paragraphe s'il n'a pas d'équivalent dans le verrou
		if ($est_nouveau_paragraphe) {
			$id = medit_db_table_paragraphe::Inserer($nouveau_niveau, $nouveau_texte, $id_connecte, false, $id_verrou);
			$est_nouvelle_contribution = true;
		}
		else {
			// Si les paragraphes sont identiques mais pas à la même place c'est une nouvelle contribution
			$index_ancien_paragraphe = array_search($id, $liste_paragraphes);
			if ($index_ancien_paragraphe != $cpt) {$est_nouvelle_contribution = true;}
		}
		// Ajout de l'identifiant (ancien ou nouveau) dans la nouvelle composition
		$liste_nouvelle_composition[$cpt] = $id;
	}
	
	// Création d'une nouvelle version /composition
	if ($est_nouvelle_contribution) {
		// Incrémentation de la version
		$couple_nouvelle_version = $article->inserer_nouvelle_sous_version($id_connecte);
		$id_nouvelle_version = (int) $article->no_to_id_version($couple_nouvelle_version);
		
		// Récupération de l'ancienne composition
		$composition = medit_db_table_composition::Liste_paragraphes_par_version($id_version);
		$nb_compositions = count($composition);
		$no_ordre = 1;
		$cpt = 0;
		
		// Premier temps : recopie des paragraphes précédant le verrou
		while (($composition[$cpt] != $id_premier_paragraphe) && ($cpt < $nb_compositions)) {
			medit_db_table_composition::Inserer($id_nouvelle_version, $composition[$cpt], $no_ordre);
			$cpt += 1;$no_ordre += 1;
		}
		
		// Second temps : remplacement de la partie verrouillée par la nouvelle composition
		foreach ($liste_nouvelle_composition as $id_nouveau_paragraphe) {
			medit_db_table_composition::Inserer($id_nouvelle_version, $id_nouveau_paragraphe, $no_ordre);
			$no_ordre += 1;
		}

		// Troisième temps : recopie des paragraphes suivant le verrou
		while (($composition[$cpt] != $id_dernier_paragraphe) && ($cpt < $nb_compositions)) {$cpt += 1;}
		for ($cpt = $cpt+1; $cpt < $nb_compositions;$cpt++) {
			medit_db_table_composition::Inserer($id_nouvelle_version, $composition[$cpt], $no_ordre);
			$no_ordre += 1;
		}
		
		// On officialise la nouvelle version
		$couple_version = $couple_nouvelle_version;
	}

	// Déverrouillage
	medit_db_table_paragraphe::Deverrouiller($liste_paragraphes, $id_verrou);
	$verrou->suspendre();

	// Journalisation (édition ou annotation + déverrouillage)
	if ($type_verrou == 1) {
		medit_db_table_journal::Inserer(medit_db_table_journal::DB_JOURNAL_EDITION_PARAGRAPHE, $id_connecte, $id_article, $id_verrou);
	}
	else if ($type_verrou == 2) {
		medit_db_table_journal::Inserer(medit_db_table_journal::DB_JOURNAL_ANNOTATION_PARAGRAPHE, $id_connecte, $id_article, $id_verrou);
	}
	medit_db_table_journal::Inserer(medit_db_table_journal::DB_JOURNAL_DEVERROUILLAGE_PARAGRAPHE, $id_connecte, $id_article, $id_verrou);

	// Retour
	$html = medit_html_panneau_article::Generer($id_article, $couple_version, $liste_nouvelle_composition, $id_connecte);
	echo json_encode(array("succes" => true, "html" => $html, "code_version" => coder_couple_version($couple_version), "erreur" => null));
