<?php

/**
 * Class xudfFormConfigurationGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy xudfFormConfigurationGUI: ilObjUdfEditorGUI
 */
class xudfFormConfigurationGUI extends xudfGUI {


    const SUBTAB_SETTINGS = 'settings';
    const SUBTAB_FORM_CONFIGURATION = 'form_configuration';

    const CMD_FORM_CONFIGURATION = 'index';
    const CMD_ADD_UDF_FIELD = 'addUdfField';
    const CMD_ADD_SEPARATOR = 'addSeparator';
    const CMD_CREATE = 'create';
    const CMD_EDIT = 'edit';
    const CMD_UPDATE = 'update';
    const CMD_DELETE = 'delete';
    const CMD_CONFIRM_DELETE = 'confirmDelete';

    protected function performCommand($cmd) {
        switch ($cmd) {
            case self::CMD_STANDARD:
                $this->initToolbar();
                break;
            default:
                break;

        }
        parent::performCommand($cmd);
    }


    protected function setSubtabs() {
        $this->tabs->addSubTab(self::SUBTAB_SETTINGS, $this->lng->txt(self::SUBTAB_SETTINGS), $this->ctrl->getLinkTargetByClass(xudfSettingsGUI::class));
        $this->tabs->addSubTab(self::SUBTAB_FORM_CONFIGURATION, $this->lng->txt(self::SUBTAB_FORM_CONFIGURATION), $this->ctrl->getLinkTargetByClass(xudfFormConfigurationGUI::class, self::CMD_STANDARD));
        $this->tabs->setSubTabActive(self::SUBTAB_FORM_CONFIGURATION);
    }

    protected function initToolbar() {
        $add_udf_field = ilLinkButton::getInstance();
        $add_udf_field->setCaption($this->pl->txt('add_udf_field'), false);
        $add_udf_field->setUrl($this->ctrl->getLinkTarget($this, self::CMD_ADD_UDF_FIELD));
        $this->toolbar->addButtonInstance($add_udf_field);
        
        $add_separator = $add_udf_field = ilLinkButton::getInstance();
        $add_separator->setCaption($this->pl->txt('add_separator'), false);
        $add_separator->setUrl($this->ctrl->getLinkTarget($this, self::CMD_ADD_SEPARATOR));
        $this->toolbar->addButtonInstance($add_separator);
    }

    protected function index() {
        $xudfFormConfigurationTableGUI = new xudfFormConfigurationTableGUI($this, self::CMD_STANDARD);
        $this->tpl->setContent($xudfFormConfigurationTableGUI->getHTML());
    }

    protected function addUdfField() {
        $xudfFormConfigurationFormGUI = new xudfFormConfigurationFormGUI($this, new xudfContentElement());
        $this->tpl->setContent($xudfFormConfigurationFormGUI->getHTML());
    }

    protected function addSeparator() {
        $element = new xudfContentElement();
        $element->setIsSeparator(true);
        $xudfFormConfigurationFormGUI = new xudfFormConfigurationFormGUI($this, $element);
        $this->tpl->setContent($xudfFormConfigurationFormGUI->getHTML());
    }



    protected function create() {
        $element = new xudfContentElement();
        $element->setIsSeparator($_POST[xudfFormConfigurationFormGUI::F_IS_SEPARATOR]);

        $xudfFormConfigurationFormGUI = new xudfFormConfigurationFormGUI($this, $element);
        $xudfFormConfigurationFormGUI->setValuesByPost();
        if (!$xudfFormConfigurationFormGUI->saveForm()) {
            ilUtil::sendFailure($this->pl->txt('msg_incomplete'));
            $this->tpl->setContent($xudfFormConfigurationFormGUI->getHTML());
        }
        ilUtil::sendSuccess($this->pl->txt('form_saved'), true);
        $this->ctrl->redirect($this, self::CMD_STANDARD);
    }

    protected function update() {
        $element = new xudfContentElement(); // TODO POST['elment_ID]

        $xudfFormConfigurationFormGUI = new xudfFormConfigurationFormGUI($this, $element);
        $xudfFormConfigurationFormGUI->setValuesByPost();
        if (!$xudfFormConfigurationFormGUI->saveForm()) {
            ilUtil::sendFailure($this->pl->txt('msg_incomplete'));
            $this->tpl->setContent($xudfFormConfigurationFormGUI->getHTML());
        }
        ilUtil::sendSuccess($this->pl->txt('form_saved'), true);
        $this->ctrl->redirect($this, self::CMD_STANDARD);
    }

}