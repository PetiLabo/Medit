<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}
	
/**
 * Classe d'affichage du panneau contenant l'article
 *
 * @auteurs		Philippe Gilles
 */
class medit_html_panneau_article {
	/**
	 * Génération du code HTML pour la barre d'outils gérant les versions de l'article
	 *
	 * @portée	publique
	 * @param	numéro de la version à afficher
	 * @retour	vide
	 */
	public static function Generer_barre_versions($id_article, $couple_version) {
		global $_MEDIT_LANGUE;
	
		$code_version = coder_couple_version($couple_version);
		$mode = medit_uc_mode::Lire_mode();
		$display = " style=\"display:".(($mode == medit_uc_mode::UC_MODE_EDITION)?"none":"block").";\"";
		$infos_version = medit_db_table_article::Lire_infos_version($id_article, $code_version);

		$html = "<div id=\"barre_outils_versions\" class=\"barre_outils_panneau\"".$display.">";
		$html .= "<p class=\"p_outil_info p_outil_info_version\">";
		$html .= "<span class=\"s_label_outil_version\">".$_MEDIT_LANGUE->version()."&nbsp;:</span>";
		$html .= "<a id=\"a_info_version_moins\" class=\"fa fa-minus-circle\" href=\"\" title=\"".$_MEDIT_LANGUE->version_precedente()."\" onclick=\"return false;\"></a>";
		$html .= "<span id=\"s_no_version_info\" class=\"s_numero_version_info\">{$code_version}</span>";
		$html .= "<a id=\"a_info_version_plus\" class=\"fa fa-plus-circle\" href=\"\" title=\"".$_MEDIT_LANGUE->version_suivante()."\" onclick=\"return false;\"></a>";
		$html .= "<span class=\"fa fa-calendar s_icone_calendrier_info\"></span>";
		$html .= "<span id=\"s_horodatage_info_1\">".$infos_version["date"]."</span>";
		$html .= "<span class=\"fa fa-clock-o s_icone_horloge_info\"></span>";
		$html .= "<span id=\"s_horodatage_info_2\">".$infos_version["heure"]."</span>";
		$html .= "<span class=\"fa fa-user s_icone_pseudo_info\"></span>";
		$html .= "<span id=\"s_auteur_info\">".$infos_version["pseudo"]."</span>";
		$html .= "</p>\n";
		$html .= "<p class=\"p_outil_info p_outil_bouton_filtre\"><a id=\"a_bouton_filtre\" class=\"fa fa-filter icone_filtrer\" href=\"\" title=\"".$_MEDIT_LANGUE->appliquer_filtre_version()."\"></a></p>";
		$html .= "</div>\n";
		return $html;
	}
	
