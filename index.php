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
	<title>Medit</title>
	<link rel="stylesheet" type="text/css" media="all" href="css/medit.css" />
	<script type="text/javascript" src="js/tiers/jq.js"></script>
	<script type="text/javascript" src="js/tiers/jq-ui.js"></script>
	<script type="text/javascript" src="js/medit-utils.js"></script>
	<script type="text/javascript" src="js/medit-general.js"></script>
	<link rel="stylesheet" type="text/css" href="css/index.css" />
</head>
<body>
	<!-- Titre -->
	<div class="panneau_noir">
		<p style="text-align:center;padding-top:10px;"><img src="images/logo-medit-3.png" alt="Logo Medit" /></p>
		<h1 class="titre_niveau_1">Medit</h1>
	</div>
	<!-- Info connexion -->
	<div class="panneau_rouge">
		<?php
		$est_connecte = medit_uc_identification::Est_connecte();
		if ($est_connecte) {
			echo "<p class=\"info_connexion\">Vous êtes actuellement connecté en tant que ".medit_uc_identification::Pseudo_connecte()."</p>";
		}
		else {
			echo "<p class=\"info_connexion\">Vous êtes actuellement déconnecté.</p>";
		}
		?>
	</div>
	<!-- Affichage de la liste des articles -->
	<div class="panneau_blanc">
<?php
	$collection = medit_db_table_collection::Construire();
	$liste_articles = $collection->lire_liste_articles();
	$nb_articles = count($liste_articles);
	$label_article = "article".(($nb_articles > 1)?"s":"");
	echo "<h2 class=\"titre_niveau_2\">{$nb_articles} {$label_article}&nbsp;:</h2>\n";
	echo "<ul>\n";
	foreach ($liste_articles as $id_article) {
		$article = medit_db_table_article::Construire($id_article);
		$nom = $article->lire_nom();
		list($no_derniere_version, $no_derniere_sous_version) = $article->lire_no_derniere_version();
		echo "<li><a href='article.php?id_article={$id_article}' title='Ouvrir cet article'>{$nom}</a><span class=\"info_no_version\"> Version {$no_derniere_version}.{$no_derniere_sous_version}</span></li>\n";
	}
	echo "</ul>\n";
?>
	</div>
	<br><br><br>
	<!-- Mise en cache du script CK Editor -->
	<script type="text/javascript" src="js/tiers/ckeditor/ckeditor.js"></script>
</body>
</html>