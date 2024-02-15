<?php

use srag\CustomInputGUIs\UdfEditor\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\UdfEditor\TableGUI\TableGUI;
use srag\DIC\UdfEditor\Exception\DICException;

class xudfLogTableGUI extends TableGUI
{
    public const ID_PREFIX = 'xudf_log_table_';
    public const PLUGIN_CLASS_NAME = ilUdfEditorPlugin::class;
    public const ROW_TEMPLATE = 'tpl.log_table_row.html';

    protected xudfLogGUI $parent_obj;

    public function __construct($parent, $parent_cmd)
    {
        $this->parent_obj = $parent;
        parent::__construct($parent, $parent_cmd);
        self::dic()->ui()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/default/log_table.css');
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
        $this->addColumn(self::plugin()->translate('values'));
        $this->addColumn(self::dic()->language()->txt('user'), 'user');
        $this->addColumn(self::dic()->language()->txt('date'), 'timestamp');
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
        $this->setTitle(self::dic()->language()->txt('history'));
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
        $string .= '<tr><th>' . self::plugin()->translate('udf_field') . '</th><th>' . self::dic()->language()->txt('value') . '</th></tr>';
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
        $result = self::dic()->database()->query(
            'SELECT DISTINCT(usr_id) FROM ' . xudfLogEntry::TABLE_NAME
        );
        $options = ['' => '-'];
        while ($rec = self::dic()->database()->fetchAssoc($result)) {
            $options[$rec['usr_id']] = ilObjUser::_lookupFullname($rec['usr_id']) . ', [' . ilObjUser::_lookupLogin($rec['usr_id']) . ']';
        }

        return $options;
    }
}