	/**
	 * Génération du code HTML pour la barre d'outils gérant les filtres en mode historique
	 *
	 * @portée	publique
	 * @param	numéro de la version à afficher
	 * @retour	vide
	 */
	public static function Generer_barre_filtres($id_article, $couple_version) {
		global $_MEDIT_LANGUE;
		
		// Récupération des infos
		$mode = medit_uc_mode::Lire_mode();
		$article = medit_db_table_article::Construire($id_article);
		$display = " style=\"display:".(($mode == medit_uc_mode::UC_MODE_EDITION)?"none":"block").";\"";
		
		// Génération du code
		$html = "<div id=\"barre_outils_filtres\" class=\"barre_outils_panneau\"".$display.">";
		$html .= "<p class=\"p_outil_info p_outil_info_filtres\">";

		// Partie éditeur
		$filtre_editeur = (int) medit_uc_filtre::Lire_critere_filtre($id_article, medit_uc_filtre::UC_FILTRE_HISTORIQUE_EDITEUR);
		$html .= "<span class=\"fa fa-pencil icone_filtrer_editeur\"></span>";
		$html .= "<select id=\"i_outil_filtre_editeur\" class=\"select_filtre_editeur\" onchange=\"MEDIT.historique.evt_filtrer_editeur(this);\">";
		$html .= medit_html_widget::Generer_select_option($filtre_editeur, 0, $_MEDIT_LANGUE->tous_les_auteurs());
		$liste_auteurs = $article->lire_liste_auteurs();
		foreach ($liste_auteurs as $id_auteur => $pseudo) {
			$html .= medit_html_widget::Generer_select_option($filtre_editeur, $id_auteur, $pseudo);
		}
		$html .= "</select>";

		// Partie annotation
		$filtre_annotation_actif = (int) medit_uc_filtre::Lire_critere_filtre($id_article, medit_uc_filtre::UC_FILTRE_HISTORIQUE_ANNOTATION_ACTIF);
		$html .= medit_html_widget::Generer_checkbox($filtre_annotation_actif, "checkbox_filtrer_annotations", "MEDIT.historique.evt_basculer_filtre_annotation");
		$classe_icone_annotation = "icone_filtrer_annotation".(($filtre_annotation_actif)?"":" icone_filtrer_inactive");
		$html .= "<span id=\"icone_filtre_annotation\" class=\"fa fa-file-text {$classe_icone_annotation}\"></span>";
		$disabled = ($filtre_annotation_actif)?"":"disabled=\"disabled\"";
		$html .= "<select id=\"i_outil_filtre_type_annotation\" class=\"select_filtre_type_annotation\" {$disabled} onchange=\"MEDIT.historique.evt_filtrer_type_annotation(this);\">";
		$filtre_type_annotation = (int) medit_uc_filtre::Lire_critere_filtre($id_article, medit_uc_filtre::UC_FILTRE_HISTORIQUE_TYPE_ANNOTATION);
		$html .= medit_html_widget::Generer_select_option($filtre_type_annotation, 0, $_MEDIT_LANGUE->toutes_les_annotations());
		$liste_types_annotation = $article->lire_liste_types_annotation();
		foreach ($liste_types_annotation as $id_type_annotation => $label_annotation) {
			$html .= medit_html_widget::Generer_select_option($filtre_type_annotation, $id_type_annotation, $label_annotation);
		}
		$html .= "</select>";
		$html .= "<select id=\"i_outil_filtre_annotateur\" class=\"select_filtre_annotateur\" {$disabled} onchange=\"MEDIT.historique.evt_filtrer_annotateur(this);\">";
		$filtre_annotateur = (int) medit_uc_filtre::Lire_critere_filtre($id_article, medit_uc_filtre::UC_FILTRE_HISTORIQUE_ANNOTATEUR);
		$html .= medit_html_widget::Generer_select_option($filtre_annotateur, 0, $_MEDIT_LANGUE->tous_les_auteurs());
		$liste_annotateurs = $article->lire_liste_annotateurs();
		foreach ($liste_annotateurs as $id_auteur => $pseudo) {
			$html .= medit_html_widget::Generer_select_option($filtre_annotateur, $id_auteur, $pseudo);
		}
		$html .= "</select>";

		// Partie commentaire
		$filtre_commentaire_actif = (int) medit_uc_filtre::Lire_critere_filtre($id_article, medit_uc_filtre::UC_FILTRE_HISTORIQUE_COMMENTAIRE_ACTIF);
		$html .= medit_html_widget::Generer_checkbox($filtre_commentaire_actif, "checkbox_filtrer_commentaires", "MEDIT.historique.evt_basculer_filtre_commentaire");
		$classe_icone_commentaire = "icone_filtrer_commentaire".(($filtre_commentaire_actif)?"":" icone_filtrer_inactive");
		$html .= "<span id=\"icone_filtre_commentaire\" class=\"fa fa-commenting  {$classe_icone_commentaire}\"></span>";
		$disabled = ($filtre_commentaire_actif)?"":"disabled=\"disabled\"";
		$html .= "<select id=\"i_outil_filtre_commentateur\" class=\"select_filtre_commentateur\" {$disabled} onchange=\"MEDIT.historique.evt_filtrer_commentateur(this);\">";
		$filtre_commentateur = (int) medit_uc_filtre::Lire_critere_filtre($id_article, medit_uc_filtre::UC_FILTRE_HISTORIQUE_COMMENTATEUR);
		$html .= medit_html_widget::Generer_select_option($filtre_commentateur, 0, $_MEDIT_LANGUE->tous_les_auteurs());
		$liste_commentateurs = $article->lire_liste_commentateurs();
		foreach ($liste_commentateurs as $id_auteur => $pseudo) {
			$html .= medit_html_widget::Generer_select_option($filtre_commentateur, $id_auteur, $pseudo);
		}
		$html .= "</select>";
		$html .= "</p>\n";
		$html .= "</div>\n";
		return $html;
	}

