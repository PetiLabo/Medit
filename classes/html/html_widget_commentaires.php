<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}
	
/**
 * Classe d'affichage du widget commentaires
 *
 * @auteurs		Philippe Gilles
 */
class medit_html_widget_commentaires {
	
	public static function Generer_panneau($id_article, $id_version) {
		// $html = "<div id=\""._MEDIT_HTML_ID_PANNEAU_COMMENTAIRES."\" class=\"medit_widget contentSelector\">\n";
		$html = "<div id=\""._MEDIT_HTML_ID_PANNEAU_COMMENTAIRES."\" class=\"medit_widget\">\n";
		$html .= self::Generer_barre_commentaires();
		$version = medit_db_table_version::Construire($id_version);
		$html .= self::Generer_zone_commentaires($id_article, $version->lire_fils_discussion());
		$html .= "</div>\n";
		return $html;
	}
	
	public static function Generer_panneau_alerte() {
		$html = "<p id=\"message_alerte_discussion\" class=\"p_message_alerte_discussion\"></p>\n";
		return $html;
	}

	public static function Generer_barre_commentaires() {
		global $_MEDIT_LANGUE;
	
		$autorisation_commentaires = medit_uc_autorisation::Lire_autorisation_connexion(medit_uc_autorisation::DB_DROIT_COMMENTAIRE);

		$html = "<div id=\"barre_outils_commentaires\" class=\"barre_outils_panneau\">";
		$html .= "<p class=\"p_outil_info p_outil_commentaire_poignee\"><a class=\"fa fa-arrows icone_poignee_deplacement\"></a></p>\n";
		if ($autorisation_commentaires) {
			$html .= "<p class=\"p_outil_info p_outil_commentaire_boutons\">";
			$html .= "<a id=\"menu_commenter\" class=\"fa fa-commenting icone_commenter\" href=\"\" title=\"".$_MEDIT_LANGUE->commenter_texte()."\" onclick=\"return false;\"></a>";
			$html .= "</p>\n";
		}
		else {
			$html .= "<p class=\"p_outil_info p_outil_commentaire_titre\">";
			$html .= "<span class=\"s_outil_titre_sommaire\">".$_MEDIT_LANGUE->titre_commentaires()."</span>";
			$html .= "</p>\n";
		}
		$html .= "</div>\n";
		return $html;
	}
	
	public static function Generer_zone_commentaires($id_article, $tab_fils_discussion) {
		$liste_etats_fils = self::Lire_cookie_fils($id_article);
		$html = "<div id=\"zone_panneau_commentaires\" class=\"panneau_commentaires\">";
		foreach($tab_fils_discussion as $id_fil_discussion) {
			if (isset($liste_etats_fils[$id_fil_discussion])) {
				list($etat_fil, $indentation_fil, $liste_zones_ouvertes) = $liste_etats_fils[$id_fil_discussion];
			}
			else {
				$etat_fil = 0;$indentation_fil = 1;$liste_zones_ouvertes = array();
			}
			// TRACE : $html .= $id_fil_discussion." => [".implode(",",$liste_zones_ouvertes)."]<br>";
			$html .= self::Generer_fil_discussion($etat_fil, $indentation_fil, $liste_zones_ouvertes, $id_fil_discussion);
		}
		$html .= "</div>\n";
		return $html;
	}

