/**
 * @package xbAutoSave 
 * @version xbautosave.js 2.0.0.0 11th Jan 2019
 * @author Roger C-O
 * @copyright (C) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * Based on plg_content_ctrls by Chupurnov Valeriy (C) 2015 Chupurnov Valeriy 
**/
(function($){
	var popupDiv = $('<div class="asave_popup_box"></div>');
	$(function(){
		$('body').append(popupDiv);
		setTimeout(function() {
			window.doAutosave(3);
		}, 3000);
	})
	function autosavePopup(msgs, classname) {
		var popuptpl = '<div class="asave_dark_background"><div class="{classname}">{msg}</div></div>';
		if (!$.isArray(msgs)) {
			msgs = [msgs];
		}
		var div = [], box;
		for(i=0;i<msgs.length;i+=1) {
			box = $(popuptpl
				.replace('{msg}', msgs[i])
				.replace('{classname}', classname)
			).click(function(){
				$(this).stop().remove();
			})
			div.push(box)
		}
		
		setTimeout(function() {
			$(div).each(function(){
				this.fadeOut(500, function() {
					$(this).remove();
				})
			})
		}, 2500);
		
		popupDiv.append(div)
	}
	function autosaveAction(callback) {
		var progress = $('<div style="position:fixed;z-index:10301;top:0px;left:0px;background:#D22;box-shadow: 0 0 10px #D22, 0 0 5px #D22;height:3px;"></div>');				
		$("body").append(progress);
		$("input[name=task]").val("article.apply");
		$.ajax({
			xhr: function(){
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function(evt){
				  if (evt.lengthComputable) {
					var percentComplete = evt.loaded / evt.total;
					progress
						.stop()
						.animate({width:percentComplete*$(window).width()+"px"},300)
				  }
				}, false);
				xhr.addEventListener("progress", function(evt){
				  if (evt.lengthComputable) {
					var percentComplete = evt.loaded / evt.total;
					progress
						.stop()
						.animate({width:percentComplete*$(window).width()+"px"},300)
				  }
				}, false);
				return xhr;
			},
			type: "POST",
			url: $("#item-form").attr("action"),
			data: $("#item-form").serialize(),
			success: function(data, status){
				progress.stop().remove();
				callback.call(this, data, status);
			}
		});
	}
	window.doAutosaveOldData = null;
	window.doAutosave = function (checkupdate) {
		if (!$('#jform_articletext').is(':visible')) {
			if (!window.updateEditorAutosave) {
				if (window['Joomla'] && Joomla["editors"] && Joomla.editors.instances['jform_articletext'] && Joomla.editors.instances['jform_articletext'].getCode) {
					document.getElementById('jform_articletext').value = Joomla.editors.instances['jform_articletext'].getCode();
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

		var data = $('#item-form').serialize();
		if (checkupdate===3) {
			window.doAutosaveOldData = data;
			return false;
		}
		if (checkupdate===2 && window.doAutosaveOldData==data) {
			return false;
		}
		window.doAutosaveOldData = data;
		autosaveAction(function(resp) {
			var newdoc = $(resp);
			autosavePopup(newdoc.find('#system-message-container .alert,#system-message-container .message').html(),newdoc.find('#system-message-container .alert,#system-message-container .message').attr('class'))
			if(newdoc.find('#jform_alias').val()) {
				$('#jform_alias').val(newdoc.find('#jform_alias').val());
			}
			if (newdoc.find('#jform_id').val()) {
				$('#jform_id').val(newdoc.find('#jform_id').val());
				$("#item-form").attr("action", $("#item-form").attr("action").replace(/&id=[0-9]+/,'&id='+$('#jform_id').val()))
			}
			delete newdoc;
		});
	};
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
			$((function(){
				var windows = [window];
				$('body iframe').each(function(){
					windows.push(this.contentWindow || this)
				})
				return windows;
			}())).each(function(){
				if (window.chkkey) {
					if(!this.hasKeydownHandler) {
						this.hasKeydownHandler = true;
						$(this).on('keydown', testKeydown);
					}
				}
			});
			setTimeout(addWindowHandler,1000);			
	}
	addWindowHandler();
}(jQuery))