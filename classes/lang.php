<?php

if (!defined("_MEDIT_VERSION")) {
	die("Accès direct au script impossible.");
}

/**
 * Classe de gestion des langues
 *
 * @auteurs		Philippe Gilles
 */
class medit_lang {

	private $table_traduction = array();

	/**
	 * Constructeur pour classe de la classe de gestion des langues
	 *
	 * @portée	privée
	 * @retour	vide
	 */
    public function __construct($code_langue) {
		$chemin = _MEDIT_RACINE."/classes/lang/{$code_langue}.csv";
		$fichier_langue = @fopen($chemin, "r");
		if ($fichier_langue == false) {die("Impossible de trouver le fichier ".$chemin);}
		while (($ligne = @fgetcsv($fichier_langue, 1024, ";")) != false) {
			$nb_champs = count($ligne);
			if ($nb_champs >= 2) {
				$item = strtolower(trim($ligne[0]));
				$traduction = htmlentities(trim($ligne[1]), ENT_QUOTES, "UTF-8");
				// On exclue les infos vides et l'item // qui correspond à un commentaire
				if ((strlen($item) > 0) && (strlen($traduction) > 0) && (strncmp($item, "//", 2))) {
					$this->table_traduction[$item] = $traduction;
				}
			}
		}
		fclose($fichier_langue);
	}
	
	public function __call($methode, $arguments) {
		return $this->texte($methode, $arguments);
	}
	
	private function texte() {
		$tab_args = func_get_args();
		$item = $tab_args[0];
		$args = $tab_args[1];
		$nb_args = count($args);
		$ret = (isset($this->table_traduction[$item]))?($this->table_traduction[$item]):null;
		for ($cpt = 1;$cpt <= $nb_args;$cpt++) {
			$ret = str_replace("%{$cpt}", $args[($cpt - 1)], $ret);
		}
		return $ret;
	}
}
