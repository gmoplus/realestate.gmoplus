<?php

/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: General Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: REQUESTS.PHP
 *  
 *  The software is a commercial product delivered under single, non-exclusive,
 *  non-transferable license for one domain or IP address. Therefore distribution,
 *  sale or transfer of the file in whole or in part without permission of Flynax
 *  respective owners is considered to be illegal and breach of Flynax License End
 *  User Agreement.
 *  
 *  You are not allowed to remove this information from the file without permission
 *  of Flynax respective owners.
 *  
 *  Flynax Classifieds Software 2024 | All copyrights reserved.
 *  
 *  https://www.flynax.com
 ******************************************************************************/

namespace Flynax\Plugin\WordPressBridge;

use Flynax\Plugin\WordPressBridge\Traits\SingletonTrait;

/**
 * Class PluginPathBuilder
 *
 * @since 2.0.0
 *
 * @package Flynax\Plugin\WordPressBridge
 */
class PluginPathBuilder
{
    use SingletonTrait;

    /**
     * @var string - Plugin key
     */
    private $pluginName;

    /**
     * @var array - Plugin folder structure
     */
    private $folderStructure;

    /**
     * Setter of the pluginName property
     *
     * @param string $pluginName
     */
    public function setPluginName($pluginName)
    {
        $pluginName = (string) $pluginName;
        $this->pluginName = $pluginName;
        $this->rebuildPaths();
    }

    /**
     * Rebuild all plugin paths
     */
    private function rebuildPaths()
    {
        $basicPluginStructure = array();

        $pluginName = $this->getPluginName();
        $rootPath = RL_PLUGINS . $pluginName;
        $rootUrl = RL_PLUGINS_URL . $pluginName;
        $mainPluginsFolders = ['view', 'admin', 'static'];

        foreach ($mainPluginsFolders as $folder) {
            $basicPluginStructure['path'][$folder] = "{$rootPath}/$folder/";
            $basicPluginStructure['url'][$folder] = "{$rootUrl}/$folder/";
        }

        $this->folderStructure = $basicPluginStructure;
    }

    /**
     * Get plugin key
     *
     * @return string
     */
    public function getPluginName()
    {
        return $this->pluginName;
    }

    /**
     * Get url to the specified static (JS/CSS) file in the plugin directory
     *
     * @param string $jsFileName
     *
     * @return string
     */
    public function getStaticFileUrl($jsFileName)
    {
        $jsPath = $this->folderStructure['path']['static'] . $jsFileName;
        if (!file_exists($jsPath)) {
            return '';
        }

        return $this->folderStructure['url']['static'] . $jsFileName;
    }

    /**
     * Include provided JS file on the page
     *
     * @param string $fileName - JS file name from the plugin static folder
     */
    public function addJsToPage($fileName)
    {
        if ($jsUrl = $this->getStaticFileUrl($fileName)) {
            echo sprintf("<script src='%s' type='text/javascript'></script>", $jsUrl);
        }
    }

    /**
     * Get provided view path in the plugin directory
     *
     * @param string $viewName - View name (without .tpl extension)
     * @return string
     */
    public function getViewPath($viewName)
    {
        return $this->folderStructure['path']['view'] . $viewName . '.tpl';
    }
}
