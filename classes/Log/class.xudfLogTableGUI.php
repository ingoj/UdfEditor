<?php

use ILIAS\DI\Container;
use srag\Plugins\UdfEditor\Libs\CustomInputGUIs\PropertyFormGUI\PropertyFormGUI;


class xudfLogTableGUI extends TableGUI
{
    public const ID_PREFIX = 'xudf_log_table_';
    public const PLUGIN_CLASS_NAME = ilUdfEditorPlugin::class;
    public const ROW_TEMPLATE = 'tpl.log_table_row.html';

    protected xudfLogGUI $parent_obj;
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

    protected function getColumnValue(string $column, array|object $row, int $format = self::DEFAULT_FORMAT): string
    {
    }

    protected function getSelectableColumns2(): array
    {
        return [];
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
        $filter_values = $this->getFilterValues();
        $filter_user = $filter_values['user'];

        $where = xudfLogEntry::where(['obj_id' => $this->parent_obj->getObjId()]);
        if ($filter_user != '') {
            $where = $where->where(['usr_id' => $filter_user]);
        }
        $this->setData($where->getArray());
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
