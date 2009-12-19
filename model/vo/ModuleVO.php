<?php

/** File for ModuleVO
 *
 *  This file just contains {@link ModuleVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Modules
 *
 *  This class just stores Module data.
 *
 *  @property int $id database internal identifier.
 *  @property DateTime $init date this Module began at.
 *  @property DateTime $end date this Module ended at.
 *  @property string $summary a summary of this Module.
 *  @property string $name name of this Module.
 *  @property int $projectId database internal identifier of the associated Project.
 */
class ModuleVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $name = NULL;
    protected $init = NULL;
    protected $_end = NULL;
    protected $summary = NULL;
    protected $projectId = NULL;

    public function setId($id) {
    if (is_null($id))
        $this->id = $id;
    else
            $this->id = (int) $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = (string) $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setInit(DateTime $init = NULL) {
        $this->init = $init;
    }

    public function getInit() {
        return $this->init;
    }

    public function setEnd(DateTime $end = NULL) {
        $this->_end = $end;
    }

    public function getEnd() {
        return $this->_end;
    }

    public function setSummary($summary) {
        $this->summary = (string) $summary;
    }

    public function getSummary() {
        return $this->summary;
    }

    public function setProjectId($projectId) {
        if (is_null($projectId))
        $this->projectId = $projectId;
    else
            $this->projectId = (int) $projectId;
    }

    public function getProjectId() {
        return $this->projectId;
    }

    /**#@-*/

}
