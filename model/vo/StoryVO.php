<?php

/** File for StoryVO
 *
 *  This file just contains {@link StoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Stories
 *
 *  This class just stores Story data.
 *
 *  @property int $id database internal identifier.
 *  @property boolean $accepted acceptance flag.
 *  @property string $name name of this Iteration.
 *  @property int $iterationId database internal identifier of the associated Iteration.
 *  @property int $userId database internal identifier of the associated User (the one who leads the Story).
 *  @property int $storyId database internal identifier of the associated Story (next one).
 */
class StoryVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $accepted = NULL;
    protected $name = NULL;
    protected $userId = NULL;
    protected $iterationId = NULL;
    protected $storyId = NULL;

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

    public function setUserId($userId) {
        if (is_null($userId))
        $this->userId = $userId;
    else
            $this->userId = (int) $userId;
    }

    public function getUserId() {
        return $this->userId;
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

    public function setStoryId($storyId) {
        if (is_null($storyId))
        $this->storyId = $storyId;
    else
            $this->storyId = (int) $storyId;
    }

    public function getStoryId() {
        return $this->storyId;
    }

    /**#@-*/

}
