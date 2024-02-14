<?php


abstract class xudfGUI
{
    public const CMD_STANDARD = 'index';
    /**
     * @var ilCtrl
     */
    protected $ctrl;
    /**
     * @var ilObjUser
     */
    protected $user;
    /**
     * @var ilLanguage
     */
    protected $lng;
    /**
     * @var ilTemplate
     */
    protected $tpl;
    /**
     * @var ilTabsGUI
     */
    protected $tabs;
    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;
    /**
     * @var ilUdfEditorPlugin
     */
    protected $pl;

    protected $parent_gui;



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



    public function executeCommand()
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



    protected function performCommand($cmd)
    {
        $this->{$cmd}();
    }



    protected function setSubtabs()
    {
        // overwrite if class has subtabs
    }


    /**
     * @return int
     */
    public function getObjId()
    {
        return $this->parent_gui->getObjId();
    }


    /**
     * @return ilObjUdfEditor
     */
    public function getObject()
    {
        return $this->parent_gui->getObject();
    }



    abstract protected function index();
}
