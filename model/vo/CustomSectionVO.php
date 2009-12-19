<?php

/** File for CustomSectionVO
 *
 *  This file just contains {@link CustomSectionVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */


include_once('phpreport/model/vo/UserVO.php');

/** Custom VO for Sections
 *
 *  This class just stores Section and additional data.
 *
 *  @property int $id database internal identifier.
 *  @property boolean $accepted acceptance flag.
 *  @property string $name name of this Module.
 *  @property string $text text with information about this Section.
 *  @property array $developers developers of this Section.
 *  @property UserVO $reviewer reviewer of this Section.
 *  @property double $estHours estimated working hours of this Section.
 *  @property double $spent working hours spent in this Section.
 *  @property double $done per-1 work done.
 *  @property double $overrun per-1 variation of real work versus estimated work.
 *  @property double $toDo pending working hours in this Section.
 *  @property int $moduleId database internal identifier of the associated Module.
 */
class CustomSectionVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $accepted = NULL;
    protected $name = NULL;
    protected $text = NULL;
    protected $developers = NULL;
    protected $reviewer = NULL;
    protected $estHours = NULL;
    protected $spent = NULL;
    protected $done = NULL;
    protected $overrun = NULL;
    protected $toDo = NULL;
    protected $moduleId = NULL;

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

    public function setText($text) {
        $this->text = (string) $text;
    }

    public function getText() {
        return $this->text;
    }

    public function setAccepted($accepted) {
        $this->accepted = (boolean) $accepted;
    }

    public function getAccepted() {
        return $this->accepted;
    }

    public function setEstHours($estHours) {
        $this->estHours = (double) $estHours;
    }

    public function getEstHours() {
        return $this->estHours;
    }

    public function setSpent($spent) {
        $this->spent = (double) $spent;
    }

    public function getSpent() {
        return $this->spent;
    }

    public function setDone($done) {
        $this->done = (double) $done;
    }

    public function getDone() {
        return $this->done;
    }

    public function setOverrun($overrun) {
        $this->overrun = (double) $overrun;
    }

    public function getOverrun() {
        return $this->overrun;
    }

    public function setToDo($toDo) {
        $this->toDo = (double) $toDo;
    }

    public function getToDo() {
        return $this->toDo;
    }

    public function setDevelopers($developers) {
        if (is_null($developers))
        $this->developers = $developers;
    else
            $this->developers = (array) $developers;
    }

    public function getDevelopers() {
        return $this->developers;
    }

    public function setReviewer(UserVO $reviewer) {
        $this->reviewer = $reviewer;
    }

    public function getReviewer() {
        return $this->reviewer;
    }

    public function setModuleId($moduleId) {
        if (is_null($moduleId))
        $this->moduleId = $moduleId;
    else
            $this->moduleId = (int) $moduleId;
    }

    public function getModuleId() {
        return $this->moduleId;
    }

    /**#@-*/

}
