<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ILIAS\DI\Container;
use srag\CustomInputGUIs\UdfEditor\Loader\CustomInputGUIsLoaderDetector;
use srag\DIC\UdfEditor\DICTrait;
use srag\Notifications4Plugin\UdfEditor\Utils\Notifications4PluginTrait;

class ilUdfEditorPlugin extends ilRepositoryObjectPlugin
{
    use DICTrait;
    use Notifications4PluginTrait;

    public const PLUGIN_ID = 'xudf';
    public const PLUGIN_CLASS_NAME = self::class;

    public function getPluginName(): string
    {
        return 'UdfEditor';
    }

    protected static bool $init_notifications = false;

    public static function initNotifications()/*:void*/
    {
        if (!self::$init_notifications) {
            self::$init_notifications = true;

            self::notifications4plugin()->withTableNamePrefix(self::PLUGIN_ID)->withPlugin(self::plugin())->withPlaceholderTypes([
                "object" => "object " . ilObjUdfEditor::class,
                "user" => "object " . ilObjUser::class,
                "user_defined_data" => "array"
            ]);
        }
    }

    protected static ?ilUdfEditorPlugin $instance = null;

    public function allowCopy(): bool
    {
        return true;
    }

    public static function getInstance(): ?ilUdfEditorPlugin
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function init()/*:void*/
    {
        self::initNotifications();
    }

    public function updateLanguages(/*array*/ $a_lang_keys = null)/*:void*/
    {
        parent::updateLanguages($a_lang_keys);

        self::notifications4plugin()->installLanguages();
    }

    protected function uninstallCustom(): void
    {
        global $DIC;
        $DIC->database()->dropTable(xudfSetting::DB_TABLE_NAME, false);
        $DIC->database()->dropTable(xudfContentElement::DB_TABLE_NAME, false);
        $DIC->database()->manipulateF('DELETE FROM copg_pobj_def WHERE component=%s', ['text'], ['Customizing/global/plugins/Services/Repository/RepositoryObject/UdfEditor']);
        self::notifications4plugin()->dropTables();
    }

    public function exchangeUIRendererAfterInitialization(Container $dic): Closure
    {
        return CustomInputGUIsLoaderDetector::exchangeUIRendererAfterInitialization();
    }
}
