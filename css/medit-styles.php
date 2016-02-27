<?php 
	header("Content-type: text/css; charset=utf-8");

	// Initialisations
	define("_MEDIT_RACINE", "..");
	require_once(_MEDIT_RACINE."/classes/init.php");

	// Création du CSS
	$liste_types_annotation = medit_db_table_type_annotation::Liste_types_annotation();
	foreach ($liste_types_annotation as $id_type_annotation) {
		$type_annotation = medit_db_table_type_annotation::Construire($id_type_annotation);
		$code_couleur = $type_annotation->lire_code_couleur();
		echo "span.style_annotation_".$id_type_annotation."{background:".$code_couleur.";}\n";
	}