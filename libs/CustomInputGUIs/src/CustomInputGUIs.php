<?php

namespace srag\Plugins\UdfEditor\Libs\CustomInputGUIs;

use srag\Plugins\UdfEditor\Libs\CustomInputGUIs\ViewControlModeUI\ViewControlModeUI;

final class CustomInputGUIs
{
    /**
     * @var self|null
     */
    protected static $instance = null;


    private function __construct()
    {

    }


    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    public function viewControlMode(): ViewControlModeUI
    {
        return new ViewControlModeUI();
    }
}