	public static function Generer_fil_discussion($etat_fil, $indentation_fil, $liste_zones_ouvertes, $id_fil_discussion) {
		global $_MEDIT_LANGUE;
	
		$fil_discussion = medit_db_table_fil_discussion::Construire($id_fil_discussion);
		$tab_commentaires = $fil_discussion->lire_liste_commentaires();
		$nb_commentaires = count($tab_commentaires);
		$afficher_fil = true;
		
		if ($nb_commentaires == 0) {
			$est_connecte = medit_uc_identification::Est_connecte();
			if (!($est_connecte)) {
				$afficher_fil = false;
			}
			else {
				$id_auteur = (int) medit_uc_identification::Id_connecte();
				$fk_auteur = (int) $fil_discussion->lire_fk_auteur();
				$afficher_fil = ($id_auteur == $fk_auteur);
			}
		}

		$html = "";
		if ($afficher_fil) {
			$html .= "<div id=\"fil_discussion_{$id_fil_discussion}\" class=\"fil_discussion_container\">";
			$classe_entete = ($nb_commentaires > 0)?"fil_discussion_entete_encours":"fil_discussion_entete_nouveau";
			// Barre d'outils
			$html .= "<div id=\"fil_discussion_entete_{$id_fil_discussion}\" class=\"fil_discussion_entete {$classe_entete}\">";
			$html .= "<p class=\"fil_discussion_minmax\">";
			if ($etat_fil == 0) {
				$classe_ancre = "fa-minus-square fil_discussion_ancre_min";
				$display_fil = "style=\"display:block;\"";
			}
			else {
				$classe_ancre = "fa-plus-square fil_discussion_ancre_max";
				$display_fil = "style=\"display:none;\"";
			}
			$html .= "<a id=\"fil_discussion_ancre_minmax_{$id_fil_discussion}\" href=\"javascript:void(0);\" class=\"fa {$classe_ancre} fil_discussion_ancre_minmax\" title=\"".$_MEDIT_LANGUE->basculer_fil_discussion()."\" onclick=\"MEDIT.commentaires.evt_basculer_fil_discussion({$id_fil_discussion});return false;\"></a>";
			$classe_icone_indentation = "icone_indenter_commentaires ".(($indentation_fil > 0)?"fa-align-justify":"fa-align-right icone_indenter_inactive");
			$html .= "<a id=\"fil_discussion_ancre_indentation_{$id_fil_discussion}\" href=\"javascrit:void(0);\" class=\"fa {$classe_icone_indentation}\" title=\"".$_MEDIT_LANGUE->basculer_indentation()."\" onclick=\"MEDIT.commentaires.evt_basculer_indentation({$id_fil_discussion});return false;\"></a>";
			$html .= "</p>\n";
			$html .= "<p class=\"fil_discussion_ancre\">";
			$html .= "<a id=\"fil_discussion_ancre_lien_{$id_fil_discussion}\" href=\"javascript:void(0);\" class=\"fa fa-link fil_discussion_ancre_lien\" title=\"".$_MEDIT_LANGUE->atteindre_zone_discussion()."\" onclick=\"MEDIT.commentaires.evt_atteindre_partie_discutee({$id_fil_discussion});return false;\"></a>";
			$html .= "</p>\n";
			$html .= "<div style=\"clear:both;\"></div></div>\n";
			// Zone de commentaires
			$html .= "<div id=\"fil_discussion_commentaires_{$id_fil_discussion}\" class=\"fil_discussion_commentaires\" {$display_fil}>";
			if ($nb_commentaires > 0) {
				foreach($tab_commentaires as $id_commentaire) {
					$zone_reponse_ouverte = in_array($id_commentaire, $liste_zones_ouvertes);
					$html .= self::Generer_commentaire($indentation_fil, $id_fil_discussion, $zone_reponse_ouverte, $id_commentaire);
				}
			}
			else {
					$html .= self::Generer_zone_reponse(0, $indentation_fil, $id_fil_discussion, true, 0);
			}
			$html .= "</div></div>\n";
		}

		return $html;
	}

	public static function Generer_commentaire($indentation_fil, $id_fil_discussion, $zone_reponse_ouverte, $id_commentaire) {
		global $_MEDIT_LANGUE;

		$autorisation_commentaire = medit_uc_autorisation::Lire_autorisation_connexion(medit_uc_autorisation::DB_DROIT_COMMENTAIRE);
	
		$commentaire = medit_db_table_commentaire::Construire($id_commentaire);
		$niveau = (int) $commentaire->lire_niveau();
		$horodatage = (int) $commentaire->lire_horodatage();
		$heure_horodatage = formatter_horodatage($horodatage);
		$id_auteur = (int) $commentaire->lire_fk_auteur();
		$auteur = medit_db_table_auteur::Construire($id_auteur);
		$pseudo = $auteur->lire_pseudo();
		$texte = htmlentities(trim($commentaire->lire_texte()), ENT_QUOTES, "UTF-8");
		$marge = "style=\"margin-left:".(2 + $niveau * 6)."px;\"";
		$classe_commentaire_indentation = "texte_commentaire_container ".(($indentation_fil > 0)?"":"fil_discussion_sans_indentation");
		$html = "<div id=\"texte_commentaire_{$id_commentaire}\" class=\"{$classe_commentaire_indentation}\" ${marge}>";
		
		/* Barre d'outils */
		$html.= "<div class=\"barre_outils_commentaire\">";
		if ($autorisation_commentaire) {
			$html .= "<p class=\"barre_outils_commentaire_general\">";
			$classe_repondre_commentaire = ($zone_reponse_ouverte)?"icone_repondre_commentaire_max fa-ellipsis-h":"icone_repondre_commentaire_min fa-reply";
			$html .= "<a id=\"lien_reponse_commentaire_{$id_fil_discussion}_{$id_commentaire}\" class=\"fa {$classe_repondre_commentaire} icone_repondre_commentaire\" href=\"javascript:void(0);\" title=\"".$_MEDIT_LANGUE->repondre_commentaire()."\" onclick=\"MEDIT.commentaires.evt_repondre_commentaire({$id_fil_discussion}, {$id_commentaire});return false;\"></a>";
			$html .= "</p>";
		}
		$html .= "<p class=\"barre_outils_commentaire_connecte\">{$heure_horodatage}</a></p>";
		$html .= "<div style=\"clear:both;\"></div></div>";

		/* Texte du commentaire */
		$html .= "<p class=\"texte_commentaire\"><strong>{$pseudo}</strong>&nbsp;: {$texte}</p>";
		$html .= "</div>\n";

		/* Zone de réponse */
		$html .= self::Generer_zone_reponse($niveau, $indentation_fil, $id_fil_discussion, $zone_reponse_ouverte, $id_commentaire);
		return $html;
	}
	
