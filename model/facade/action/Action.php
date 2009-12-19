<?php

/** File for Action
 *
 *  This file just contains {@link Action}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once('phpreport/util/ConfigurationParametersManager.php');

/** Base class for all actions
 *
 *  This class is extended by every action we create.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */
abstract class Action {

    /** Pre-action plugins parameter.
     *
     * This variable contains the name of the variable storing the pre-action plugins from <i>{@link config.php}</i>.
     *
     * @var string
     * @see config.php
     */
    protected $preActionParameter;

    /** Post-action plugins parameter.
     *
     * This variable contains the name of the variable storing the post-action plugins from <i>{@link config.php}</i>.
     *
     * @var string
     * @see config.php
     */
    protected $postActionParameter;

    /** Specific code execute.
     *
     * This is the function that contains the code of the specific action.
     *
     */
    abstract protected function doExecute();

    /** Plugins execute.
     *
     * This function executes a plugin list with the parameter <var>$status</var> (this is used for avoiding
     * post-action plugins execution when specific code had any kind of error).
     *
     * @param array $pluginNameList an array with plugin names to execute.
     * @param int $status the status of the specific action code execution.
     */
    private function runPlugins($pluginNameList, $status) {

        foreach((array) $pluginNameList as $pluginClassName) {
            require_once('phpreport/model/facade/actionplugin/' . $pluginClassName . ".php");

            $action = new $pluginClassName($this);
            $action->run($status);
        }

    }

    /** Action execute.
     *
     * This function executes all the action code, the specific one and the plugins. Firs the pre-action plugins are executed, then the specific code and, if
     * there were no problems, in last place the post-action plugins.
     *
     * @return mixed return value from the specific code.
     */
    final function execute() {
        $status = true;
    $returnValue = NULL;

        try {
            $preActionPlugins = ConfigurationParametersManager::getParameter($this->preActionParameter);
            $preActionPluginsList = preg_split("/[\s,]+/",$preActionPlugins);

            $this->runPlugins($preActionPluginsList, $status);
        }
        catch(UnknownParameterException $e) {
            //we suppose there are no plugins and continue
            //echo "preaction not found"."\n";
        }

        $emitedException = null;
        try {
            $returnValue = $this->doExecute();
        }
        catch(Exception $e) {
            $emitedException = $e;
            $status = false;
        }

        try {
            $postActionPlugins = ConfigurationParametersManager::getParameter($this->postActionParameter);
            $postActionPluginsList = preg_split("/[\s,]+/",$postActionPlugins);

            $this->runPlugins($postActionPluginsList, $status);
        }
        catch(UnknownParameterException $e) {
            //we suppose there are no plugins and continue
            //echo "postaction not found"."\n";
        }

        if ($emitedException != null)
            throw $emitedException;
        else
            return $returnValue;
    }

}
