<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe d'utilitaires PDO
 *
 * @auteurs		Philippe Gilles
 */
class medit_db {
	
	public static $Connexion = null;

	/**
	 * RAZ constructeur pour classe statique
	 *
	 * @portée	privée
	 * @retour	vide
	 */
    private function __construct() {}

	// --------------------------------------------------------------------

	/**
	 * Connexion à la base de données
	 *
	 * @portée	publique
	 * @retour	vide
	 */
	public static function Init() {
		global $_MEDIT_LANGUE;
		
		if (self::$Connexion == null) {
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			try {
				$db = new PDO("mysql:host=".medit_config::DB_SERVEUR.";dbname=".medit_config::DB_NOM_BASE.";charset=utf8", medit_config::DB_UTILISATEUR, medit_config::DB_MOT_DE_PASSE, $pdo_options);
				self::$Connexion = $db;
			} catch (Exception $e) {
				die($_MEDIT_LANGUE->erreur_connexion_bdd($e->getMessage()));
			}
		}
	}
	
	/**
	 * Exécution d'une requête
	 *
	 * @portée	publique
	 * @param	requête SQL dans la syntaxe PDO
	 * @param	tableau des paramètres
	 * @retour	vide
	 */
	public static function Executer_requete($sql, $parametres) {
		global $_MEDIT_LANGUE;
		
		$requete = null;
		if (self::$Connexion == null) {
			die($_MEDIT_LANGUE->erreur_requete_sans_bdd($sql));
		}
		try {
			$requete = self::$Connexion->prepare($sql);
			$requete->execute($parametres);
		}
		catch (Exception $e) {
			die($_MEDIT_LANGUE->erreur_execution_requete($sql, $e->getMessage()));
		}
		return $requete;
	}
	
	/**
	 * Exécution d'une requête d'insertion
	 *
	 * @portée	publique
	 * @param	requête SQL dans la syntaxe PDO
	 * @param	tableau des paramètres
	 * @retour	valeur de la clé primaire du nouvel enregistrement
	 */
	public static function Inserer_requete($sql, $parametres) {
		global $_MEDIT_LANGUE;
		
		$id = null;
		if (self::$Connexion == null) {
			die($_MEDIT_LANGUE->erreur_requete_sans_bdd($sql));
		}
		try {
			$requete = self::$Connexion->prepare($sql);
			$requete->execute($parametres);
			$id = self::$Connexion->lastInsertId();
		}
		catch (Exception $e) {
			die($_MEDIT_LANGUE->erreur_execution_requete($sql, $e->getMessage()));
		}
		return $id;
	}
	
	/**
	 * Test de la présence d'un tuple dans une table en base de données
	 *
	 * @portée	publique
	 * @param	valeur de l'identifiant recherché
	 * @param	nom de la table
	 * @retour	vide
	 */
	public static function Chercher_tuple($label, $valeur, $table) {
		global $_MEDIT_LANGUE;
		
		$trouve = false;
		if (self::$Connexion == null) {
			die($_MEDIT_LANGUE->erreur_requete_sans_bdd($sql));
		}
		try {
			$sql = "SELECT {$label} FROM {$table} WHERE {$label} = :valeur";
			$requete = self::$Connexion->prepare($sql);
			$requete->execute(array("valeur" => $valeur));
		}
		catch (Exception $e) {
			die($_MEDIT_LANGUE->erreur_execution_requete($sql, $e->getMessage()));
		}
		$trouve = ($requete->fetch())?true:false;
		return $trouve;
	}
	
	/**
	 * Supprime un tuple dans une table en base de données
	 *
	 * @portée	publique
	 * @param	valeur de l'identifiant recherché
	 * @param	nom de la table
	 * @retour	vide
	 */
	public static function Supprimer_tuple($label, $valeur, $table) {
		global $_MEDIT_LANGUE;

		if (self::$Connexion == null) {
			die($_MEDIT_LANGUE->erreur_requete_sans_bdd($sql));
		}
		try {
			$sql = "DELETE FROM {$table} WHERE {$label} = :valeur";
			$requete = self::$Connexion->prepare($sql);
			$requete->execute(array("valeur" => $valeur));
		}
		catch (Exception $e) {
			die($_MEDIT_LANGUE->erreur_execution_requete($sql, $e->getMessage()));
		}
	}
}