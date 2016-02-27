/*
 * Javascript pour la gestion des articles Medit
 */

var MEDIT = MEDIT || {};

MEDIT.edition = {
	/* Lancement de requêtes Ajax *****************************************************************/

	/* Chargement d'une version d'article dans le panneau central */
	maj_edition_panneau_article: function() {
		$.ajax({
			type: "POST",
			url: "classes/ajax/maj_edition_panneau_article.php",
			data: {id_article: Global_id_article},
			dataType: "json"
		}).done(function(data) {
			var code_version = data["code_version"];
			var code_html = data["html"];
			$("#texte_panneau_article").html(code_html+"<div class='pied_article_edition'></div>");
			/* Déclenchement de l'éditeur en ligne */
			MEDIT.editeur.declencher_editeurs(Global_langage);
			Global_version_article = code_version;
		});
	},

	/* Mise à jour des paragraphes verrouillées et déverrouilés            */
	/* TODO : Alléger le système en passant par les événements journalisés */
	maj_verrou_paragraphe: function() {
		$.ajax({
			type: "POST",
			url: "classes/ajax/maj_verrou_paragraphe.php",
			data: {id_article: Global_id_article},
			dataType: "json"
		}).done(function(data) {
			/* Le refraichissement de l'affichage n'a lieu qu'en mode édition */
			if (Global_mode_gestion_article == 1) {
				var liste_verrous = data["liste_verrous"];
				/* Paragraphes à déverrouiller : on cherche les wrappers de verrous (f_<id_verrou>) */
				/* et on déverrouille ceux qui ne sont plus dans la liste                           */
				$("div.verrou_wrapper").each(function(index) {
					var f_id = $(this).attr("id");
					var id_verrou = parseInt(f_id.replace("f_", ""));
					var encore_ferme = (id_verrou in liste_verrous);
					if (!(encore_ferme)) {
						$.ajax({
							type: "POST",
							url: "classes/ajax/maj_texte_paragraphe.php",
							data: {id_article: Global_id_article, id_verrou: id_verrou},
							dataType: "json"
						}).done(function(data) {
							// TODO : Gérer le var a_change = data["a_change"];
							var id_verrou = data["id_verrou"];
							var html = data["html"];
							remplacer_paragraphes(id_verrou, html);
						});
					}
				});
				/* Paragraphes à verrouiller : on parcourt la liste des verrous et on verrouille */
				/* ceux qui ne sont pas déjà verrouillés                                         */
				$.each(liste_verrous, function(id_verrou, infos) {
					var edition_wrapper = $("#v_"+id_verrou);
					var f_id = "f_"+id_verrou;
					var verrou_wrapper = $("#"+f_id);
					if ((verrou_wrapper.length == 0) && (edition_wrapper.length == 0)) {
						var pseudo = infos["pseudo"];
						var liste_paragraphes = infos["liste_paragraphes"];
						var nb_paragraphes = liste_paragraphes.length;
						var selecteur_paragraphes = "#p_"+liste_paragraphes[0];
						for (var i = 1; i < nb_paragraphes; i += 1) {
							selecteur_paragraphes = selecteur_paragraphes+",#p_"+liste_paragraphes[i];
						}
						var ensemble_verrou = $(selecteur_paragraphes);
						ensemble_verrou.wrapAll("<div id=\""+f_id+"\" class=\"verrou_wrapper\"></div>");
						ensemble_verrou.removeClass("p_box_ouvert").addClass("p_box_ferme");
						/* TODO : Changer l'icone en fonction du type de verrou (édition, annotation) Cf PHP Generer_lecture */
						var s_verrouilleur = ensemble_verrou.find("span.info_pseudo_verrouilleur");
						s_verrouilleur.html(pseudo);
					}
				});
			}
			/* Réarmement du timer */
			window.setTimeout(MEDIT.edition.maj_verrou_paragraphe, 1000 * parseInt(Global_intervalle_panneau_article));
			/* On remet à jour systématiquement le no de version */
			var code_version = data["code_version"];
			mettre_a_jour_version(code_version);
		});
	},

	/* Callbacks sur les événements IHM : *********************************************************/

	/* Création d'une nouvelle version majeure         */
	/* TODO : Revoir le passage du message en argument */
	evt_creer_nouvelle_version: function(message, no_nouvelle_version) {
		var confirmation = confirm(message);
		if (confirmation == true) {
			demarrer_attente();
			$.ajax({
				type: "POST",
				url: "classes/ajax/action_creation_version_majeure.php",
				data: {id_article: Global_id_article, no_nouvelle_version: no_nouvelle_version},
				dataType: "json"
			}).done(function(data) {
				terminer_attente();
				var succes = data["succes"];
				if (succes) {
					$(location).attr("href","article.php?id_article="+Global_id_article);
				}
				else {alert(data["erreur"]);}
			});
		}
	},

	/* Déclenchement de l'édition d'une liste de paragraphes       */
	/* TODO : Réfléchir sur la question de passer le n° de version */
	evt_verrouillage_contribution: function(id_article, type_verrou, nodes) {
		var nb_nodes = nodes.length;
		if (nb_nodes > 0) {
			demarrer_attente();
			$.ajax({
				type: "POST",
				url: "classes/ajax/action_verrouillage_paragraphe.php",
				data: {id_article: id_article, type_verrou: type_verrou, liste_paragraphes: nodes},
				dataType: "json"
			}).done(function(data) {
				var succes = data["succes"];
				if (succes) {
					var liste_paragraphes = data["liste_paragraphes"];
					var contenu_html = data["html"];
					var nb_paragraphes = liste_paragraphes.length;
					if (nb_paragraphes > 0) {
						// TODO : Placer cette partie dans une fonction
						var paragraphe_0 = "#"+liste_paragraphes[0];
						var origine = $(paragraphe_0);
						if (nb_paragraphes > 1) {
							var paragraphes_1_n = "#"+liste_paragraphes[1];
							for (var i = 2; i < nb_paragraphes; i += 1) {
								paragraphes_1_n = paragraphes_1_n+",#"+liste_paragraphes[i];
							}
							$(paragraphes_1_n).remove();
						}
						origine.replaceWith(contenu_html);
						effacer_selection();
						MEDIT.editeur.declencher_editeurs(Global_langage);
					}
				}
				else {alert(data["erreur"]);}
				terminer_attente();
			});
		}
	},

	/* Déclenchement de la validation de l'édition sous un verrou */
	evt_validation_edition: function(id_article, code_version, no_verrou) {
		var id_texte = "txt_"+no_verrou;
		var val_texte = MEDIT.editeur.lire_texte_editeur(id_texte);
		demarrer_attente();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_validation_contribution.php",
			data: {id_article: id_article, code_version: code_version, id_verrou: no_verrou, texte: val_texte},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			MEDIT.editeur.fermer_editeur(id_texte);
			if (succes) {
				var p_editable = $("#v_"+no_verrou);
				p_editable.replaceWith(data["html"]);
				effacer_selection();
			}
			else {alert(data["erreur"]);}
			var code_version = data["code_version"];
			mettre_a_jour_version(code_version);
			terminer_attente();
		});
	},

	/* Déclenchement de l'annulation de l'édition sous un verrou */
	evt_annulation_edition: function(id_article, code_version, no_verrou) {
		var id_texte = "txt_"+no_verrou;
		demarrer_attente();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_annulation_contribution.php",
			data: {id_article: id_article, code_version: code_version, id_verrou: no_verrou},
			dataType: "html"
		}).done(function(data) {
			// TODO : Prendre le no_verrou en retour dans le JSON
			var p_editable = $("#v_"+no_verrou);
			MEDIT.editeur.fermer_editeur(id_texte);
			p_editable.replaceWith(data);
			effacer_selection();
			terminer_attente();
		});
	}
}

