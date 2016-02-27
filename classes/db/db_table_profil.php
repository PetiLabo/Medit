<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion de la table medit_profil
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table_profil extends medit_db_table {
	public static $Liste_tuples = array();
	public static $Liste_id_tuples = array();
}