	/**
	 * Génération du code HTML pour la barre d'outils gérant l'édition des articles
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Generer_barre_edition($id_article, $couple_version) {
		global $_MEDIT_LANGUE;

		$code_version = coder_couple_version($couple_version);
		$mode = medit_uc_mode::Lire_mode();
		$display = " style=\"display:".(($mode == medit_uc_mode::UC_MODE_EDITION)?"block":"none").";\"";
		$autorisation_edition = medit_uc_autorisation::Lire_autorisation_connexion(medit_uc_autorisation::DB_DROIT_EDITION);
		$autorisation_annotation = medit_uc_autorisation::Lire_autorisation_connexion(medit_uc_autorisation::DB_DROIT_ANNOTATION);
		$autorisation_version = medit_uc_autorisation::Lire_autorisation_connexion(medit_uc_autorisation::DB_DROIT_CREATION_VERSION);
		
		$html = "<div id=\"barre_outils_article\" class=\"barre_outils_panneau\"".$display.">";
		$html .= "<p class=\"p_outil_info p_outil_info_article p_outil_info_article_edition\">";
		if ($autorisation_edition) {
			$html .= "<a id=\"menu_editer\" class=\"fa fa-pencil icone_editer\" href=\"\" title=\"".$_MEDIT_LANGUE->editer_texte()."\" onclick=\"return false;\"></a>";
		}
		if ($autorisation_annotation) {
			$html .= "<a id=\"menu_annoter\" class=\"fa fa-file-text icone_annoter\" href=\"\" title=\"".$_MEDIT_LANGUE->annoter_texte()."\" onclick=\"return false;\"></a>";
		}
		$html .= "</p>\n";
		$html .= "<p class=\"p_outil_info p_outil_info_article p_outil_info_article_version\">";
		$html .= "<span>".$_MEDIT_LANGUE->version()."&nbsp;:</span>";
		$html .= "<span class=\"s_numero_version_edition\">".$code_version."</span>";
		if ($autorisation_version) {
			$version_mineure = (int) $couple_version[1];
			// On ne propose de version majeure que sur une version mineure
			// Donc on ne peut pas passer directement de 2.1 à 4.0 par exemple
			if ($version_mineure > 0) {
				$version_majeure = (int) $couple_version[0];
				$nouvelle_version_majeure = 1 + $version_majeure;
				$code_nouvelle_version = $nouvelle_version_majeure.".0";
				$message_nouvelle_version = $_MEDIT_LANGUE->confirmer_nouvelle_version($code_nouvelle_version, $code_version);
				$html .= "<button id=\"menu_creer_version\" class=\"bouton_creer_version\" title=\"".$_MEDIT_LANGUE->creer_nouvelle_version()." ".$code_nouvelle_version." ".$_MEDIT_LANGUE->creer_a_partir_de()."\" onclick='MEDIT.edition.evt_creer_nouvelle_version(\"".$message_nouvelle_version."\", ".$nouvelle_version_majeure.");'><i class=\"fa fa-floppy-o\"></i> ".$code_nouvelle_version."</button>";
			}
		}
		$html .= "</p>\n";
		$html .= "</div>\n";
		return $html;
	}
	
	/**
	 * Génération du sous-panneau contenant le texte de l'article
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Generer_zone_article($mode, $contenu = "") {
		$classe_mode = ($mode == medit_uc_mode::UC_MODE_EDITION)?"texte_panneau_edition":"texte_panneau_versions";
		$html = "<div id=\"texte_panneau_article\" class=\"texte_panneau {$classe_mode}\">".$contenu."</div>";
		return $html;
	}

	/**
	 * Génération du code HTML pour une liste de paragraphes dans une version d'article donnée
	 *
	 * @portée	publique
	 * @retour	liste de paragraphes
	 */
	public static function Generer($id_article, $couple_version, $liste_paragraphes, $id_connecte = 0) {
		// Récupération de l'article
		$article = medit_db_table_article::Construire($id_article);
		if (!($article->en_db())) {return null;}
		
		// Récupération de la version de l'article
		$id_version = $article->no_to_id_version($couple_version);
		$version = medit_db_table_version::Construire($id_version);
		if (!($version->en_db())) {return null;}
		
		// Constitution des paragraphes dans l'ordre de la composition
		$html = "";
		$type_actuel = -1;
		$liste_a_afficher = array();
		$liste_dans_ordre = medit_db_table_composition::Liste_paragraphes_par_version($id_version);
		foreach ($liste_dans_ordre as $id_paragraphe) {
			if (in_array($id_paragraphe, $liste_paragraphes)) {
				$type = 0;
				$paragraphe = medit_db_table_paragraphe::Construire($id_paragraphe);
				$texte = $paragraphe->lire_texte();
				$verrouillage = $paragraphe->lire_verrou();
				
				// En cas de verrouillage, est-ce en lecture (autre auteur, mode autre qu'édition) ou écriture ?
				$mode = medit_uc_mode::Lire_mode();
				if (($verrouillage) && ($id_connecte > 0) && ($mode == medit_uc_mode::UC_MODE_EDITION)) {
					$id_verrou = $paragraphe->lire_fk_verrou();
					$verrou = medit_db_table_verrou::Construire($id_verrou);
					if ($verrou->en_db()) {
						$id_auteur = $verrou->lire_fk_auteur();
						$type = ($id_auteur == $id_connecte)?$id_verrou:0;
					}
				}

				// On compare avec le type de génération en cours
				if ($type_actuel == -1) {
					// On démarre avec la première configuation trouvée
					$type_actuel = $type;
					$liste_a_afficher[] = $id_paragraphe;
				}
				elseif ($type_actuel == 0) {
					if ($type == 0) {
						// On était en lecture et on le reste
						$liste_a_afficher[] = $id_paragraphe;
					}
					else {
						// On était en lecture et on passe en écriture
						$html .= self::Generer_lecture($mode, $liste_a_afficher);
						// Affichage des paragraphes en lecture et démarrage d'une liste en écriture
						unset($liste_a_afficher);$liste_a_afficher = array($id_paragraphe);
						// Le nouveau type actuel porte le n° du verrou en écriture
						$type_actuel = $type;
					}
				}
				else {
					if ($type == 0) {
						// On était en écriture et on passe en lecture
						$html .= self::Generer_ecriture($type_actuel, $liste_a_afficher);
						// Affichage des paragraphes en écriture et démarrage d'une liste en lecture
						unset($liste_a_afficher);$liste_a_afficher = array($id_paragraphe);
						// Le nouveau type actuel correspond à la lecture
						$type_actuel = 0;
					}
					else {
						if ($type_actuel == $type) {
							// On était en écriture sous un verrou et on le reste
							$liste_a_afficher[] = $id_paragraphe;
						}
						else {
							// On était en écriture sous un verrou mais on change de verrou
							$html .= self::Generer_ecriture($type_actuel, $liste_a_afficher);
							// Affichage des paragraphes en écriture et démarrage d'une liste en écriture sous un nouveau verrou
							unset($liste_a_afficher);$liste_a_afficher = array($id_paragraphe);
							// Le nouveau type actuel correspond au nouveau numéro de verrou
							$type_actuel = $type;
						}
					}
				}
			}
		}
		// Traitement de la queue de paragraphes
		if (count($liste_a_afficher) > 0) {
			if ($type_actuel == 0) {
				$html .= self::Generer_lecture($mode, $liste_a_afficher);
			}
			else {
				$html .= self::Generer_ecriture($type_actuel, $liste_a_afficher);
			}
		}
		return $html;
	}

