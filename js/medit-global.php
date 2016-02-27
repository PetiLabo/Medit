<?php 
	header("Content-type: application/javascript; charset=utf-8");

	// Initialisations
	define("_MEDIT_RACINE", "..");
	require_once(_MEDIT_RACINE."/classes/init.php");

	global $_MEDIT_LANGUE;

	// Récupération des infos sur article et version
	$id_article = (int) get("id");
	if ($id_article > 0) {
		$article = medit_db_table_article::Construire($id_article);
		$couple_derniere_version = $article->lire_no_derniere_version();
		$code_derniere_version = coder_couple_version($couple_derniere_version);
	}
	else {
		$code_derniere_version = "0.0";
	}

	// Récupération des données de connexion
	$est_connecte = medit_uc_identification::Est_connecte();
	$id_connecte = medit_uc_identification::Id_connecte();
	$mode = medit_uc_mode::Lire_mode();
	
	// Préparation des informations pour les annotations
	if ($est_connecte) {
		$auteur = medit_db_table_auteur::Construire($id_connecte);
		$pseudo = ($auteur->en_db())?$auteur->lire_pseudo():$_MEDIT_LANGUE->pseudo_inconnu();
		$liste_types_annotation = medit_db_table_type_annotation::Liste_types_annotation();
		$stylesset_js = "[";
		foreach ($liste_types_annotation as $id_type_annotation) {
			$type_annotation = medit_db_table_type_annotation::Construire($id_type_annotation);
			$label_annotation = $type_annotation->lire_label_annotation();
			$stylesset_js .= "{name: \"{$label_annotation}\", element: \"span\", attributes: ";
			$stylesset_js .= "{\"class\": \"style_annotation style_annotation_{$id_type_annotation}\", \"title\": \"{$pseudo} : {$label_annotation}\", \"name\": \"id_auteur_{$id_connecte}\" }},";
		}
		$stylesset_js .= "]";
	}
	else {
		$stylesset_js = "false";
	}

	// Paramètres IHM stockés dans les cookies
	$dim_journal = medit_html_widget_journal::Lire_dimension_journal($id_article);

	// Génération des variables globales
	echo "/* Variables globales générales */\n";
	echo "var Global_id_article = ".$id_article.";\n";
	echo "var Global_version_article = \"".$code_derniere_version."\";\n";
	echo "var Global_est_connecte = ".(int) $est_connecte.";\n";
	echo "var Global_id_connecte = ".(int) $id_connecte.";\n";
	echo "var Global_mode_gestion_article = ".$mode.";\n";
	echo "var Global_langage = \"".medit_config::LANG_MEDIT."\";\n";
	echo "var Global_cookie_layout = \"".medit_config::HTML_COOKIE_LAYOUT."\";\n";
	echo "var Global_cookie_sortable = \"".medit_config::HTML_COOKIE_SORTABLE."\";\n";
	echo "var Global_cookie_fils = \"".medit_config::HTML_COOKIE_FILS."\";\n";
	echo "var Global_dimension_journal = ".$dim_journal.";\n";
	echo "var Global_intervalle_panneau_article = ".medit_config::JS_INTERVALLE_ARTICLE.";\n";
	echo "var Global_intervalle_panneau_commentaires = ".medit_config::JS_INTERVALLE_COMMENTAIRES.";\n";
	echo "var Global_intervalle_panneau_journal = ".medit_config::JS_INTERVALLE_JOURNAL.";\n";
	echo "\n/* Variable globale pour CKEditor */\n";
	echo "var Global_stylesset_annotations = ".$stylesset_js.";\n";
	
	// Messages javascript
	// TODO : Voir pour le message_version dans medit_html_panneau_article::Generer_barre_edition
	// TODO mieux encore : Produire une fonction qui parse le même fichier CSV que les scripts PHP
	echo "\n/* Messages javascript dans la langue choisie */\n";
	echo "var Global_label_annuler = \"".$_MEDIT_LANGUE->annuler()."\";\n";
	echo "var Global_label_valider = \"".$_MEDIT_LANGUE->valider()."\";\n";
	echo "var Global_label_distance_nulle = \"".$_MEDIT_LANGUE->alerte_fil_distance_nulle()."\";\n";
	echo "var Global_label_distance_negative = \"".$_MEDIT_LANGUE->alerte_fil_distance_negative()."\";\n";
	echo "var Global_label_distance_positive = \"".$_MEDIT_LANGUE->alerte_fil_distance_positive()."\";\n";
