/**
 * @package xbAutoSave for Joomla! 4.x/5.x
 * @filesource media/js/xbautosave.js
 * @version 3.1.1 18th November 2023
 * @author Roger C-O
 * @copyright (C) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * Based on plg_content_ctrls by Chupurnov Valeriy (C) 2015 Chupurnov Valeriy 
**/
import { JoomlaEditor } from 'editor-api';
document.addEventListener('DOMContentLoaded', function() {
	let xbautosaveoptions = Joomla.getOptions('plg_content_xbautosave');
	if (xbautosaveoptions.period > 0) {
		setInterval(function () { window.doAutosave(2);},xbautosaveoptions.period);
	}
	window.chkkey = xbautosaveoptions.chkkey;
	var popupDiv = document.createElement('div');
	popupDiv.classList.add('asave_popup_box');
	popupDiv.classList.add('fade');
	document.body.appendChild(popupDiv);
	popupDiv = document.querySelector('.asave_popup_box');
	setTimeout(function() {
		window.doAutosave(3);
	}, 3000);
		
	function autosavePopup(msgs) {
		var popuptpl = '<div>{msg}</div>';
		if (!Array.isArray(msgs)) {
			msgs = [msgs];
		}
		var div = [], box;
		for(let i=0;i<msgs.length;i+=1) {
			let amsg = msgs[i].replace('<noscript>','').replace('</noscript>','');  
			box = document.createElement('div');
			box.innerHTML =amsg ;
			document.body.appendChild(box);
			box.addEventListener('click',function(e){
				e.parentNode.removeChild(e);
				popupDiv.classList.remove('show');
			})
			div.push(box)
		}
		
		setTimeout(function() {
			div.forEach(function(e){
				e.parentNode.removeChild(e);
				popupDiv.classList.remove('show');
			})
		}, 2500);
		div.forEach(function(e) {
			popupDiv.append(e);
		})
		popupDiv.classList.add('show');
	}
	function autosaveAction(callback) {
		let progress = document.createElement('div');
		progress.id = "xbautosaveprogress";
		progress.style="position:fixed;z-index:10301;top:0px;left:0px;background:#D22;box-shadow: 0 0 10px #D22, 0 0 5px #D22;height:3px;";		
		document.body.appendChild(progress);
		progress = document.querySelector('#xbautosaveprogress');

		let inputtasks = document.querySelectorAll("input[name=task]");
		inputtasks.forEach(function (inputtask) {
			inputtask.value = "article.apply";
		})
		let itemform = document.querySelector("#item-form");
		Joomla.request({
			method: "POST",
			url: itemform.getAttribute("action"),
			data: serialize(itemform),
			onBefore: (xhr) => {
				xhr.upload.addEventListener("progress", function(evt){
				  if (evt.lengthComputable) {
					var percentComplete = evt.loaded / evt.total;
					let winwidth = window.innerWidth;
					progress.style.width = percentComplete*winwidth+"px";
				  }
				}, false);
				xhr.addEventListener("progress", function(evt){
				  if (evt.lengthComputable) {
					var percentComplete = evt.loaded / evt.total;
					let winwidth = window.innerWidth;
					progress.style.width = percentComplete*winwidth+"px";
				  }
				}, false);
				return xhr;
			},
			onComplete: function(data, xhr){
				if (progress.parentNode) progress.parentNode.removeChild(progress);
				callback.call(this,data.response);
			}
		}) 
		
	}
	window.doAutosaveOldData = null;
	window.doAutosave = function (checkupdate) {
		let articletext = document.querySelector('#jform_articletext');
		if (!isVisible(articletext)) {
			if (!window.updateEditorAutosave) {
				if (window['Joomla'] && JoomlaEditor && JoomlaEditor.get('jform_articletext')) {
					if (JoomlaEditor.get('jform_articletext').getValue) {
						document.getElementById('jform_articletext').value = JoomlaEditor.get('jform_articletext').getValue();
					}
				}
				if (window['WFEditor'] && WFEditor.getContent) {
					WFEditor.getContent('jform_articletext');
				}
				if (window['tinyMCE'] && tinyMCE.get("jform_articletext") && tinyMCE.get("jform_articletext").save) {
					tinyMCE.get("jform_articletext").save()
				}
				if(window['CKEDITOR']){
					for(var inst in CKEDITOR.instances)
						 CKEDITOR.instances[inst].updateElement();
				}
			} else {
				window.updateEditorAutosave();
			}
		}
		let itemform = document.querySelector('#item-form');
		var data = serialize(itemform);
		if (checkupdate===3) {
			window.doAutosaveOldData = data;
			return false;
		}
		if (checkupdate===2 && window.doAutosaveOldData==data) {
			return false;
		}
		window.doAutosaveOldData = data;
		autosaveAction(function(resp) {
			var parser = new DOMParser();
			var newdoc = parser.parseFromString(resp, "text/html");			
			let systemmsg = newdoc.querySelector('#system-message-container');
			autosavePopup(systemmsg.innerHTML) ;
			let alias = newdoc.querySelector('#jform_alias');
			if (alias && alias.value) {
				document.querySelector('#jform_alias').value = alias.value;
			}
			let jformid = newdoc.querySelector('#jform_id');
			if (jformid && jformid.value) {
				document.querySelector('#jform_id').value = jformid.value;
				let itemform = document.querySelector("#item-form");
				itemform.setAttribute("action",itemform.getAttribute("action").replace(/&id=[0-9]+/,'&id='+jform_id.value));
			} 
			newdoc = null;
		});
	};
	// function serialize from https://barker.codes/blog/serialize-form-data-into-a-query-string-in-vanilla-j
	function serialize (form) {
	// Create a new FormData object
		const formData = new FormData(form);
	// Create a new URLSearchParams object
		const params = new URLSearchParams(formData);
	// Return the query string
		return params.toString();	
	}
	// function isvisible from https://stackoverflow.com/questions/44612141/get-only-visible-element-using-pure-javascript
	function isVisible(el) {
        while (el) {
            if (el === document) {
                return true;
            }
            var $style = window.getComputedStyle(el, null);
            if (!el) {
                return false;
            } else if (!$style) {
                return false;
            } else if ($style.display === 'none') {
                return false;
            } else if ($style.visibility === 'hidden') {
                return false;
            } else if (+$style.opacity === 0) {
                return false;
            } else if (($style.display === 'block' || $style.display === 'inline-block') &&
                $style.height === '0px' && $style.overflow === 'hidden') {
                return false;
            } else {
                return $style.position === 'fixed' || isVisible(el.parentNode);
            }
        }
    }
	
	function testKeydown(event) {
		if (!((event.which == 83)  && (event.ctrlKey || event.metaKey))) {
// testing for Mac cmd+S is usually unreliable as Joomla tinyMCE intercepts event.metaKey (ie cmd) before we can get it :-(
			return true;
		}
		event.preventDefault();
		window.doAutosave(0);
		return false;
	}
	function addWindowHandler() {
		var windows = [window];
		let iframe = document.querySelectorAll('body iframe');
		
		iframe.forEach(function(e){
			windows.push(e.contentWindow || e)
		})
		windows.forEach(function(e){
			if (window.chkkey) {
				if (!e.hasHandler) {
					e.addEventListener('keydown', testKeydown);
					e.hasHandler = true;
				}
			}
		});
		setTimeout(addWindowHandler,1000);			
	}
	addWindowHandler();
})