	private static function Generer_zone_reponse($niveau, $indentation_fil, $id_fil_discussion, $zone_reponse_ouverte, $id_commentaire) {
		global $_MEDIT_LANGUE;

		$html = "";
		$autorisation_commentaire = medit_uc_autorisation::Lire_autorisation_connexion(medit_uc_autorisation::DB_DROIT_COMMENTAIRE);
		if ($autorisation_commentaire) {
			$marge = "margin-left:".(2 + $niveau * 6)."px;";
			if ($id_commentaire == 0) {
				$evt_annuler = "MEDIT.commentaires.evt_supprimer_fil_discussion({$id_fil_discussion});";
			}
			else {
				$evt_annuler = "MEDIT.commentaires.evt_annuler_commentaire({$id_fil_discussion}, {$id_commentaire});";
			}
			$display = "display:".(($zone_reponse_ouverte)?"block":"none").";";
			$classe_reponse_indentation = "zone_reponse_commentaire ".(($indentation_fil > 0)?"":"fil_discussion_sans_indentation");
			$html .= "<div id=\"zone_reponse_commentaire_{$id_fil_discussion}_{$id_commentaire}\" class=\"{$classe_reponse_indentation}\" style=\"{$marge}{$display}\">";
			$html .= "<div class=\"champ_reponse_container\"><textarea id=\"champ_reponse_commentaire_{$id_fil_discussion}_{$id_commentaire}\" class=\"champ_reponse_commentaire\"></textarea></div>";
			$html .= "<button id=\"valider_reponse_commentaire_{$id_fil_discussion}_{$id_commentaire}\" class=\"valider_reponse_commentaire\" onclick=\"MEDIT.commentaires.evt_valider_commentaire({$id_fil_discussion}, {$id_commentaire});\">".$_MEDIT_LANGUE->ok()."</button>";
			$html .= "<button id=\"annuler_reponse_commentaire_{$id_fil_discussion}_{$id_commentaire}\" class=\"annuler_reponse_commentaire\" onclick=\"{$evt_annuler}\">".$_MEDIT_LANGUE->annuler()."</button>";
			$html .= "<div style=\"clear:both;\"></div></div>";
		}
		return $html;
	}

	public static function Lire_cookie_fils($id_article) {
		$liste = array();
		$nom_cookie = (medit_config::HTML_COOKIE_FILS)."_".$id_article;
		if (isset($_COOKIE[$nom_cookie])) {
			$str_cookie = $_COOKIE[$nom_cookie];
			if (strlen($str_cookie) > 0) {
				$tab = explode("|", $str_cookie);
				foreach ($tab as $config) {
					$tab_config = explode(",", $config);
					if (count($tab_config) > 2) {
						$id_fil = (int) array_shift($tab_config);
						$etat_fil = (int) array_shift($tab_config);
						$indentation_fil = (int) array_shift($tab_config);
						$liste[$id_fil] = array($etat_fil, $indentation_fil, $tab_config);
					}
				}
			}
		}
		return $liste;
	}
}