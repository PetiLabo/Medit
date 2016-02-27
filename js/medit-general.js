/*
 * Javascript pour la gestion générale de l'IHM Medit
 */

var MEDIT = MEDIT || {};

MEDIT.general = {
	/* Lancement de requêtes Ajax *****************************************************************/

	/* Chargement d'une version d'article dans le panneau infos */
	maj_version_panneau_infos: function(code_version) {
		$.ajax({
			type: "POST",
			url: "classes/ajax/maj_version_panneau_infos.php",
			data: {id_article: Global_id_article, code_version: code_version},
			dataType: "json"
		}).done(function(data) {
			$("span#s_no_version_info").text(code_version);
			$("span#s_horodatage_info_1").text(data["date"]);
			$("span#s_horodatage_info_2").text(data["heure"]);
			$("span#s_auteur_info").text(data["pseudo"]);
		});
		Global_version_article = code_version;
	},

	/* Callbacks sur les événements IHM ***********************************************************/

	/* Déclenchement du changement de mode */
	evt_changement_mode: function(nouveau_mode) {
		demarrer_attente();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_changement_mode.php",
			data: {mode: nouveau_mode},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			var valeur_mode = parseInt(data["mode"]);
			if (succes) {	
				Global_mode_gestion_article = valeur_mode;
			}
			else {
				var message_erreur = data["erreur"];
				alert(message_erreur);
			}
			$(location).attr("href","article.php?id_article="+Global_id_article);
			terminer_attente();
		});
	},

	/* Déclenchement du bouton déconnexion */
	evt_deconnexion: function() {
		demarrer_attente();
		$.ajax({
			url: "classes/ajax/action_deconnexion.php",
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {
				$(location).attr("href","article.php?id_article="+Global_id_article);
			}
			else {
				alert(data["erreur"]);
				$("p.p_outil_info_connexion").replaceWith(data["html"]);
			}
			terminer_attente();
		});
	},

	/* Tentative de connexion */
	evt_connexion: function() {
		var identifiant = $("input#i_login_id").val();
		var mot_de_passe = Sha1.hash($("input#i_login_mdp").val());
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_login.php",
			data: {i_login_id: identifiant, i_login_mdp: mot_de_passe},
			dataType: "json"
		}).done(function(data) {
			var succes = data["succes"];
			if (succes) {
				$(location).attr("href","article.php?id_article="+Global_id_article);
			}
			else {
				$("#fenetre_login").addClass("shake");
				setTimeout(function(){$("#fenetre_login").removeClass("shake");},500);
				$("#message_erreur_login").css("opacity", "0").html(data["message"]).animate({opacity: 1},500);
				setTimeout(function(){$("#message_erreur_login").animate({opacity: 0},500);},2500);
			}
		});
		return false;
	},

	/* Gestion des panneaux **********************************************************************/

	/* Positionnement sur un paragraphe */
	atteindre: function(id_contrib) {
		var elem = $(id_contrib);
		if (elem.length > 0) {
			var article = $("div#texte_panneau_article");
			var offset = elem.position().top;
			var delta = article.scrollTop();
			/* Le décalage prend en compte la hauteur de scrolling */
			var decalage = offset + delta - 70;
			article.animate({scrollTop: decalage},'slow'); 
		}
	},

	/* Génération d'une chaine représentant la configuration des panneaux composables */
	lire_config_panneaux: function() {
		var columns = [];
		/* On lit dans le sens ouest-est-sud */
		$(".ui-layout-west").each(function() {
			columns.push($(this).sortable("toArray").join(","));
		});
		$(".ui-layout-east").each(function() {
			columns.push($(this).sortable("toArray").join(","));
		});
		$(".ui-layout-south").each(function() {
			columns.push($(this).sortable("toArray").join(","));
		});
		ret = columns.join('|');
		return ret;
	}
}

/* Lancement des opérations lorsque le document est prêt **************************************/
$(document).ready(function() {
	/* Mise en place des panneaux */
	$("body").layout({
		applyDefaultStyles: true,
		defaults: {fxName: "slide", fxSpeed: "fast", spacing_closed: 10, initClosed: false},
		north__size: 52,
		south__size: 108,
		north__resizable: false,
		livePaneResizing: true,
		animatePaneSizing: true,
		stateManagement__enabled: true,
		stateManagement__autoSave: true,
		stateManagement__autoLoad: true,
		stateManagement__cookie: { name: Global_cookie_layout}
	});

	/* Sauvegarde de la configuration des panneaux dans un cookie */
	$(".panneau_composable").sortable({
		connectWith: ".panneau_composable",
		update: function() {$.cookie(Global_cookie_sortable, MEDIT.general.lire_config_panneaux());}
	});

	/* Attachement de l'événement sur tentative de login */
	$("form.f_login").submit(MEDIT.general.evt_connexion);
	
	/* Chargement de la version de l'article */
	if (Global_mode_gestion_article == 1) {
		MEDIT.edition.maj_edition_panneau_article();
	}
	else {
		MEDIT.historique.maj_historique_panneau_article(Global_version_article);
	}
	
	/* Activation du drag and drop */
    $("div.panneau_composable").sortable({
		connectWith: "div.panneau_composable",
		handle: "a.icone_poignee_deplacement",
		placeholder : "drop_zone",
		helper: function (e,ui) {return $(ui).clone().appendTo('body').show();} 
    });

	/* TODO : Déplacer cette écriture dans onchange (cf barre filtres historique) */
	/* Infos : Changement de mode */
	$("input:radio.i_radio_mode").change(function() {
		var nouveau_mode = $(this).val();
		MEDIT.general.evt_changement_mode(nouveau_mode);
		return false;
	});

	/* Connexion */
	$("#panneau_infos").on("click", "#a_connexion", function(event){
		$.magnificPopup.open({
			closeOnContentClick: false,
			closeOnBgClick: false,
			showCloseBtn: true,
			items: {
				src: $("#fenetre_login"),
				type: "inline"
			}
		});
		return false;
	});

	/* Déconnexion */
	$("#panneau_infos").on("click", "a#a_deconnexion", function(event){
		MEDIT.general.evt_deconnexion();
		return false;
	});
});