<?php

abstract class xudfGUI
{
    public const CMD_STANDARD = 'index';

    protected ilCtrl $ctrl;

    protected ilObjUser $user;

    protected ilLanguage $lng;
    protected ilGlobalPageTemplate $tpl;

    protected ilTabsGUI $tabs;

    protected ilToolbarGUI $toolbar;

    protected ilUdfEditorPlugin $pl;

    protected ilObjUdfEditorGUI $parent_gui;

    public function __construct(ilObjUdfEditorGUI $parent_gui)
    {
        global $DIC;
        $this->ctrl = $DIC['ilCtrl'];
        $this->user = $DIC['ilUser'];
        $this->lng = $DIC['lng'];
        $this->tpl = $DIC['tpl'];
        $this->tabs = $DIC['ilTabs'];
        $this->toolbar = $DIC['ilToolbar'];
        $this->tree = $DIC->repositoryTree();
        $this->pl = ilUdfEditorPlugin::getInstance();
        $this->parent_gui = $parent_gui;
    }

    public function executeCommand(): void
    {
        $this->setSubtabs();
        $next_class = $this->ctrl->getNextClass();
        switch ($next_class) {
            default:
                $cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
                $this->performCommand($cmd);
                break;
        }
    }

    protected function performCommand($cmd): void
    {
        $this->{$cmd}();
    }

    protected function setSubtabs(): void
    {
        // overwrite if class has subtabs
    }

    public function getObjId(): int
    {
        return $this->parent_gui->getObjId();
    }

    public function getObject(): ilObjUdfEditor
    {
        return $this->parent_gui->getObject();
    }

    abstract protected function index();
}
