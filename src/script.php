<?php
/**
 * @package xbAutoSave
 * @filesource script.php
 * @version 3.0.0.a 14th September 2021
 * @author Roger C-O
 * @copyright (C) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;

class plgContentXbautosaveInstallerScript
{
    function preflight($type, $parent)
    {
    }
    
    function install($parent)
    {
        echo '<h3>xbAutosave installed</h3>';
        echo '<p>Version'.$parent->getManifest()->version.' '.$parent->getManifest()->creationDate.'</p>';
        echo '<p>For help and information see <a href="http://crosborne.co.uk/xbautosave/doc" target="_blank">
            www.crosborne.co.uk/xbautosave/doc</a></p>';
        echo '<p><i>Don\'t forget to set required options and enable xbAutoSave</i>&nbsp;';
        echo '&nbsp;<a href="index.php?option=com_plugins&filter_folder=content&filter_element=xbautosave" >Goto Options Page</a></p>';
    }
    
    function uninstall($parent)
    {
        echo '<p>The xbAutoSave Button plugin has been uninstalled</p>';
    }
    
    function update($parent)
    {
        echo '<p>The xbAutoSave Plugin has been updated to version ' . $parent->getManifest()->version . '</p>';
        echo '<p>For details see <a href="http://crosborne.co.uk/xbAutosave/changelog" target="_blank">
            www.crosborne.co.uk/xbautosave/changelog</a></p>';
    }
    
    function postflight($type, $parent)
    {
        $message = $parent->getManifest()->name.' version'.$parent->getManifest()->version.' has been ';
        switch ($type) {
            case 'install': $message .= 'installed'; break;
            case 'uninstall': $message .= 'uninstalled'; break;
            case 'update': $message .= 'updated'; break;
            case 'discover_install': $message .= 'discovered and installed'; break;
        }
        Factory::getApplication()->enqueueMessage($message);       
    }
}