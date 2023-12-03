<?php
/**
 * @package xbAutoSave for Joomla! 4.x/5.x
 * @filesource script.php
 * @version 4.0.0 18th November 2023
 * @author Roger C-O, Pascal Leconte
 * @copyright (C) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\Filesystem\File;

class plgContentXbautosaveInstallerScript
{
	private $min_joomla_version      = '4.0.0';
	private $min_php_version         = '8.0';
	private $extname                 = 'xbautosave';
    
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
    function preflight($type, $parent)
    {
		if ( ! $this->passMinimumJoomlaVersion())
		{
			return false;
		}

		if ( ! $this->passMinimumPHPVersion())
		{
			return false;
		}
    }
    
    function postflight($type, $parent)
    {
		$langFiles = [
			sprintf("%s/language/en-GB/en-GB.plg_content_%s.ini", JPATH_ADMINISTRATOR, $this->extname),
			sprintf("%s/language/en-GB/en-GB.plg_content_%s.sys.ini", JPATH_ADMINISTRATOR, $this->extname),
			sprintf("%s/language/fr-FR/fr-FR.plg_content_%s.ini", JPATH_ADMINISTRATOR, $this->extname),
			sprintf("%s/language/fr-FR/fr-FR.plg_content_%s.sys.ini", JPATH_ADMINISTRATOR, $this->extname),
		];
		foreach ($langFiles as $file) {
			if (@is_file($file)) {
				File::delete($file);
			}
		}
		
        $message = $parent->getManifest()->name.' version'.$parent->getManifest()->version.' has been ';
        switch ($type) {
            case 'install': $message .= 'installed'; break;
            case 'uninstall': $message .= 'uninstalled'; break;
            case 'update': $message .= 'updated'; break;
            case 'discover_install': $message .= 'discovered and installed'; break;
        }
        Factory::getApplication()->enqueueMessage($message);       
    }
	// Check if Joomla version passes minimum requirement
	private function passMinimumJoomlaVersion()
	{
		$j = new Version();
		$version=$j->getShortVersion(); 
		if (version_compare($version, $this->min_joomla_version, '<'))
		{
			Factory::getApplication()->enqueueMessage(
				'Incompatible Joomla version : found <strong>' . $version . '</strong>, Minimum : <strong>' . $this->min_joomla_version . '</strong>',
				'error'
			);

			return false;
		}

		return true;
	}

	// Check if PHP version passes minimum requirement
	private function passMinimumPHPVersion()
	{

		if (version_compare(PHP_VERSION, $this->min_php_version, '<'))
		{
			Factory::getApplication()->enqueueMessage(
					'Incompatible PHP version : found  <strong>' . PHP_VERSION . '</strong>, Minimum <strong>' . $this->min_php_version . '</strong>',
				'error'
			);
			return false;
		}

		return true;
	}
	
}