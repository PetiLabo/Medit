/*
 * Javascript pour la gestion des commentaires dans l'IHM Medit
 */
 
var MEDIT = MEDIT || {};

MEDIT.commentaires = {
	Pointeur_id_commentaires: 0,

	/* Lancement de requêtes Ajax *****************************************************************/

	/* Mise à jour des discussions dans le panneau commentaires */
	maj_commentaires: function() {
		$.ajax({
			type: "POST",
			url: "classes/ajax/maj_panneau_commentaires.php",
			data: {id_article: Global_id_article, code_version: Global_version_article, id_journal: MEDIT.commentaires.Pointeur_id_commentaires},
			dataType: "json"
		}).done(function(data) {
			MEDIT.commentaires.Pointeur_id_commentaires = parseInt(data["id_journal"]);
			var liste_creation_fils = data["liste_creation_fils"];
			var nb_liste_creation_fils = liste_creation_fils.length;
			var liste_creation_commentaires = data["liste_creation_commentaires"];
			var nb_liste_creation_commentaires = liste_creation_commentaires.length;
			var index;
			for (index = 0;index < nb_liste_creation_fils;index += 1) {
				var id_nouveau_fil_discussion = liste_creation_fils[index];
				alert(id_nouveau_fil_discussion);
			}
			for (index = 0;index < nb_liste_creation_commentaires;index += 1) {
				var infos_nouveau_commentaire = liste_creation_commentaires[index];
				var id_fil_discussion = infos_nouveau_commentaire["id_fil_discussion"];
				var id_nouveau_commentaire = infos_nouveau_commentaire["id_commentaire"];
				var fk_commentaire = infos_nouveau_commentaire["fk_commentaire"];
				MEDIT.commentaires.maj_nouveau_commentaire(id_fil_discussion, id_nouveau_commentaire, fk_commentaire);
			}
			window.setTimeout(MEDIT.commentaires.maj_commentaires, 1000 * parseInt(Global_intervalle_panneau_commentaires));
		});
	},

	/* Mise à jour : nouveau commentaire à insérer dans un fil de discussion en cours */
	maj_nouveau_commentaire: function(id_fil_discussion, id_nouveau_commentaire, fk_commentaire) {
		/* Récupération de l'indentation */
		var ancre = $("a#fil_discussion_ancre_indentation_"+id_fil_discussion);
		if (ancre.length > 0) {
			var ancre_inactive = ancre.hasClass("icone_indenter_inactive");
			if (ancre_inactive) {var indentation = 0;} else {var indentation = 1;}
			$.ajax({
				type: "POST",
				url: "classes/ajax/maj_nouveau_commentaire.php",
				data: {indentation_fil: indentation, id_fil_discussion: id_fil_discussion, id_commentaire: id_nouveau_commentaire, fk_commentaire: fk_commentaire},
				dataType: "json"
			}).done(function(data) {
				var id_commentaire_parent = parseInt(data["fk_commentaire"]);
				var id_commentaire_fils = parseInt(data["id_commentaire"]);
				var commentaire_parent = $("div#texte_commentaire_"+id_commentaire_parent);
				if (commentaire_parent.length > 0) {
					html = data["html"];
					var nouveau_commentaire = $(html);
					nouveau_commentaire.hide();
					commentaire_parent.after(nouveau_commentaire);
					var commentaire_fils = $("div#texte_commentaire_"+id_commentaire_fils);
					if (commentaire_fils.length > 0) {commentaire_fils.slideDown(250);}
				}
			});
		}
	},

	/* Chargement d'une version d'article dans le panneau commentaires */
	maj_version_panneau_commentaires: function(id_fil_discussion, code_version) {
		$.ajax({
			type: "POST",
			url: "classes/ajax/maj_version_panneau_commentaires.php",
			data: {id_article: Global_id_article, code_version: code_version},
			dataType: "html"
		}).done(function(html) {
			$("div#zone_panneau_commentaires").replaceWith(html);
			MEDIT.commentaires.atteindre_fil_discussion(id_fil_discussion);
		});
		Global_version_article = code_version;
	},

	/* Insertion d'un nouveau commentaire dans un fil de discussion (refresh de tout le fil) */
	action_inserer_commentaire: function(id_fil_discussion, id_commentaire, texte) {
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_inserer_commentaire.php",
			data: {id_fil_discussion: id_fil_discussion, id_commentaire: id_commentaire, texte: texte},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {
				var html = data["html"];
				$("div#fil_discussion_"+id_fil_discussion).replaceWith(html);
				/* Stockage de la configuration dans un cookie */
				$.cookie(Global_cookie_fils+"_"+Global_id_article, MEDIT.commentaires.lire_config_fils());
			}
			else {
				var message_erreur = data["erreur"];
				alert(message_erreur);
			}
		});
	},

	/* Commentaires : insertion d'un nouveau fil de discussion */
	action_inserer_fil_discussion: function(liste_a_discuter) {
		demarrer_attente();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_inserer_fil_discussion.php",
			data: {id_article: Global_id_article, liste_a_discuter: liste_a_discuter},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {	
				var id_fil_discussion = parseInt(data["id_fil_discussion"]);
				MEDIT.commentaires.maj_version_panneau_commentaires(id_fil_discussion, Global_version_article);
			}
			else {
				var message_erreur = data["erreur"];
				alert(message_erreur);
			}
			terminer_attente();
		});
	},

	/* Callbacks sur les événements IHM ***********************************************************/

	evt_verifier_fil_discussion: function(nodes) {
		var nb_nodes = nodes.length;
		if (nb_nodes > 0) {
			$.ajax({
				type: "POST",
				url: "classes/ajax/action_verifier_fil_discussion.php",
				data: {id_article: Global_id_article, liste_paragraphes: nodes},
				dataType: "json"
			}).done(function(data) {
				var succes = data["succes"];
				if (succes) {
					var liste = data["liste"];
					var id_doublon_egal = data["id_doublon_egal"];
					var id_doublon_negatif = data["id_doublon_negatif"];
					var id_doublon_positif = data["id_doublon_positif"];
					/* Un recouvrement a été détecté */
					if ((id_doublon_egal > 0) || (id_doublon_negatif > 0) || (id_doublon_positif > 0)) {
						var message = "";
						if (id_doublon_egal > 0) {
							message = message+Global_label_distance_nulle+" ==> "+id_doublon_egal+"<br>";
						}
						if (id_doublon_negatif > 0) {
							message = message+Global_label_distance_negative+" ==> "+id_doublon_negatif+"<br>";
						}
						if (id_doublon_positif > 0) {
							message = message+Global_label_distance_positive+" ==> "+id_doublon_positif+"<br>";
						}
						$("p#message_alerte_discussion").html(message);
						$.magnificPopup.open({
							closeOnContentClick: false,
							closeOnBgClick: false,
							showCloseBtn: true,
							items: {
								src: $("#fenetre_alerte_discussion"),
								type: "inline"
							}
						});
					}
					/* Pas de recouvrement : on peut directement créer le fil */
					else {
						var liste_a_discuter = data["liste_a_discuter"];
						MEDIT.commentaires.action_inserer_fil_discussion(liste_a_discuter);
					}
				}
				else {
					var erreur = data["erreur"];
					alert(erreur);
				}
			});
		}
	},

	/* Réponse à un commentaire */
	evt_repondre_commentaire: function(id_fil_discussion, id_commentaire) {
		var ancre = $("a#lien_reponse_commentaire_"+id_fil_discussion+"_"+id_commentaire);
		if (ancre.length > 0) {
			var etat_min = ancre.hasClass("icone_repondre_commentaire_min");
			if (etat_min) {MEDIT.commentaires.afficher_zone_commentaire(id_fil_discussion, id_commentaire);}
			else {MEDIT.commentaires.masquer_zone_commentaire(id_fil_discussion, id_commentaire);}
		}
	},

	/* Validation d'un commentaire */
	evt_valider_commentaire: function(id_fil_discussion, id_commentaire) {
		var textarea = $("textarea#champ_reponse_commentaire_"+id_fil_discussion+"_"+id_commentaire);
		if (textarea.length > 0) {
			texte = textarea.val();
			if (texte.length > 0) {
				MEDIT.commentaires.action_inserer_commentaire(id_fil_discussion, id_commentaire, texte);
			}
			else {
				MEDIT.commentaires.masquer_zone_commentaire(id_fil_discussion, id_commentaire);
			}
		}
	},

	/* Annulation d'un commentaire */
	evt_annuler_commentaire: function(id_fil_discussion, id_commentaire) {
		var textarea = $("textarea#champ_reponse_commentaire_"+id_fil_discussion+"_"+id_commentaire);
		if (textarea.length > 0) {
			MEDIT.commentaires.masquer_zone_commentaire(id_fil_discussion, id_commentaire);
			textarea.val("");
		}
	},

	/* Suppression d'un fil de discussion */
	evt_supprimer_fil_discussion: function(id_fil_discussion) {
		demarrer_attente();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_supprimer_fil_discussion.php",
			data: {id_fil_discussion: id_fil_discussion},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {
				MEDIT.commentaires.maj_version_panneau_commentaires(id_fil_discussion, Global_version_article);
			}
			else {
				var message_erreur = data["erreur"];
				alert(message_erreur);
			}
			terminer_attente();
		});
	},

	/* Clic sur le lien vers la partie discutée */
	evt_atteindre_partie_discutee: function (id_fil_discussion) {
		demarrer_attente();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_atteindre_discussion.php",
			data: {id_fil_discussion: id_fil_discussion},
			dataType: "json"
		}).done(function(liste_contrib) {
			if (liste_contrib.length > 0) {
				var id_contrib = parseInt(liste_contrib[0]);
				MEDIT.general.atteindre("#p_"+id_contrib);
			}
			terminer_attente();
		});
	},

	/* Bascule entre état affiché et masqué du fil de discussion */
	evt_basculer_fil_discussion: function(id_fil_discussion) {
		var ancre = $("a#fil_discussion_ancre_minmax_"+id_fil_discussion);
		if (ancre.length > 0) {
			var fil = $("div#fil_discussion_commentaires_"+id_fil_discussion);
			if (fil.length > 0) {
				var etat_min = ancre.hasClass("fil_discussion_ancre_min");
				if (etat_min) {
					fil.slideUp(500);
					ancre.removeClass("fil_discussion_ancre_min").addClass("fil_discussion_ancre_max");
					ancre.removeClass("fa-minus-square").addClass("fa-plus-square");
				}
				else {
					fil.slideDown(500);
					ancre.removeClass("fil_discussion_ancre_max").addClass("fil_discussion_ancre_min");
					ancre.removeClass("fa-plus-square").addClass("fa-minus-square");
				}
				/* Stockage de la configuration dans un cookie */
				$.cookie(Global_cookie_fils+"_"+Global_id_article, MEDIT.commentaires.lire_config_fils());
			}
		}
	},

	/* Bascule entre état indenté ou non indenté dans le fil de discussion */
	evt_basculer_indentation: function(id_fil_discussion) {
		var ancre = $("a#fil_discussion_ancre_indentation_"+id_fil_discussion);
		if (ancre.length > 0) {
			var fil = $("div#fil_discussion_commentaires_"+id_fil_discussion);
			if (fil.length > 0) {
				var indentation_inactive = ancre.hasClass("icone_indenter_inactive");
				if (indentation_inactive) {
					fil.find("div.texte_commentaire_container").removeClass("fil_discussion_sans_indentation");
					fil.find("div.zone_reponse_commentaire").removeClass("fil_discussion_sans_indentation");
					ancre.removeClass("icone_indenter_inactive").removeClass("fa-align-right").addClass("fa-align-justify");
				}
				else {
					fil.find("div.texte_commentaire_container").addClass("fil_discussion_sans_indentation");
					fil.find("div.zone_reponse_commentaire").addClass("fil_discussion_sans_indentation");
					ancre.addClass("icone_indenter_inactive").removeClass("fa-align-justify").addClass("fa-align-right");
				}
				/* Stockage de la configuration dans un cookie */
				$.cookie(Global_cookie_fils+"_"+Global_id_article, MEDIT.commentaires.lire_config_fils());
			}
		}
	},

	/* Survol du lien vers la partie discutée : entrée */
	evt_allumer_partie_discutee: function(id_fil_discussion, est_nouveau) {
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_atteindre_discussion.php",
			data: {id_fil_discussion: id_fil_discussion},
			dataType: "json"
		}).done(function(liste_contrib) {
			var id_contrib;var cpt;
			var classe_survol = (est_nouveau)?"p_box_survol_discussion_nouveau":"p_box_survol_discussion";
			var nb_contribs = liste_contrib.length;
			for (cpt = 0;cpt < nb_contribs; cpt += 1) {
				id_contrib = parseInt(liste_contrib[cpt]);
				$("div#p_"+id_contrib).addClass(classe_survol);
			}
		});
	},

	/* Survol du lien vers la partie discutée : sortie */
	evt_eteindre_partie_discutee: function(id_fil_discussion) {
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_atteindre_discussion.php",
			data: {id_fil_discussion: id_fil_discussion},
			dataType: "json"
		}).done(function(liste_contrib) {
			var id_contrib;var cpt;
			var nb_contribs = liste_contrib.length;
			for (cpt = 0;cpt < nb_contribs; cpt += 1) {
				id_contrib = parseInt(liste_contrib[cpt]);
				$("div#p_"+id_contrib).removeClass("p_box_survol_discussion p_box_survol_discussion_nouveau");
			}
		});
	},

	/* Fonctions de service **********************************************************************/

	/* Positionnement sur un paragraphe */
	atteindre_fil_discussion: function(id_fil_discussion) {
		var elem = $("div#fil_discussion_"+id_fil_discussion);
		if (elem.length > 0) {
			var fil_discussion = elem.closest(".ui-layout-pane");
			/* var fil_discussion = $("div.ui-layout-pane-west"); */
			var offset = elem.position().top;
			var delta = fil_discussion.scrollTop();
			/* Le décalage prend en compte la hauteur de scrolling */
			var decalage = offset + delta;
			fil_discussion.animate({scrollTop: decalage},'fast'); 
		}
	},

	/* Fonction de service pour l'affichage d'un fil de discussion */
	afficher_zone_commentaire: function(id_fil_discussion, id_commentaire) {
		var zone = $("div#zone_reponse_commentaire_"+id_fil_discussion+"_"+id_commentaire);
		if (zone.length > 0) {
			var ancre = $("a#lien_reponse_commentaire_"+id_fil_discussion+"_"+id_commentaire);
			if (ancre.length > 0) {
				zone.slideDown(300);
				ancre.removeClass("icone_repondre_commentaire_min").addClass("icone_repondre_commentaire_max");
				ancre.removeClass("fa-reply").addClass("fa-ellipsis-h");
				/* Stockage de la configuration dans un cookie */
				$.cookie(Global_cookie_fils+"_"+Global_id_article, MEDIT.commentaires.lire_config_fils());
			}
		}
	},

	/* Fonction de service pour le masquage d'un fil de discussion */
	masquer_zone_commentaire: function(id_fil_discussion, id_commentaire) {
		var zone = $("div#zone_reponse_commentaire_"+id_fil_discussion+"_"+id_commentaire);
		if (zone.length > 0) {
			var ancre = $("a#lien_reponse_commentaire_"+id_fil_discussion+"_"+id_commentaire);
			if (ancre.length > 0) {
				zone.slideUp(300);
				ancre.removeClass("icone_repondre_commentaire_max").addClass("icone_repondre_commentaire_min");
				ancre.removeClass("fa-ellipsis-h").addClass("fa-reply");
				/* Stockage de la configuration dans un cookie */
				$.cookie(Global_cookie_fils+"_"+Global_id_article, MEDIT.commentaires.lire_config_fils());
			}
		}
	},

	/* Génération d'une chaine représentant la configuration des fils de discussion */
	lire_config_fils: function() {
		var config = "";
		$("div.fil_discussion_container").each(function() {
			var div_fil = $(this).attr("id");
			var id_fil = parseInt(div_fil.replace("fil_discussion_", ""));
			if (id_fil > 0) {
				/* Identifiant du fil de discussion */
				if (config.length > 0) {config = config + "|";}
				config = config + id_fil;
				/* Etat affiché (max) ou masqué (min) */
				var ancre_minmax = $(this).find("a#fil_discussion_ancre_minmax_"+id_fil);
				if (ancre_minmax.length > 0) {
					var etat_min = ancre_minmax.hasClass("fil_discussion_ancre_min");
					if (etat_min) {var etat_fil = 0;} else {var etat_fil = 1;}
					config = config + "," + etat_fil;
				}
				else {config = config + ",0";}
				/* Indentation active ou non */
				var ancre_indentation = $(this).find("a#fil_discussion_ancre_indentation_"+id_fil);
				if (ancre_indentation.length > 0) {
					var indentation = ancre_indentation.hasClass("icone_indenter_inactive");
					if (indentation) {var indentation_fil = 0;} else {var indentation_fil = 1;}
					config = config + "," + indentation_fil;
				}
				else {config = config + ",1";}
				/* Zones de réponse ouvertes */
				$(this).find("a[id^=lien_reponse_commentaire_"+id_fil+"_]").each(function() {
					var display = $(this).css("display");
					if ($(this).hasClass("icone_repondre_commentaire_max")) {
						var id_zone = $(this).attr("id");
						var id_commentaire = parseInt(id_zone.replace("lien_reponse_commentaire_"+id_fil+"_", ""));
						if (id_commentaire > 0) { 
							config = config + "," + id_commentaire;
						}
					}
				});
			}
		});
		return config;
	}
}

