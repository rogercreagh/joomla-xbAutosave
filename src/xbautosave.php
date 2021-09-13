<?php
/**
 * @package xbAutoSave 
 * @version xbautosave.js 2.0.0.0 11th Jan 2019
 * @author Roger C-O
 * @copyright (C) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * Based on plg_content_ctrls by Chupurnov Valeriy (C) 2015 Chupurnov Valeriy 
**/

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgcontentXbautosave extends JPlugin 
{
    protected $autoloadLanguage = true;
    
	function __construct(&$subject, $config = array()) 
	{
	    $app = JFactory::getApplication();
		if ($app->isAdmin()) {
			parent::__construct($subject, $config);
		}
		// if we not in backend then do nothing
	}
	
	public function getParam($name, $defaultvalue = null) {
		return $this->params->get($name, $defaultvalue)!==null ? $this->params->get($name, $defaultvalue) : $defaultvalue;
	}
	
	function onContentPrepareForm() {
        $app = JFactory::getApplication();
        if (!$app->isAdmin()) { // only autosave on admin side
            return;
        }
        if ($app->input->get('option', '') != 'com_content') { //only autosave for articles (com_content)
            return;
        }
        if ($app->input->get('layout') != 'edit') { // if we aren't in edit layout then we can't be autosaving
            return;
        }
        $artid = $app->input->get('id');
        if (!$artid) { // if there is no id then the article hasn't been saved yet - show info and don't enable autosave
            $app->enqueueMessage(JText::_('PLG_CONTENT_ASAVE_DISABLE_MSG'), 'Warning');
            return;
        }
        $doc = JFactory::getDocument();
        $doc->addScript(JURI::root().'/media/plg_content_xbautosave/js/xbautosave.js');
        $editor = JFactory::getEditor();
        $doc->addScriptDeclaration('window.updateEditorAutosave = function() {
                if (document.getElementById("jform_articletext")) {
				    document.getElementById("jform_articletext").value = '.$editor->getContent("jform_articletext").';
                }
			};');
        if (($this->getParam('use_autosave')) || ($this->getParam('use_keysave')) ) {
            $period = intval($this->getParam('autosave_period',30));
            if ($this->getParam('article_id')!=$artid) { //is this a different article to last time we saved?
                //confirm Autosave enabled and generate a warning message about multiple versions building up
                $msg=''; 
                if ($this->getParam('use_autosave')) {
                    $msg = JText::sprintf('PLG_CONTENT_ASAVE_RECOMMEND_MSG1',$period).' ';
                }
                if ($this->getParam('use_keysave')) {
                    $msg .= JText::_('PLG_CONTENT_ASAVE_RECOMMEND_MSG2');
                }
                $msg .= JText::_('PLG_CONTENT_ASAVE_RECOMMEND_MSG3');
                $app->enqueueMessage($msg,'Notice');
                $table = new JTableExtension(JFactory::getDbo());
                $table->load(array('element' => 'xbautosave'));
                $this->params->set('article_id',$artid);
                $table->set('params', $this->params->toString());
                $table->store(); 
                //this will hide the message every time we save this article until we have edited another one
            } //endif article_id
            $period *= 1000;  //convert sec to ms
            $doc->addScriptDeclaration('
                        setInterval(function () { window.doAutosave(2);},'.$period.');
                    ');
        } //endif use_autosave
        $keySaveEnabled = $this->getParam('use_keysave',0);
        $doc->addScriptDeclaration('
                window.chkkey = '.$keySaveEnabled.';
            ');
        $doc->addStyleSheet(JURI::root().'/media/plg_content_xbautosave/css/xbautosave.css');
        return;
	}
}