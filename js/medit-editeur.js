/*
 * Javascript pour la gestion de CK Editor
 */

var MEDIT = MEDIT || {};

MEDIT.editeur = {
	/* Déclenchement de tous les éditeurs dans les textarea */
	declencher_editeurs: function(langue) {
		$("textarea[id^='txt_']").each(function() {
			var id_editeur = $(this).attr("id");
			var attr_nom = $(this).attr("name");
			var type_editeur = parseInt(attr_nom.replace("type_", ""));
			MEDIT.editeur.ouvrir_editeur(id_editeur, type_editeur, langue);
		});
	},

	/* Ouverture d'un éditeur */
	ouvrir_editeur: function(id_editeur, type_editeur, langue) {
		/* On vérifie que l'instance n'existe pas déjà */
		var instance = CKEDITOR.instances[id_editeur];
		if (instance) {return;}
		
		/* On vérifie le verrou */
		var id_verrou = parseInt(id_editeur.replace("txt_", ""));
		if (id_verrou == 0) {return;}
		
		/* On vérifie que le type d'éditeur demandé est correct */
		if (type_editeur == 1) {
			var fichier_config = "config-edition.js";
			var nom_liste_annotations = false;
		}
		else if (type_editeur == 2) {
			var fichier_config = "config-annotation.js";
			/* Pour les annotations, on crée le stylesset à la volée pour inclure les infos sur l'auteur */
			var nom_liste_annotations = "set_annotations_"+Global_id_connecte;
			var id_auteur = "auteur_"+Global_id_connecte;
			var stylesset = CKEDITOR.stylesSet.get(nom_liste_annotations);
			/* On ne crée le stylesset que s'il n'existe pas encore */
			if (stylesset == null) {
				/* TODO : Produire le stylesset à partir de la BDD */
				CKEDITOR.stylesSet.add(nom_liste_annotations, Global_stylesset_annotations);
			}
		}
		else {
			return;
		}

		/* Création de l'instance de CKEditor */
		var editeur = CKEDITOR.replace(id_editeur, {
			customValues: { param_verrou: id_verrou, param_article: Global_id_article, param_version: Global_version_article },
			customConfig: fichier_config,
			stylesSet: nom_liste_annotations,
			language: langue
		});
		
		if (type_editeur == 2) {
			/* Désactivation du clavier en annotation */
			editeur.on("key", function( event ) {event.cancel();});
			editeur.on("paste", function( event ) {event.cancel();});
			editeur.on("drop", function( event ) {event.cancel();});
			/* PB : Si on supprime le contenteditable, le cleanup styles ne fonctionne plus...
			editeur.on("contentDom", function() {
				var editable = this.editable();
				if (editable) {
					editable.changeAttr("contenteditable", "false");
				}
			});
			*/
		}
		
		/* Bouton Annuler */
		editeur.addCommand("commande_annuler", {
			exec: function(editeur) {
				var param_verrou = editeur.config.customValues.param_verrou;
				var param_article = editeur.config.customValues.param_article;
				var param_version = editeur.config.customValues.param_version;
				MEDIT.edition.evt_annulation_edition(param_article, param_version, param_verrou);
			}
		});
		editeur.ui.addButton('bouton_annuler', {
			label: Global_label_annuler,
			command: 'commande_annuler',
			toolbar: 'sortie',
			icon: 'images/cross-button.png'
		});

		/* Bouton Valider */
		editeur.addCommand("commande_valider", {
			exec: function(editeur) {
				var param_verrou = editeur.config.customValues.param_verrou;
				var param_article = editeur.config.customValues.param_article;
				var param_version = editeur.config.customValues.param_version;
				MEDIT.edition.evt_validation_edition(param_article, param_version, param_verrou);
			}
		});
		editeur.ui.addButton('bouton_valider', {
			label: Global_label_valider,
			command: 'commande_valider',
			toolbar: 'sortie',
			icon: 'images/tick-button.png'
		});

		/* Positionnement en haut à droite des deux boutons */
		CKEDITOR.on("instanceReady", function (evt) {
			  $(".cke_button__bouton_valider").closest(".cke_toolbar").css({ "float": "right" });
		});
	},

	/* Fermeture d'un éditeur */
	fermer_editeur: function(id_editeur) {
		var instance = CKEDITOR.instances[id_editeur];
		if (instance) {
			var etat = instance.commands.maximize.state;
			/* Si maximisé => on switche vers l'état minimisé */
			if (etat == 1) {
				instance.execCommand("maximize");
			}
			instance.destroy(); 
		}
	},

	/* Lecture du texte dans l'éditeur donné */
	lire_texte_editeur: function(id_texte) {
		var texte = CKEDITOR.instances[id_texte].getData();
		return texte;
	}
}
