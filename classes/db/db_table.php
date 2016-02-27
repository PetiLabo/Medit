<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion générique des tables
 *
 * @auteurs		Philippe Gilles
 */
class medit_db_table {

	/**
	 * Construction d'une instance de tuple
	 * L'instanciation n'a lieu que si le tuple n'existe pas
	 * Règles de nommage des classes filles :
	 * medit_db_table_<nom de la table>
	 *
	 * @portée	publique
	 * @param	identifiant du tuple
	 * @param	autochargement activé ou non
	 * @retour	tuple
	 */
	public static function Construire($id_tuple = 0, $auto_chargement = true) {
		$classe_table = get_called_class();
		$classe_tuple = str_replace("db_table_", "db_", $classe_table);
		if (!(self::Id_tuple_existe($id_tuple))) {
			$reflection = new ReflectionClass($classe_tuple);
			$instance = $reflection->newInstanceArgs(array($id_tuple));
			$instance->ecrire_auto_chargement($auto_chargement);
			static::$Liste_tuples[$id_tuple] = $instance;
			static::$Liste_id_tuples[] = $id_tuple;
		}
		return static::$Liste_tuples[$id_tuple];
	}

	/**
	 * Destruction d'une instance de tuple (en BDD et en mémoire)
	 *
	 * @portée	publique
	 * @param	identifiant
	 * @retour	vrai ou faux
	 */
	public static function Detruire($id_tuple) {
		$classe_table = get_called_class();
		$classe_tuple = str_replace("db_table_", "db_", $classe_table);
		if (!(self::Id_tuple_existe($id_tuple))) {
			
			$reflection = new ReflectionClass($classe_tuple);
			$instance = $reflection->newInstanceArgs(array($id_tuple));
		}
		else {
			$instance = static::$Liste_tuples[$id_tuple];
		}
		$instance->supprimer();
		unset(static::$Liste_tuples[$id_tuple]);
	}
	
	/**
	 * Test de présence d'un tuple par son identifiant
	 *
	 * @portée	publique
	 * @param	identifiant
	 * @retour	vrai ou faux
	 */
	public static function Id_tuple_existe($id_tuple) {
		return isset(static::$Liste_tuples[$id_tuple]);
	}

	/**
	 * Recherche de tuple par son identifiant
	 *
	 * @portée	publique
	 * @param	identifiant
	 * @retour	tuple
	 */
	public static function Chercher_tuple_par_id($id_tuple) {
		if (self::Id_tuple_existe($id_tuple)) {
			return static::$Liste_tuples[$id_tuple];
		}
		else {return null;}
	}

	/**
	 * Recherche de tuple par son index
	 *
	 * @portée	publique
	 * @param	index
	 * @retour	tuple
	 */
	public static function Chercher_tuple_par_index($nth_tuple) {
		if (isset(static::$Liste_id_tuples[$nth_tuple])) {
			$id_tuple = static::$Liste_id_tuples[$nth_tuple];
			return self::lire_tuple_par_id($id_tuple);
		}
		else {return null;}
	}

	/**
	 * Comptage du nombre de tuples en mémoire
	 *
	 * @portée	publique
	 * @retour	nombre de tuples
	 */
	public static function Nombre_de_tuples() {
		return count(static::$Liste_id_tuples);
	}
}