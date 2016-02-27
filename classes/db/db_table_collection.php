<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_collection
 *
 * @collections		Philippe Gilles
 */
class medit_db_table_collection extends medit_db_table {
	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();
}