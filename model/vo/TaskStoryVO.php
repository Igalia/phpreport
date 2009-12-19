<?php

/** File for TaskStoryVO
 *
 *  This file just contains {@link TaskStoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Task Stories
 *
 *  This class just stores Task Story data.
 *
 *  @property int $id database internal identifier.
 *  @property int $risk risk level.
 *  @property string $name name of this Task Story.
 *  @property DateTime $init date this Task began at.
 *  @property DateTime $end date this Task ended at.
 *  @property double $estHours estimated number of hours this Task Story will last.
 *  @property double $toDo pending working hours in this Task Story.
 *  @property DateTime $estEnd estimated end date of this Task Story.
 *  @property int $storyId database internal identifier of the associated Story.
 *  @property int $userId database internal identifier of the associated User.
 *  @property int $taskSectionId database internal identifier of the associated TaskSection.
 */
class TaskStoryVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $risk = NULL;
    protected $name = NULL;
    protected $estHours = NULL;
    protected $estEnd = NULL;
    protected $toDo = NULL;
    protected $init = NULL;
    protected $end = NULL;
    protected $storyId = NULL;
    protected $userId = NULL;
    protected $taskSectionId = NULL;

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

    public function setRisk($risk) {
    if (is_null($risk))
        $this->risk = $risk;
    else
            $this->risk = (int) $risk;
    }

    public function getRisk() {
        return $this->risk;
    }

    public function setEstHours($estHours) {
    if (is_null($estHours))
        $this->estHours = $estHours;
    else
            $this->estHours = (double) $estHours;
    }

    public function getEstHours() {
        return $this->estHours;
    }

    public function setToDo($toDo) {
    if (is_null($toDo))
        $this->toDo = $toDo;
    else
            $this->toDo = (double) $toDo;
    }

    public function getToDo() {
        return $this->toDo;
    }

    public function setEstEnd(DateTime $estEnd = NULL) {
        $this->estEnd = $estEnd;
    }

    public function getEstEnd() {
        return $this->estEnd;
    }

    public function setInit(DateTime $init = NULL) {
        $this->init = $init;
    }

    public function getInit() {
        return $this->init;
    }

    public function setEnd(DateTime $end = NULL) {
        $this->end = $end;
    }

    public function getEnd() {
        return $this->end;
    }

    public function setStoryId($storyId) {
        if (is_null($storyId))
        $this->storyId = $storyId;
    else
            $this->storyId = (int) $storyId;
    }

    public function getStoryId() {
        return $this->storyId;
    }

    public function setUserId($userId) {
        if (is_null($userId))
        $this->userId = $userId;
    else
            $this->userId = (int) $userId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setTaskSectionId($taskSectionId) {
        if (is_null($taskSectionId))
        $this->taskSectionId = $taskSectionId;
    else
            $this->taskSectionId = (int) $taskSectionId;
    }

    public function getTaskSectionId() {
        return $this->taskSectionId;
    }

    /**#@-*/

}
