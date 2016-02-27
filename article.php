<?php
	// Initialisations
	define("_MEDIT_RACINE", ".");
	require_once(_MEDIT_RACINE."/classes/init.php");
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<meta http-equiv="X-UA-Compatible" content="IE=Edge;chrome=1">
<?php
	// Récupération des données de connexion
	$est_connecte = medit_uc_identification::Est_connecte();
	$id_connecte = medit_uc_identification::Id_connecte();
	$mode = medit_uc_mode::Lire_mode();

	// Récupération de l'article
	$id_article = (int) get("id_article");
	if ($id_article < 1) {die($_MEDIT_LANGUE->erreur_id_article_incorrect());}
	$article = medit_db_table_article::Construire($id_article);
	if (!($article->en_db())) {die($_MEDIT_LANGUE->erreur_id_article_inexistant($id_article));}
	
	// Récupération de la version de l'article
	$couple_derniere_version = $article->lire_no_derniere_version();
	list($no_derniere_version, $no_derniere_sous_version) = $couple_derniere_version;
	$code_derniere_version = coder_couple_version($couple_derniere_version);
	$id_version = $article->no_to_id_version($couple_derniere_version);

	// Purge des verrous suspendus
	medit_db_table_verrou::Purger();
	
	// Purge des évenements trop anciens dans le journal
	medit_db_table_journal::Purger();
?>
	<title>Medit | <?php echo $article->lire_nom();?></title>
	<link rel="stylesheet" type="text/css" href="css/tiers/fonts.css" />
	<link rel="stylesheet" type="text/css" href="css/tiers/layout.css" />
	<link rel="stylesheet" type="text/css" href="css/tiers/mfp.css" />
	<link rel="stylesheet" type="text/css" href="css/medit-general.css" />
	<link rel="stylesheet" type="text/css" href="css/medit-panneaux-divers.css" />
	<link rel="stylesheet" type="text/css" href="css/medit-article.css" />
	<link rel="stylesheet" type="text/css" href="css/medit-commentaires.css" />
	<link rel="stylesheet" type="text/css" href="css/medit-styles.css" />
	<link rel="stylesheet" type="text/css" href="css/medit-styles.php" />
</head>
<body>
	<?php
		// Initialisation de l'afficheur de widgets
		$html_widget = new medit_html_widget($id_article, $id_version);
	?>
	
	<!-- Panneau nord : infos -->
	<div id="panneau_infos" class="ui-layout-north">
		<?php
			// Ce panneau n'est pas composable : on l'affiche directement
			$html = medit_html_panneau_infos::Generer_panneau();
			echo $html;
		?>
	</div>

	<!-- Panneau sud -->
	<div class="ui-layout-south panneau_composable">
		<?php
			// Appel à l'afficheur de widgets
			$html_widget->afficher_widgets(medit_html_widget::HTML_PANNEAU_SUD);
		?>
	</div>
	
	<!-- Panneau ouest -->
	<div class="ui-layout-west panneau_composable">
		<?php
			// Appel à l'afficheur de widgets
			$html_widget->afficher_widgets(medit_html_widget::HTML_PANNEAU_OUEST);
		?>
	</div>
	
	
	<!-- Panneau est -->
	<div class="ui-layout-east panneau_composable">
		<?php
			// Appel à l'afficheur de widgets
			$html_widget->afficher_widgets(medit_html_widget::HTML_PANNEAU_EST);
		?>
	</div>

	<!-- Panneau central : article -->
	<div id="<?php echo _MEDIT_HTML_ID_PANNEAU_ARTICLE; ?>" class="ui-layout-center" >
		<?php
			// Barres d'outils
			if ($mode == medit_uc_mode::UC_MODE_EDITION) {
				$html = medit_html_panneau_article::Generer_barre_edition($id_article, $couple_derniere_version);
			}
			else {
				$html = medit_html_panneau_article::Generer_barre_versions($id_article, $couple_derniere_version);
				$html .= medit_html_panneau_article::Generer_barre_filtres($id_article, $couple_derniere_version);
			}
			// Article
			$html .= medit_html_panneau_article::Generer_zone_article($mode);
			echo $html;
		?>
	</div>
	
	<!-- Fenêtre de login (initialement cachée) -->
	<div id="fenetre_login" class="mfp-hide">
		<?php 
			$html = medit_html_panneau_login::Generer_panneau_login();
			echo $html;
		?>
	</div>
	
	<!-- Fenêtre d'alerte sur les commentaires (initialement cachée) -->
	<div id="fenetre_alerte_discussion" class="mfp-hide">
		<?php 
			$html = medit_html_widget_commentaires::Generer_panneau_alerte();
			echo $html;
		?>
	</div>

	<!-- Lancement des scripts tiers -->
	<script type="text/javascript" src="js/tiers/jq.js"></script>
	<script type="text/javascript" src="js/tiers/jq-ui.js"></script>
	<script type="text/javascript" src="js/tiers/jq-touchpunch.js"></script>
	<script type="text/javascript" src="js/tiers/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="js/tiers/mfp.js"></script>
	<script type="text/javascript" src="js/tiers/layout.js"></script>
	<script type="text/javascript" src="js/tiers/layout.state.js"></script>
	
	<!-- Lancement des scripts Medit -->
	<script type="text/javascript" src="js/medit-global.php?id=<?php echo (int) $id_article;?>"></script>
	<script type="text/javascript" src="js/medit-utils.js"></script>
	<script type="text/javascript" src="js/medit-general.js"></script>
	<script type="text/javascript" src="js/medit-editeur.js"></script>
	<script type="text/javascript" src="js/medit-sommaire.js"></script>
	<script type="text/javascript" src="js/medit-commentaires.js"></script>
	<script type="text/javascript" src="js/medit-journal.js"></script>
	<?php
		if ($mode == medit_uc_mode::UC_MODE_EDITION) {
			echo "<script type=\"text/javascript\" src=\"js/medit-article-edition.js\"></script>\n";
		}
		else {
			echo "<script type=\"text/javascript\" src=\"js/medit-article-historique.js\"></script>\n";
		}
	?>
</body>
</html>