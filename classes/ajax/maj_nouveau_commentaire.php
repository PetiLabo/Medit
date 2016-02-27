<?php
	// Initialisations
	define("_MEDIT_RACINE", "../..");
	require_once(_MEDIT_RACINE."/classes/init.php");

	// Récupération du fil de discussion concerné
	$id_fil_discussion = (int) post("id_fil_discussion");
	if ($id_fil_discussion < 1) {die($_MEDIT_LANGUE->erreur_id_fil_discussion_incorrect());}

	// Récupération du commentaire qui fait la réponse
	$id_commentaire = (int) post("id_commentaire");
	if ($id_commentaire < 1) {die($_MEDIT_LANGUE->erreur_id_commentaire_incorrect());}
	
	// Récupération du commentaire qui est répondu
	$fk_commentaire = (int) post("fk_commentaire");
	if ($fk_commentaire < 1) {die($_MEDIT_LANGUE->erreur_id_commentaire_incorrect());}
	
	// Récupération de l'indentation
	$indentation = (int) post("indentation_fil");
	
	// Génération du code HTML
	$html = medit_html_widget_commentaires::Generer_commentaire($indentation, $id_fil_discussion, false, $id_commentaire);
	echo json_encode(array("html" => $html, "id_commentaire" => $id_commentaire, "fk_commentaire" => $fk_commentaire));