/* Lancement des opérations lorsque le document est prêt **************************************/
$(document).ready(function() {
	/* Survol des annotations */
	$("div#texte_panneau_article").on("mouseover", "span.style_annotation", function() {$(this).css("cursor", "default");});

	/* Gestion du menu des paragraphes déverrouillés : bouton éditer */
	$("#barre_outils_article").on("click", "a#menu_editer", function(event){
		var nodes = getSelectedNodes();
		if (nodes.length > 0) {
			MEDIT.edition.evt_verrouillage_contribution(Global_id_article, 1, nodes);
		}
		return false;
	});
	
	/* Gestion du menu des paragraphes déverrouillés : bouton annoter */
	$("#barre_outils_article").on("click", "a#menu_annoter", function(event){
		var nodes = getSelectedNodes();
		if (nodes.length > 0) {
			MEDIT.edition.evt_verrouillage_contribution(Global_id_article, 2, nodes);
		}
		return false;
	});
	 
	/* Survol des paragraphes verrouillés */
	$("#texte_panneau_article").on("mouseenter", ".p_box_ferme", function() {
		var id_box = $(this).attr("id");
		var largeur_box = $(this).width();
		var hauteur_box = $(this).height();
		var id_verrou = id_box.replace("p_", "verrou_");
		$("#"+id_box).css("cursor", "default");
		$("#"+id_box+" .texte").css("opacity", "0.2");
		$("#"+id_verrou).css("width", (largeur_box - 10)+"px").css("margin-top", ((hauteur_box / 2) - 16)+"px").css("display", "block");
	});
	$("#texte_panneau_article").on("mouseleave", ".p_box_ferme", function() {
		var id_box = $(this).attr("id");
		var id_verrou = id_box.replace("p_", "verrou_");
		$("#"+id_box+" .texte").css("opacity", "1");
		$("#"+id_verrou).css("display", "none").css("width", "0").css("margin-top", "0");
	});

	/* Démarrage du timer : setInterval évité en cas de problèmes de lenteur */
	window.setTimeout(MEDIT.edition.maj_verrou_paragraphe, 1000 * parseInt(Global_intervalle_panneau_article));
});