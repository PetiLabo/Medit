<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_commentaire
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table_commentaire extends medit_db_table {
	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();
	
	public static function Inserer($niveau, $texte, $fk_commentaire, $id_auteur, $id_fil_discussion) {
		$sql = "INSERT INTO ".table("commentaire")."(niveau, horodatage, texte, fk_commentaire, fk_auteur, fk_fil_discussion) ";
		$sql .= "VALUES (:niveau, NOW(), :texte, :fk_commentaire, :id_auteur, :id_fil_discussion)";
		$id_commentaire = medit_db::Inserer_requete($sql, array("niveau" => $niveau, "texte" => $texte, "fk_commentaire" => $fk_commentaire, "id_auteur" => $id_auteur, "id_fil_discussion" => $id_fil_discussion));
		return $id_commentaire;
	}
}