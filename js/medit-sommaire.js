/*
 * Javascript pour la gestion du sommaire dans l'IHM Medit
 */

var MEDIT = MEDIT || {};

MEDIT.sommaire = {
	/* Lancement de requêtes Ajax *****************************************************************/

	/* Chargement d'une version d'article dans le panneau sommaire */
	maj_version_panneau_sommaire: function(code_version) {
		$.ajax({
			type: "POST",
			url: "classes/ajax/maj_version_panneau_sommaire.php",
			data: {id_article: Global_id_article, code_version: code_version},
			dataType: "html"
		}).done(function(html) {
			$("div#zone_panneau_sommaire").replaceWith(html);
		});
		Global_version_article = code_version;
	}
}

/* Lancement des opérations lorsque le document est prêt **************************************/
$(document).ready(function() {
	/* Sommaire : Clic sur les liens vers les titres de l'article */
	$("body").on("click", "a.lien_entree_sommaire", function() {MEDIT.general.atteindre($(this).attr("href"));return false;});
});