	/**
	 * Code HTML d'une liste de paragraphes en lecture
	 *
	 * @portée	publique
	 * @retour	html
	 */
	public static function Generer_lecture($mode, $liste_paragraphes) {
		global $_MEDIT_LANGUE;

		$html = "";
		$verrouillage_actuel = -1;
		foreach ($liste_paragraphes as $id_paragraphe) {
			$paragraphe = medit_db_table_paragraphe::Construire($id_paragraphe);
			$verrouillage = ($mode == medit_uc_mode::UC_MODE_EDITION)?($paragraphe->lire_verrou()):false;
			if ($verrouillage) {
				$id_verrou = $paragraphe->lire_fk_verrou();
				if ($verrouillage_actuel != $id_verrou) {
					if ($verrouillage_actuel > 0) {
						// On ferme le verrou précédent
						$html .= "</div>";
					}
					$html .= "<div id=\"f_{$id_verrou}\" class=\"verrou_wrapper\">";
				}
				$verrouillage_actuel = $id_verrou;
				$verrou = medit_db_table_verrou::Construire($id_verrou);
				$id_auteur = $verrou->lire_fk_auteur();
				$auteur = medit_db_table_auteur::Construire($id_auteur);
				$pseudo = ($auteur->en_db())?$auteur->lire_pseudo():$_MEDIT_LANGUE->pseudo_inconnu();
			}
			else {
				if ($verrouillage_actuel > 0) {
					// On ferme le verrou précédent
					$html .= "</div>";
				}
				$verrouillage_actuel = -1;
				$pseudo = "";
			}
			$classe_div = "p_box_".(($verrouillage)?"ferme":"ouvert");
			$html .= "<div id=\"p_{$id_paragraphe}\" class=\"p_box {$classe_div}\">";
			$niveau = $paragraphe->lire_niveau();
			list($balise, $avec_classe_niveau, $classe_niveau) = niveau_to_balise($niveau, false);
			$texte = $paragraphe->lire_texte();
			$classe_texte = ($verrouillage)?"":"editable";
			$html .= "<{$balise} id=\"contrib_{$id_paragraphe}\" class=\"texte {$classe_texte} {$classe_niveau}\">{$texte}</{$balise}>";
			$html .= "<p id=\"verrou_{$id_paragraphe}\" class=\"p_box_info_verrou\">";
			// TODO : Changer l'icone en fonction du type de verrou (édition, annotation) Cf JS mode-edition.js
			$html .= "<span class=\"fa fa-lock info_icone_verrou\"></span><span class=\"info_pseudo_verrouilleur\">{$pseudo}</span>";
			$html .= "</p>";
			$html .= "</div>\n";
		}
		if ($verrouillage_actuel > 0) {
			// On ferme le verrou précédent
			$html .= "</div>\n";
		}
		return $html;
	}
	
