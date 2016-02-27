/*
 * Fonctions utilitaires au service des autres scripts medit
 */
  
/* IMPORTANT : bloque les mises en cache (sinon pas de refresh dans les setinterval sur IE) */
$.ajaxSetup ({cache: false});

/* Petites fonctions utilitaires *************************************************************/
function mettre_a_jour_version(code_version) {
	var ret = false;
	if (code_version != Global_version_article) {
		if (Global_mode_gestion_article == 1) {
			Global_version_article = code_version;
			// TODO : MAJ barre d'outils
			$("span.s_numero_version_edition").text(code_version);
			MEDIT.sommaire.maj_version_panneau_sommaire(code_version);
		}
		ret = true;
	}
	return ret;
}

function demarrer_attente() {
	$("body").css("cursor", "wait");
}

function terminer_attente() {
	$("body").css("cursor", "auto");
}

/* Effet de scroll lors de l'ajout d'événements dans un journal */
function scroller_journal(journal, tab_evt) {
	if (tab_evt.length > 0) {
		var nouvel_evt = tab_evt.shift();
		nouvel_evt.hide();
		journal.append(nouvel_evt);
		journal.find("p:first").slideUp(250, function() {$(this).remove();scroller_journal(journal, tab_evt);});
		nouvel_evt.slideDown(250);
	}
}

/* Fonctions de gestion des sélections de texte ***********************************************/

/* RAZ de la sélection de texte courante */
function effacer_selection() {
    if ( document.selection ) {
        document.selection.empty();
    } else if ( window.getSelection ) {
        window.getSelection().removeAllRanges();
    }
}

/* Rafraichissement d'un paragraphe après validation */
function remplacer_paragraphes(id_verrou, html) {
	/* Récupération de la hauteur de l'ancien élément */
	var verrou = $("#f_"+id_verrou);
	var ancienne_hauteur = verrou.height();

	/* Récupération de la hauteur du nouvel élément */
	var nouveau_paragraphes = $("<div>"+html+"</div>");
	var nouvelle_hauteur = 0;
	nouveau_paragraphes.find("div[id^='p_']").each(function() {
		$(this).attr("id", false).css({position:'absolute', visibility: 'hidden', display: 'block'});
		$("#panneau_article").append($(this));
		nouvelle_hauteur = nouvelle_hauteur + $(this).height();
	});
	/* Remplacement */
	verrou.css("height", ancienne_hauteur+"px").html(html).animate({height: nouvelle_hauteur}, 1500, function() {
		$(this).replaceWith(html);
	});
	nouveau_paragraphes.remove();
}

/* Retourne le texte sélectionné (source : StackOverflow) */
function getSelectedText() {
	if (window.getSelection) {
		selection = window.getSelection();
		texte = selection.toString();
	}
	else if (document.selection) {
		selection = document.selection.createRange();
		texte = selection.text;
	}
	else {
		texte = "";
	}
	texte = $.trim(texte);
	return texte;
}

/* Retourne la liste des noeuds sélectionnés (source : StackOverflow) */
function getSelectedNodes() {
	var selection = window.getSelection();
	if (selection.isCollapsed) {return [];}

	var node1 = selection.anchorNode;
	var node2 = selection.focusNode;
	var selectionAncestor = get_common_ancestor(node1, node2);
	if (selectionAncestor == null) {return [];}

	var nodes = getNodesBetween(selectionAncestor, node1, node2);
	var ids = [], balise, id;
	var len = nodes.length;
	for (var i = 0; i < len; ++i) {
		balise = nodes[i].localName;
		id = nodes[i].id;
		if ((balise == "div") && (id != "undefined")) {
			ids.push(id);
		}
	}

	if (ids.length == 0) {
		var node = getSelectedNode();
		balise = node.localName;
		if (balise != "div") {
			node = node.parentNode;
			balise = node.localName;
		}
		id = node.id;
		if ((balise == "div") && (id != "undefined")) {
			ids.push(id);
		}
	}
	return ids;
}

/* Fonction de service pour la fonction principale getSelectedNodes */
function get_common_ancestor(a, b) {
    $parentsa = $(a).parents();
    $parentsb = $(b).parents();

    var found = null;
    $parentsa.each(function() {
        var thisa = this;
        $parentsb.each(function() {
            if (thisa == this) {
                found = this;
                return false;
            }
        });
        if (found) return false;
    });
    return found;
}

/* Fonction de service pour la fonction principale getSelectedNodes */
function isDescendant(parent, child) {
	var node = child;
	while (node != null) {
		if (node == parent) {
			return true;
		}
		node = node.parentNode;
	}
	return false;
}

/* Fonction de service pour la fonction principale getSelectedNodes */
function getNodesBetween(rootNode, node1, node2) {
	var resultNodes = [];
	var isBetweenNodes = false;
	for (var i = 0; i < rootNode.childNodes.length; i+= 1) {
		if (isDescendant(rootNode.childNodes[i], node1) || isDescendant(rootNode.childNodes[i], node2)) {
			if (resultNodes.length == 0) {
				isBetweenNodes = true;
			}
			else {
				isBetweenNodes = false;
			}
			resultNodes.push(rootNode.childNodes[i]);
		}
		else if (resultNodes.length == 0) {
		} 
		else if (isBetweenNodes) {
			resultNodes.push(rootNode.childNodes[i]);
		}
		else {
			return resultNodes;
		}
	}
	if (resultNodes.length == 0) {
		return [rootNode];
	} 
	else if (isDescendant(resultNodes[resultNodes.length - 1], node1) || isDescendant(resultNodes[resultNodes.length - 1], node2)) {
		return resultNodes;
	}
	else {
		// same child node for both should never happen
		return [resultNodes[0]];
	}
}

