/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'sortie', groups: ['annuler', 'valider'] },
		{ name: 'format', groups: [ 'styles' ] },
		{ name: 'copier_coller', groups: [ 'clipboard', 'undo' ] },
		{ name: 'attributs', groups: [ 'basicstyles' ] },
		{ name: 'liens', groups: [ 'links' ] },
		{ name: 'source', groups: [ 'mode' ] },
		{ name: 'plein_ecran', groups: [ 'tools' ] }
	];

	config.removeButtons = 'Scayt,About,Image,InlineCancel,SpecialChar,RemoveFormat,Styles,NumberedList,BulletedList,Anchor';

	// Simplification des boîtes de dialogue
	config.removeDialogTabs = 'image:advanced;link:advanced';

	// Liste des blocs
	config.format_tags = 'p;h1;h2;h3;h5';
	
	// AJOUT : CSS dans l'iframe
	config.contentsCss = 'css/medit-styles.css'

	// AJOUT : autorisation des ids
	config.extraAllowedContent = '*[id];p(*)';
	
	// AJOUT : plugin autogrow
	config.extraPlugins = 'autogrow';
	config.autoGrow_onStartup = true;
	config.autoGrow_minHeight = 25;
	
	// AJOUT : plugin fixed toolbar
	config.extraPlugins = 'fixed';

	// AJOUT : désactivation du menu contextuel sur clic droit
	config.removePlugins = 'closebtn,elementspath,liststyle,scayt,menubutton,contextmenu,resize';
	
	config.stylesSet = [];
};
