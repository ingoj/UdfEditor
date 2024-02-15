<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ILIAS\DI\Container;
use srag\Plugins\UdfEditor\Libs\CustomInputGUIs\Loader\CustomInputGUIsLoaderDetector;
use srag\Plugins\UdfEditor\Libs\Notifications4Plugin\Utils\Notifications4PluginTrait;

class ilUdfEditorPlugin extends ilRepositoryObjectPlugin
{

    use Notifications4PluginTrait;

    public const PLUGIN_ID = 'xudf';
    public const PLUGIN_CLASS_NAME = self::class;

    protected static bool $init_notifications = false;
    protected static ?ilUdfEditorPlugin $instance = null;

    public function getPluginName(): string
    {
        return 'UdfEditor';
    }

    public static function initNotifications(): void
    {
        if (!self::$init_notifications) {
            self::$init_notifications = true;

            self::notifications4plugin()->withTableNamePrefix(self::PLUGIN_ID)
                ->withPlugin(self::getInstance())
                ->withPlaceholderTypes([
                    "object" => "object " . ilObjUdfEditor::class,
                    "user" => "object " . ilObjUser::class,
                    "user_defined_data" => "array"
                ]);
        }
    }


    public function allowCopy(): bool
    {
        return true;
    }

    public static function getInstance(): ilUdfEditorPlugin
    {
        if (!isset(self::$instance)) {
            global $DIC;

            /** @var $component_factory ilComponentFactory */
            $component_factory = $DIC['component.factory'];
            /** @var $plugin ilUdfEditorPlugin */
            $plugin = $component_factory->getPlugin(self::PLUGIN_ID);

            self::$instance = $plugin;
        }

        return self::$instance;
    }

    protected function init(): void
    {
        self::initNotifications();
    }

    protected function uninstallCustom(): void
    {
        global $DIC;
        $DIC->database()->dropTable(xudfSetting::DB_TABLE_NAME, false);
        $DIC->database()->dropTable(xudfContentElement::DB_TABLE_NAME, false);
        $DIC->database()->manipulateF(
            'DELETE FROM copg_pobj_def WHERE component=%s',
            ['text'],
            ['Customizing/global/plugins/Services/Repository/RepositoryObject/UdfEditor']
        );
        self::notifications4plugin()->dropTables();
    }

    public function exchangeUIRendererAfterInitialization(Container $dic): Closure
    {
        return CustomInputGUIsLoaderDetector::exchangeUIRendererAfterInitialization();
    }
}
