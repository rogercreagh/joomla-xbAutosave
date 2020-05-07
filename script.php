<?php
/**
 * @package xbAutoSave
 * @version xbautosave.php 2.0.0.0 11th Jan 2019
 * @author Roger C-O
 * @copyright (C) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
// No direct access to this file
defined('_JEXEC') or die;

class plgContentXbautosaveInstallerScript
{
    function preflight($type, $parent)
    {
    }
    
    function install($parent)
    {
        echo '<h3>xbAutosave installed</h3>';
        echo '<p>Version'.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate.'</p>';
        echo '<p>For help and information see <a href="http://crosborne.co.uk/autosavedoc" target="_blank">
            www.crosborne.co.uk/autosavedoc</a></p>';
        echo '<p><i>Don\'t forget to set required options and enable xbAutoSave</i>&nbsp;';
        echo '&nbsp;<a href="index.php?option=com_plugins&filter_folder=content&filter_element=xbautosave" >Goto Options Page</a></p>';
    }
    
    function uninstall($parent)
    {
        echo '<p>The xbAutoSave Button plugin has been uninstalled</p>';
    }
    
    function update($parent)
    {
        echo '<p>The xbAutoSave Button has been updated to version ' . $parent->get('manifest')->version . '</p>';
        echo '<p>For details see <a href="http://crosborne.co.uk/autosave#changelog" target="_blank">
            www.crosborne.co.uk/autosave#changelog</a></p>';
    }
    
    function postflight($type, $parent)
    {
        $message = $parent->get('manifest')->name.' version'.$parent->get('manifest')->version.' has been ';
        switch ($type) {
            case 'install': $message .= 'installed'; break;
            case 'uninstall': $message .= 'uninstalled'; break;
            case 'update': $message .= 'updated'; break;
            case 'discover_install': $message .= 'discovered and installed'; break;
        }
        JFactory::getApplication()->enqueueMessage($message);       
    }
}