	/**
	 * Code HTML d'une liste de paragraphes en écriture
	 *
	 * @portée	publique
	 * @retour	html
	 */
	
	public static function Generer_ecriture($id_verrou, $liste_paragraphes) {
		global $_MEDIT_LANGUE;

		// Constitution de l'attribut name portant le type de verrou
		$verrou = medit_db_table_verrou::Construire($id_verrou);
		$type_verrou = $verrou->lire_type_verrou();
		$html = "<div id=\"v_{$id_verrou}\" class=\"editeur_box\">";
		$html .= "<textarea id=\"txt_{$id_verrou}\" name=\"type_{$type_verrou}\" class=\"editeur_texte\">";
		foreach ($liste_paragraphes as $id_paragraphe) {
			$paragraphe = medit_db_table_paragraphe::Construire($id_paragraphe);
			$texte = $paragraphe->lire_texte();
			// En cas de verrou en édition on retire les annotations
			if ($type_verrou == 1) {
				$texte = retirer_annotations($texte);
			}
			$niveau = $paragraphe->lire_niveau();
			list($balise, $avec_classe_niveau, $classe_niveau) = niveau_to_balise($niveau, true);
			$classe = $avec_classe_niveau?" class=\"{$classe_niveau}\"":"";
			$html .= "<{$balise} id=\"contrib_{$id_paragraphe}\"".$classe.">{$texte}</{$balise}>";
		}
		$html .= "</textarea>";
		$html .= "</div>";
		return $html;
	}
	
