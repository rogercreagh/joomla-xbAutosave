<?php
/**
 * @package xbAutoSave for Joomla! 4.x/5.x
 * @filesource Extension/xbautosave.php
 * @version 4.0.0. 18th November 2023
 * @author Roger C-O, Pascal Leconte
 * @copyright (C) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @desc Based on plg_content_ctrls by Chupurnov Valeriy (C) 2015 Chupurnov Valeriy 
**/
namespace Crosborne\Plugin\Content\Xbautosave\Extension;
defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Table\Extension AS TableExtension;

class Xbautosave extends CMSPlugin 
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
            $this->app->enqueueMessage(Text::_('PLG_CONTENT_ASAVE_DISABLE_MSG'), 'Warning');
            return;
        }
        $doc = Factory::getDocument();
        Factory::getApplication()->getDocument()->getWebAssetManager()
			->registerAndUseStyle('xbautosave','plg_content_xbautosave/xbautosave.css')
            ->registerAndUseScript('xbautosave','plg_content_xbautosave/xbautosave.js',[],['type' => 'module'],[]);
		
		$period = '0'; // initialize $period
		if ($this->getParam('use_autosave')) {
            $period = intval($this->getParam('autosave_period',30));
            if ($this->getParam('article_id')!=$artid) { //is this a different article to last time we saved?
                //confirm Autosave enabled and generate a warning message about multiple versions building up
                $msg=''; 
                if ($this->getParam('use_autosave')) {
                    $msg = Text::sprintf('PLG_CONTENT_ASAVE_RECOMMEND_MSG1',$period).' ';
                }
                if ($this->getParam('use_keysave')) {
                    $msg .= Text::_('PLG_CONTENT_ASAVE_RECOMMEND_MSG2');
                }
                $msg .= Text::_('PLG_CONTENT_ASAVE_RECOMMEND_MSG3');
                $this->app->enqueueMessage($msg,'Notice');
                $table = new TableExtension(Factory::getDbo());
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
		$doc->addScriptOptions('plg_content_xbautosave', 
			array('chkkey' => $keySaveEnabled,'period' => $period));        
        return;
	}
}
