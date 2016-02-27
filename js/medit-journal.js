/*
 * Javascript pour la gestion du journal dans l'IHM Medit
 */

var MEDIT = MEDIT || {};

MEDIT.journal = {
	Pointeur_id_journal: 0,

	/* Lancement de requêtes Ajax *****************************************************************/

	/* Mise à jour des événements dans le panneau journal */
	maj_journal: function() {
		$.ajax({
			type: "POST",
			url: "classes/ajax/maj_panneau_journal.php",
			data: {id_article: Global_id_article, id_journal: MEDIT.journal.Pointeur_id_journal},
			dataType: "json"
		}).done(function(data) {
			MEDIT.journal.Pointeur_id_journal = parseInt(data["id_journal"]);
			var html = data["html"];
			if (html.length > 0) {MEDIT.journal.journaliser(html, true);}
			window.setTimeout(MEDIT.journal.maj_journal, 1000 * parseInt(Global_intervalle_panneau_journal));
		});
	},

	/* Déclenchement du changement de dimension du journal */
	evt_changement_dimension_journal: function(nouvelle_dimension) {
		demarrer_attente();
		$.ajax({
			type: "POST",
			url: "classes/ajax/action_changement_dim_journal.php",
			data: {id_article: Global_id_article, dimension: nouvelle_dimension, id_journal: MEDIT.journal.Pointeur_id_journal},
			dataType: "json"
		}).done(function(data) {
			MEDIT.journal.Pointeur_id_journal = parseInt(data["id_journal"]);
			var html = data["html"];
			if (html.length > 0) {MEDIT.journal.journaliser(html, false);}
			Global_dimension_journal = nouvelle_dimension;
			terminer_attente();
		});
	},

	/* Gestion des panneaux **********************************************************************/

	/* Journalisation */
	journaliser: function(message, avec_animation) {
		var journal = $("div#zone_panneau_journal");
		if (avec_animation) {
			var nb_evts = journal.find("p").length;
			var ajout = $(message);
			var tab_evt = [];
			ajout.each(function() {
				if (nb_evts >= Global_dimension_journal) {
					/* On pousse vers la liste à scroller */
					tab_evt.push($(this));
				}
				else {
					journal.append($(this));
					nb_evts = nb_evts + 1;
				}
			});
			/* Appel de l'utilitaire gérant le scrolling des événements */
			scroller_journal(journal, tab_evt);
		}
		else {
			journal.html(message);
		}
	}
}

/* Lancement des opérations lorsque le document est prêt **************************************/
$(document).ready(function() {
	/* Changement du nombre d'événements */
	/* TODO : Déplacer cette écriture dans onchange (cf barre filtres historique) */
	$("select#s_outil_dimension_journal").change(function() {
		var val = $(this).val();
		if (val != Global_dimension_journal) {
			MEDIT.journal.evt_changement_dimension_journal(val);
		}
	});
	
	/* Changement du filtre sur les articles */
	$("input:checkbox#i_outil_case_article_journal").change(function() {
		var val = $(this).is(":checked");
		alert(val);
	});
	
	/* Démarrage de la journalisation */
	MEDIT.journal.evt_changement_dimension_journal(Global_dimension_journal);
	window.setTimeout(MEDIT.journal.maj_journal, 1000 * parseInt(Global_intervalle_panneau_journal));
});