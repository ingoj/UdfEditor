<?php

use ILIAS\DI\Container;
use srag\Plugins\UdfEditor\Libs\CustomInputGUIs\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\Plugins\UdfEditor\Libs\CustomInputGUIs\PropertyFormGUI\Items\Items;
use srag\Plugins\UdfEditor\Libs\CustomInputGUIs\PropertyFormGUI\PropertyFormGUI;

class xudfLogTableGUI extends ilTable2GUI
{
    public const ID_PREFIX = 'xudf_log_table_';
    public const PLUGIN_CLASS_NAME = ilUdfEditorPlugin::class;
    public const ROW_TEMPLATE = 'tpl.log_table_row.html';
    /**
     * @var xudfLogGUI|xudfLogGUI
     */
    protected ?object $parent_obj;
    /**
     * @var ilFormPropertyGUI[]
     *
     */
    private array $filter_cache = [];

    private Container $dic;
    private ilUdfEditorPlugin $plugin;

    public function __construct($parent, $parent_cmd)
    {
        $this->parent_obj = $parent;
        parent::__construct($parent, $parent_cmd);
        global $DIC;
        $this->dic = $DIC;
        $this->plugin = ilUdfEditorPlugin::getInstance();
        $this->dic->ui()->mainTemplate()->addCss($this->plugin->getDirectory() . '/templates/default/log_table.css');
    }

    protected function initColumns(): void
    {
        $this->addColumn($this->plugin->txt('values'));
        $this->addColumn($this->dic->language()->txt('user'), 'user');
        $this->addColumn($this->dic->language()->txt('date'), 'timestamp');
    }


    /**
     * @throws Exception
     */
    protected function initData(): void
    {
        $filter_values = array_map(static function ($item) {
            return Items::getValueFromItem($item);
        }, $this->filter_cache);
        $filter_user = $filter_values['user'];

        $where = xudfLogEntry::where(['obj_id' => $this->parent_obj->getObjId()]);
        if ($filter_user != '') {
            $where = $where->where(['usr_id' => $filter_user]);
        }
        $this->setData($where->getArray());
    }

    public function initFilter(): void
    {
        $this->setDisableFilterHiding(true);

        $this->initFilterFields();

        if (!is_array($this->filter_fields)) {
            throw new Exception("\$filters needs to be an array!");
        }

        foreach ($this->filter_fields as $key => $field) {
            if (!is_array($field)) {
                throw new Exception("\$field needs to be an array!");
            }

            if ($field[PropertyFormGUI::PROPERTY_NOT_ADD]) {
                continue;
            }

            $item = Items::getItem($key, $field, $this, $this);

            /*if (!($item instanceof ilTableFilterItem)) {
                throw new TableGUIException("\$item must be an instance of ilTableFilterItem!", TableGUIException::CODE_INVALID_FIELD);
            }*/

            if ($item instanceof MultiLineNewInputGUI) {
                if (is_array($field[PropertyFormGUI::PROPERTY_SUBITEMS])) {
                    foreach ($field[PropertyFormGUI::PROPERTY_SUBITEMS] as $child_key => $child_field) {
                        if (!is_array($child_field)) {
                            throw new Exception("\$fields needs to be an array!");
                        }

                        if ($child_field[PropertyFormGUI::PROPERTY_NOT_ADD]) {
                            continue;
                        }

                        $child_item = Items::getItem($child_key, $child_field, $item, $this);

                        $item->addInput($child_item);
                    }
                }
            }

            $this->filter_cache[$key] = $item;

            $this->addFilterItem($item);

            if ($this->hasSessionValue($item->getFieldId())) { // Supports filter default values
                $item->readFromSession();
            }
        }
    }

    protected function hasSessionValue(string $field_id): bool
    {
        // Not set (null) on first visit, false on reset filter, string if is set
        return (isset($_SESSION["form_" . $this->getId()][$field_id]) && $_SESSION["form_" . $this->getId()][$field_id] !== false);
    }

    protected function initFilterFields(): void
    {
        $this->filter_fields = [
            "user" => [
                PropertyFormGUI::PROPERTY_CLASS => ilSelectInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => $this->getUserFilterOptions()
            ]
        ];
    }

    protected function initId(): void
    {
        $this->setId(self::ID_PREFIX . $this->parent_obj->getObjId());
    }

    protected function initTitle(): void
    {
        $this->setTitle($this->dic->language()->txt('history'));
    }

    protected function fillRow(array $row): void
    {
        $this->tpl->setVariable('VALUES', $this->formatValues($row['values']));
        $this->tpl->setVariable('USER', ilObjUser::_lookupFullname($row['usr_id']) . ', [' . ilObjUser::_lookupLogin($row['usr_id']) . ']');
        $this->tpl->setVariable('DATE', $row['timestamp']->get(IL_CAL_FKT_DATE, 'd.m.Y H:i:s'));
    }

    protected function formatValues(array $values): string
    {
        // this should be a template, but i'm too lazy
        $string = '<table class="xudf_log_values">';
        $string .= '<tr><th>' . $this->plugin->txt('udf_field') . '</th><th>' . $this->dic->language()->txt('value') . '</th></tr>';
        foreach ($values as $title => $value) {
            $string .= '<tr>';
            $string .= '<td>' . $title . '</td>';
            $string .= '<td>' . $value . '</td>';
            $string .= '</tr>';
        }

        return $string . '</table>';
    }

    protected function getUserFilterOptions(): array
    {
        $result = $this->dic->database()->query(
            'SELECT DISTINCT(usr_id) FROM ' . xudfLogEntry::TABLE_NAME
        );
        $options = ['' => '-'];
        while ($rec = $this->dic->database()->fetchAssoc($result)) {
            $options[$rec['usr_id']] = ilObjUser::_lookupFullname($rec['usr_id']) . ', [' . ilObjUser::_lookupLogin($rec['usr_id']) . ']';
        }

        return $options;
    }
}
