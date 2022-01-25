<?php
/**
 * @package xbAutoSave
 * @filesource xbautosave.php
 * @version 3.0.0.a 14th September 2021
 * @author Roger C-O
 * @copyright (C) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @desc Based on plg_content_ctrls by Chupurnov Valeriy (C) 2015 Chupurnov Valeriy 
**/

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class plgcontentXbautosave extends CMSPlugin 
{
    protected $autoloadLanguage = true;
    protected $app;
    	
	public function getParam($name, $defaultvalue = null) {
		return $this->params->get($name, $defaultvalue)!==null ? $this->params->get($name, $defaultvalue) : $defaultvalue;
	}
	
	function onContentPrepareForm() {
	    if (!$this->app->isClient('administrator')) { // only autosave on admin side
	        return;
	    }	    
        if ($this->app->input->get('option', '') != 'com_content') { //only autosave for articles (com_content)
            return;
        }
        if ($this->app->input->get('layout') != 'edit') { // if we aren't in edit layout then we can't be autosaving
            return;
        }
        $artid = $this->app->input->get('id');
        if (!$artid) { // if there is no id then the article hasn't been saved yet - show info and don't enable autosave
            $this->app->enqueueMessage(JText::_('PLG_CONTENT_ASAVE_DISABLE_MSG'), 'Warning');
            return;
        }
        JHtml::_('jquery.framework');
        $doc = Factory::getDocument();
        $doc->addScript(Uri::root().'/media/plg_content_xbautosave/js/xbautosave.js');
//        $editor = JFactory::getEditor();
//        $doc->addScriptDeclaration('window.updateEditorAutosave = function() {
//                if (document.getElementById("jform_articletext")) {
//				    document.getElementById("jform_articletext").value = '.$editor->getContent("jform_articletext").';
//                }
//			};');
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
                $this->app->enqueueMessage($msg,'Notice');
                $table = new JTableExtension(Factory::getDbo());
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
        $doc->addStyleSheet(Uri::root().'/media/plg_content_xbautosave/css/xbautosave.css');
        return;
	}
}
