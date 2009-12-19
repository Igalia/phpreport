<?php

abstract class ActionPlugin {

    protected $pluggedAction;

    abstract public function run($status);

}