/* Retourne le premier ou le dernier noeud sélectionné (source : StackOverflow)  */
/* IsStart : permet de choisir entre startContainer et endContainer (par défaut) */
function getSelectedNode(isStart) {
	var range, sel, container;
	if (document.selection) {
		range = document.selection.createRange();
		range.collapse(isStart);
		return range.parentElement();
	} 
	else {
		sel = window.getSelection();
		if (sel.getRangeAt) {
			if (sel.rangeCount > 0) {
				range = sel.getRangeAt(0);
			}
		}
		else {
			// Old WebKit
			range = document.createRange();
			range.setStart(sel.anchorNode, sel.anchorOffset);
			range.setEnd(sel.focusNode, sel.focusOffset);

			// Handle the case when the selection was selected backwards (from the end to the start in the document)
			if (range.collapsed !== sel.isCollapsed) {
				range.setStart(sel.focusNode, sel.focusOffset);
				range.setEnd(sel.anchorNode, sel.anchorOffset);
			}
		}
		if (range) {
		   container = range[isStart ? "startContainer" : "endContainer"];

		   // Check if the container is a text node and return its parent if so
		   return container.nodeType === 3 ? container.parentNode : container;
		}   
	}
}
 
/* Fonctions JQuery additionnelles ************************************************************/

/* Hashing SHA1 (c) Chris Veness 2002-2010 | www.movable-type.co.uk | MIT Licence */
var Sha1={};Sha1.hash=function(r,t){t="undefined"==typeof t?!0:t,t&&(r=Utf8.encode(r));var e=[1518500249,1859775393,2400959708,3395469782];r+=String.fromCharCode(128);for(var a=r.length/4+2,o=Math.ceil(a/16),n=new Array(o),f=0;o>f;f++){n[f]=new Array(16);for(var h=0;16>h;h++)n[f][h]=r.charCodeAt(64*f+4*h)<<24|r.charCodeAt(64*f+4*h+1)<<16|r.charCodeAt(64*f+4*h+2)<<8|r.charCodeAt(64*f+4*h+3)}n[o-1][14]=8*(r.length-1)/Math.pow(2,32),n[o-1][14]=Math.floor(n[o-1][14]),n[o-1][15]=8*(r.length-1)&4294967295;for(var u,c,S,d,C,i=1732584193,v=4023233417,A=2562383102,g=271733878,l=3285377520,p=new Array(80),f=0;o>f;f++){for(var s=0;16>s;s++)p[s]=n[f][s];for(var s=16;80>s;s++)p[s]=Sha1.ROTL(p[s-3]^p[s-8]^p[s-14]^p[s-16],1);u=i,c=v,S=A,d=g,C=l;for(var s=0;80>s;s++){var x=Math.floor(s/20),H=Sha1.ROTL(u,5)+Sha1.f(x,c,S,d)+C+e[x]+p[s]&4294967295;C=d,d=S,S=Sha1.ROTL(c,30),c=u,u=H}i=i+u&4294967295,v=v+c&4294967295,A=A+S&4294967295,g=g+d&4294967295,l=l+C&4294967295}return Sha1.toHexStr(i)+Sha1.toHexStr(v)+Sha1.toHexStr(A)+Sha1.toHexStr(g)+Sha1.toHexStr(l)},Sha1.f=function(r,t,e,a){switch(r){case 0:return t&e^~t&a;case 1:return t^e^a;case 2:return t&e^t&a^e&a;case 3:return t^e^a}},Sha1.ROTL=function(r,t){return r<<t|r>>>32-t},Sha1.toHexStr=function(r){for(var t,e="",a=7;a>=0;a--)t=r>>>4*a&15,e+=t.toString(16);return e};var Utf8={};Utf8.encode=function(r){var t=r.replace(/[\u0080-\u07ff]/g,function(r){var t=r.charCodeAt(0);return String.fromCharCode(192|t>>6,128|63&t)});return t=t.replace(/[\u0800-\uffff]/g,function(r){var t=r.charCodeAt(0);return String.fromCharCode(224|t>>12,128|t>>6&63,128|63&t)})},Utf8.decode=function(r){var t=r.replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g,function(r){var t=(15&r.charCodeAt(0))<<12|(63&r.charCodeAt(1))<<6|63&r.charCodeAt(2);return String.fromCharCode(t)});return t=t.replace(/[\u00c0-\u00df][\u0080-\u00bf]/g,function(r){var t=(31&r.charCodeAt(0))<<6|63&r.charCodeAt(1);return String.fromCharCode(t)})};

/* Cookie plugin (c) 2006 Klaus Hartl | stilbuero.de | Dual licensed under the MIT and GPL licenses */
jQuery.cookie=function(e,i,o){if("undefined"==typeof i){var n=null;if(document.cookie&&""!=document.cookie)for(var r=document.cookie.split(";"),t=0;t<r.length;t++){var p=jQuery.trim(r[t]);if(p.substring(0,e.length+1)==e+"="){n=decodeURIComponent(p.substring(e.length+1));break}}return n}o=o||{},null===i&&(i="",o.expires=-1);var u="";if(o.expires&&("number"==typeof o.expires||o.expires.toUTCString)){var s;"number"==typeof o.expires?(s=new Date,s.setTime(s.getTime()+24*o.expires*60*60*1e3)):s=o.expires,u="; expires="+s.toUTCString()}var a=o.path?"; path="+o.path:"",c=o.domain?"; domain="+o.domain:"",m=o.secure?"; secure":"";document.cookie=[e,"=",encodeURIComponent(i),u,a,c,m].join("")};
