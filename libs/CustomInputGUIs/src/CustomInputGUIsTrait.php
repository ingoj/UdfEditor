<?php

namespace srag\Plugins\UdfEditor\Libs\CustomInputGUIs;

trait CustomInputGUIsTrait
{
    final protected static function customInputGUIs(): CustomInputGUIs
    {
        return CustomInputGUIs::getInstance();
    }
}
