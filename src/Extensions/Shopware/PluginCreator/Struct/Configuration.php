<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Struct;

use ShopwareCli\Struct;

/**
 * The config struct stores all info needed to create the plugin
 */
class Configuration extends Struct
{
    public $phpFileHeader = "<?php\n";

    // the PluginConfig part from the config.yaml file
    public $pluginConfig;

    // Name of the plugin: SwagMyPlugin
    public $name;

    // Namespace of the plugin: frontend / core / backend
    public $namespace;

    // frontend controller needed?
    public $hasFrontend;

    // backend application needed?
    public $hasBackend;

    // widgets needed?
    public $hasWidget;

    // api needed?
    public $hasApi;

    // models needed?
    public $hasModels;

    // commands needed ?
    public $hasCommands;

    // dbal facet / condition needed?
    public $hasFilter;

    // model for the backend ($hasBackend)
    public $backendModel;

    // license header
    public $licenseHeader;

    public $licenseHeaderPlain;

    // is legacy-plugin ?
    public $isLegacyPlugin = false;

    // Has an elastic search integration?
    public $hasElasticSearch = false;
}
