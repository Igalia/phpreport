<?php

/** File for CustomStoryVO
 *
 *  This file just contains {@link CustomStoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */


include_once('phpreport/model/vo/UserVO.php');

/** Custom VO for Stories
 *
 *  This class just stores Story and additional data.
 *
 *  @property int $id database internal identifier.
 *  @property boolean $accepted acceptance flag.
 *  @property string $name name of this Iteration.
 *  @property array $developers developers of this Story.
 *  @property UserVO $reviewer reviewer of this Story.
 *  @property double $estHours estimated working hours of this Story.
 *  @property double $spent working hours spent in this Story.
 *  @property double $done per-1 work done.
 *  @property double $overrun per-1 variation of real work versus estimated work.
 *  @property double $toDo pending working hours in this Story.
 *  @property int $iterationId database internal identifier of the associated Iteration.
 *  @property int $nextStoryId database internal identifier of the associated Story (next one).
 */
class CustomStoryVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $accepted = NULL;
    protected $name = NULL;
    protected $developers = NULL;
    protected $reviewer = NULL;
    protected $estHours = NULL;
    protected $spent = NULL;
    protected $done = NULL;
    protected $overrun = NULL;
    protected $toDo = NULL;
    protected $iterationId = NULL;
    protected $nextStoryId = NULL;

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

    public function setIterationId($iterationId) {
        if (is_null($iterationId))
        $this->iterationId = $iterationId;
    else
            $this->iterationId = (int) $iterationId;
    }

    public function getIterationId() {
        return $this->iterationId;
    }

    public function setNextStoryId($nextStoryId) {
        if (is_null($nextStoryId))
        $this->nextStoryId = $nextStoryId;
    else
            $this->nextStoryId = (int) $nextStoryId;
    }

    public function getNextStoryId() {
        return $this->nextStoryId;
    }

    /**#@-*/

}
