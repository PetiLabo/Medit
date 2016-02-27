/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'sortie', groups: ['annuler', 'valider'] },
		{ name: 'ajout_annotations', groups: [ 'styles' ] },
		{ name: 'suppression_annotations', groups: [ 'cleanup' ] },
		{ name: 'copier_coller', groups: [ 'clipboard', 'undo' ] },
		{ name: 'source', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'plein_ecran', groups: [ 'tools' ] },
	];

	config.removeButtons = 'Underline,Subscript,Superscript,Cut,Copy,Paste,PasteText,PasteFromWord,Scayt,Anchor,Link,Unlink,Image,Table,HorizontalRule,SpecialChar,Bold,Italic,Strike,NumberedList,BulletedList,Indent,Outdent,Format,Blockquote,About';

	// Simplification des boîtes de dialogue
	config.removeDialogTabs = 'image:advanced;link:advanced';

	// Liste des blocs
	config.format_tags = 'p;h1;h2;h3;h5';
	
	// AJOUT : CSS dans l'iframe
	config.contentsCss = ['css/medit-styles.css', 'css/medit-styles.php'];

	// AJOUT : autorisation des ids + des balises car absence du bouton "format"
	config.extraAllowedContent = 'p(*);h1;h2;h3;h5;strong;em;s;u;sup;sub;span;*[id](*)';
	
	// AJOUT : plugin autogrow
	config.extraPlugins = 'autogrow';
	config.autoGrow_onStartup = true;
	config.autoGrow_minHeight = 25;
	
	// AJOUT : plugin fixed toolbar
	config.extraPlugins = 'fixed';

	// AJOUT : désactivation du menu contextuel sur clic droit
	config.removePlugins = 'clipboard,closebtn,elementspath,liststyle,scayt,menubutton,contextmenu,resize';
	
	config.stylesSet = [];
}
