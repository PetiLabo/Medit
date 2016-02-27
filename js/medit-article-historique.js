/*
 * Javascript pour la gestion des articles en mode historique
 */

var MEDIT = MEDIT || {};

MEDIT.historique = {

	/* Chargement d'une version d'article dans le panneau central */
	maj_historique_panneau_article: function(code_version) {
		$.ajax({
			type: "POST",
			url: "classes/ajax/maj_historique_panneau_article.php",
			data: {id_article: Global_id_article, code_version: code_version},
			dataType: "html"
		}).done(function(data) {
			$("#texte_panneau_article").html(data+"<div class='pied_article_historique'></div>");
		});
	},

	/* Callbacks sur les événements IHM ***********************************************************/

	/* Déclenchement du changement vers la version précédente */
	evt_change_version_precedente: function(code_version) {
		demarrer_attente();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_version_precedente.php",
			data: {id_article: Global_id_article, code_version: code_version},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {
				var nouveau_code_version = data["code_version"];
				MEDIT.general.maj_version_panneau_infos(nouveau_code_version);
				MEDIT.historique.maj_historique_panneau_article(nouveau_code_version);
				MEDIT.sommaire.maj_version_panneau_sommaire(nouveau_code_version);
			}
			Global_version_article = code_version;
			terminer_attente();
		});
	},

	/* Déclenchement du changement vers la version précédente */
	evt_change_version_suivante: function(code_version) {
		demarrer_attente();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_version_suivante.php",
			data: {id_article: Global_id_article, code_version: code_version},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {
				var nouveau_code_version = data["code_version"];
				MEDIT.general.maj_version_panneau_infos(nouveau_code_version);
				MEDIT.historique.maj_historique_panneau_article(nouveau_code_version);
				MEDIT.sommaire.maj_version_panneau_sommaire(nouveau_code_version);
			}
			Global_version_article = code_version;
			terminer_attente();
		});
	},

	/* Filtrage du texte par éditeur */
	evt_filtrer_editeur: function(element) {
		demarrer_attente();
		var valeur = $(element).val();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_filtrer_editeur.php",
			data: {id_article: Global_id_article, valeur: valeur},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {
				MEDIT.historique.maj_historique_panneau_article(Global_version_article);
			}
			else {alert(data["erreur"]);}
			terminer_attente();
		});
	},

	/* Activation / désactivation du filtre des annotations */
	evt_basculer_filtre_annotation: function(element) {
		demarrer_attente();
		var checked = $(element).is(":checked");
		var valeur = checked?1:0;
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_basculer_filtre_annotation.php",
			data: {id_article: Global_id_article, valeur: valeur},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {
				$("select#i_outil_filtre_type_annotation").attr("disabled", !(checked));
				$("select#i_outil_filtre_annotateur").attr("disabled", !(checked));
				if (checked) {$("span#icone_filtre_annotation").removeClass("icone_filtrer_inactive");}
				else {$("span#icone_filtre_annotation").addClass("icone_filtrer_inactive");}
				MEDIT.historique.maj_historique_panneau_article(Global_version_article);
			}
			else {alert(data["erreur"]);}
			terminer_attente();
		});
	},

	/* Filtrage par type d'annotation */
	evt_filtrer_type_annotation: function(element) {
		demarrer_attente();
		var valeur = $(element).val();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_filtrer_type_annotation.php",
			data: {id_article: Global_id_article, valeur: valeur},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {
				MEDIT.historique.maj_historique_panneau_article(Global_version_article);
			}
			else {alert(data["erreur"]);}
			terminer_attente();
		});
	},

	/* Filtrage par annotateur */
	evt_filtrer_annotateur: function(element) {
		demarrer_attente();
		var valeur = $(element).val();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_filtrer_annotateur.php",
			data: {id_article: Global_id_article, valeur: valeur},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {
				MEDIT.historique.maj_historique_panneau_article(Global_version_article);
			}
			else {alert(data["erreur"]);}
			terminer_attente();
		});
	},

	/* Activation / désactivation du filtre des commentaires */
	evt_basculer_filtre_commentaire: function(element) {
		var checked = $(element).is(":checked");
		var valeur = checked?1:0;
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_basculer_filtre_commentaire.php",
			data: {id_article: Global_id_article, valeur: valeur},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {
				$("select#i_outil_filtre_commentateur").attr("disabled", !(checked));
				if (checked) {$("span#icone_filtre_commentaire").removeClass("icone_filtrer_inactive");}
				else {$("span#icone_filtre_commentaire").addClass("icone_filtrer_inactive");}
				MEDIT.historique.maj_historique_panneau_article(Global_version_article);
			}
			else {alert(data["erreur"]);}
			terminer_attente();
		});
	},

	/* Filtrage par commentateur */
	evt_filtrer_commentateur: function(element) {
		demarrer_attente();
		var valeur = $(element).val();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_filtrer_commentateur.php",
			data: {id_article: Global_id_article, valeur: valeur},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {
				MEDIT.historique.maj_historique_panneau_article(Global_version_article);
			}
			else {alert(data["erreur"]);}
			terminer_attente();
		});
	}
}

/* Lancement des opérations lorsque le document est prêt **************************************/
$(document).ready(function() {
	/* Survol des annotations */
	$("div#texte_panneau_article").on("mouseover", "span.style_annotation", function() {$(this).css("cursor", "default");});

	/* Changements de versions d'article */
	$("#barre_outils_versions").on("click", "#a_info_version_moins", function(event){
		var version_en_cours = $("#s_no_version_info").text();
		if (version_en_cours.length > 2) {MEDIT.historique.evt_change_version_precedente(version_en_cours);}
		return false;
	});
	$("#barre_outils_versions").on("click", "#a_info_version_plus", function(event){
		var version_en_cours = $("#s_no_version_info").text();
		if (version_en_cours.length > 2) {MEDIT.historique.evt_change_version_suivante(version_en_cours);}
		return false;
	});
});