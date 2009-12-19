<?php

include_once('phpreport/model/facade/actionplugin/ActionPlugin.php');

class EmailAdminPlugin extends ActionPlugin {

    public function __construct($action) {
        $this->pluggedAction = $action;
    }

    //TODO: actually send the email
    public function run($status) {
        if ($this->pluggedAction instanceof CreateUserAction) {
            if ($status)
                echo "ok\n";
            else
                echo "bad\n";
        }
        // if the action doesn't belong to one of those classes,
        // we do nothing
    }
}
