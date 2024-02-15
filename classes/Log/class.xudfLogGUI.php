<?php


/**
 * @ilCtrl_isCalledBy xudfLogGUI: ilObjUdfEditorGUI
 */
class xudfLogGUI extends xudfGUI
{
    protected function index(): void
    {
        $table = new xudfLogTableGUI($this, self::CMD_STANDARD);
        $this->tpl->setContent($table->getHTML());
    }

    protected function applyFilter(): void
    {
        $table = new xudfLogTableGUI($this, self::CMD_STANDARD);
        $table->writeFilterToSession();
        $table->resetOffset();
        $this->index();
    }

    protected function resetFilter(): void
    {
        $table = new xudfLogTableGUI($this, self::CMD_STANDARD);
        $table->resetFilter();
        $table->resetOffset();
        $this->index();
    }
}
