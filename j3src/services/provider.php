<?php
/**
 * @package xbAutoSave for Joomla! 4.x/5.x
 * @filesource xbautosave.php
 * @version 3.1.0. 18th November 2023
 * @author Roger C-O
 * @copyright (C) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @desc Based on plg_content_ctrls by Chupurnov Valeriy (C) 2015 Chupurnov Valeriy 
**/

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Crosborne\Plugin\Content\Xbautosave\Extension\Xbautosave;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.3.0
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
				$dispatcher = $container->get(DispatcherInterface::class);
				$plugin     = new Xbautosave(
                    $dispatcher,
                    (array) PluginHelper::getPlugin('content', 'xbautosave')
                );
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