/* Lancement des opérations lorsque le document est prêt **************************************/
$(document).ready(function() {
	/* Clic sur le bouton d'insertion d'un fil de discussion */
	$("div#panneau_commentaires").on("click", "a#menu_commenter", function(event){
		var nodes = getSelectedNodes();
		if (nodes.length > 0) {
			MEDIT.commentaires.evt_verifier_fil_discussion(nodes);
		}
		return false;
	});
	
	/* Survol des liens vers les parties discutées : entrée */
	$("div#panneau_commentaires").on("mouseenter", "a.fil_discussion_ancre_lien", function() {
		var ancre_id = $(this).attr("id");
		var id_fil = parseInt(ancre_id.replace("fil_discussion_ancre_lien_", ""));
		var entete = $("div#fil_discussion_entete_"+id_fil);
		if (entete.length > 0) {
			var est_nouveau = entete.hasClass("fil_discussion_entete_nouveau");
		}
		else {
			var est_nouveau = false;
		}
		MEDIT.commentaires.evt_allumer_partie_discutee(id_fil, est_nouveau);
	});
	
	/* Survol des liens vers les parties discutées : sortie */
	$("div#panneau_commentaires").on("mouseleave", "a.fil_discussion_ancre_lien", function() {
		var ancre_id = $(this).attr("id");
		var id_fil = parseInt(ancre_id.replace("fil_discussion_ancre_lien_", ""));
		MEDIT.commentaires.evt_eteindre_partie_discutee(id_fil);
	});
	
	/* Démarrage de la surveillance de l'activité dans les commentaires */
	MEDIT.commentaires.maj_commentaires();
});