	/**
	 * Code HTML d'une liste de paragraphes avec application d'un filtre
	 *
	 * @portée	publique
	 * @retour	html
	 */
	public static function Generer_filtre($id_article, $couple_version, $liste_paragraphes, $filtre) {
		$html = "";
		$filtre_actif = false;
		$filtre_editeur = (int) $filtre[medit_uc_filtre::UC_FILTRE_HISTORIQUE_EDITEUR];
		$filtre_annotation_actif = (int) $filtre[medit_uc_filtre::UC_FILTRE_HISTORIQUE_ANNOTATION_ACTIF];
		$filtre_commentaire_actif = (int) $filtre[medit_uc_filtre::UC_FILTRE_HISTORIQUE_COMMENTAIRE_ACTIF];
		foreach ($liste_paragraphes as $id_paragraphe) {
			$a_filtrer = false;
			$paragraphe = medit_db_table_paragraphe::Construire($id_paragraphe);
			if ($filtre_editeur > 0) {
				$auteur = (int) $paragraphe->lire_fk_auteur();
				$a_filtrer = ($filtre_editeur != $auteur);
			}
			if (($filtre_annotation_actif) && (!($a_filtrer))) {
				$filtre_annotateur = (int) $filtre[medit_uc_filtre::UC_FILTRE_HISTORIQUE_ANNOTATEUR];
				$liste_annotateurs = $paragraphe->lire_liste_annotateurs();
				if ($filtre_annotateur > 0) {
					$a_filtrer = !(in_array($filtre_annotateur, $liste_annotateurs));
				}
				else {
					$a_filtrer = (count($liste_annotateurs) == 0);
				}
				if (!($a_filtrer)) {
					$filtre_type_annotation = (int) $filtre[medit_uc_filtre::UC_FILTRE_HISTORIQUE_TYPE_ANNOTATION];
					if ($filtre_type_annotation > 0) {
						$liste_types_annotation = $paragraphe->lire_liste_types_annotation();
						$a_filtrer = !(in_array($filtre_type_annotation, $liste_types_annotation));
					}
				}
			}
			if (($filtre_commentaire_actif) && (!($a_filtrer))) {
				$filtre_commentateur = (int) $filtre[medit_uc_filtre::UC_FILTRE_HISTORIQUE_COMMENTATEUR];
				$liste_commentateurs = $paragraphe->lire_liste_commentateurs();
				if ($filtre_commentateur > 0) {
					$a_filtrer = !(in_array($filtre_commentateur, $liste_commentateurs));
				}
				else {
					$a_filtrer = (count($liste_commentateurs) == 0);
				}
			}
			if ($a_filtrer) {
				if (!($filtre_actif)) {
					$html .= "<div class=\"p_box\">";
					$html .= "<p class=\"texte texte_filtre\">[...]</p>";
					$html .= "</div>\n";
				}
				$filtre_actif = true;
			}
			else {
				$niveau = $paragraphe->lire_niveau();
				list($balise, $avec_classe_niveau, $classe_niveau) = niveau_to_balise($niveau, false);
				$texte = $paragraphe->lire_texte();
				$html .= "<div id=\"p_{$id_paragraphe}\" class=\"p_box p_box_ouvert\">";
				$html .= "<{$balise} id=\"contrib_{$id_paragraphe}\" class=\"texte {$classe_niveau}\">{$texte}</{$balise}>";
				$html .= "</div>\n";
				$filtre_actif = false;
			}
		}
		return $html;
	}
}