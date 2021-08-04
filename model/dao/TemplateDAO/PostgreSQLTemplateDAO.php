<?php
/*
 * Copyright (C) 2016 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */


/** File for PostgreSQLTaskDAO
 *
 *  This file just contains {@link PostgreSQLTaskDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 */
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/TemplateVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TemplateDAO/TemplateDAO.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

/** DAO for Templates in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link TaskDAO}.
 *
 * @see TaskDAO, TaskVO
 */
class PostgreSQLTemplateDAO extends TemplateDAO{

    /** Template DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link TemplateDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see TemplateDAO::__construct()
     */
    function __construct() {
        parent::__construct();
    }

    /** Template value object constructor for PostgreSQL.
     *
     * This function creates a new {@link TemplateVO} with data retrieved from database.
     *
     * @param array $row an array with the Task values from a row.
     * @return TemplateVO a {@link TemplateVO} with its properties set to the values from <var>$row</var>.
     * @see TemplateVO
     */
    protected function setValues($row) {
        $templateVO = new TemplateVO();

        $templateVO->setId($row['id']);
        $templateVO->setName($row['name']);
        $templateVO->setStory($row['story']);
        $templateVO->setStory($row['story']);
        if (strtolower($row['telework']) == "t")
            $templateVO->setTelework(True);
        elseif (strtolower($row['telework']) == "f")
            $templateVO->setTelework(False);
        if (strtolower($row['onsite']) == "t")
            $templateVO->setOnsite(True);
        elseif (strtolower($row['onsite']) == "f")
            $templateVO->setOnsite(False);
        $templateVO->setText($row['text']);
        $templateVO->setTtype($row['ttype']);
        $templateVO->setUserId($row['usrid']);
        $templateVO->setProjectId($row['projectid']);
        $templateVO->setTaskStoryId($row['task_storyid']);
        $templateVO->setInitTime($row['init_time']);
        $templateVO->setEndTime($row['end_time']);

        return $templateVO;
    }

    /** Template retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Template table with the id <var>$templateId</var> and creates a {@link TemplateVO} with its data.
     *
     * @param int $templateId the id of the row we want to retrieve.
     * @return TemplateVO a value object {@link TemplateVO} with its properties set to the values from the row.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($templateId) {
        if (!is_numeric($templateId))
            throw new SQLIncorrectTypeException($templateId);
        $sql = "SELECT * FROM template WHERE id=".$templateId;
        $result = $this->execute($sql);
        return $result[0] ?? NULL;
    }

    /** Templates retriever by User id for PostgreSQL.
     *
     * This function retrieves the rows from Templates table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link TemplateVO} with data from each row.
     *
     * @param int $userId the id of the User whose Tempaltes we want to retrieve.
     * @return array an array with value objects {@link TemplatesVO} with their properties set to the values from the rows
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserId($userId) {
        if (!is_numeric($userId))
            throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM template WHERE usrid=$userId";
        $result = $this->execute($sql);
        return $result;
    }

    /** Template creator for PostgreSQL.
     *
     * This function creates a new row for a Template by its {@link TemplateVO}.
     * The internal id of <var>$templateVO</var> will be set after its creation.
     *
     * @param TemplateVO $templateVO the {@link TemplateVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(TemplateVO $templateVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO template (name, story, telework, onsite, text, ttype, usrid, projectid, init_time, end_time, task_storyid) VALUES(" .
            DBPostgres::checkStringNull($templateVO->getName()) . ", " .
            DBPostgres::checkStringNull($templateVO->getStory()) . ", " .
            DBPostgres::boolToString($templateVO->isTelework()) . ", " .
            DBPostgres::boolToString($templateVO->isOnsite()) . ", " .
            DBPostgres::checkStringNull($templateVO->getText()) . ", " .
            DBPostgres::checkStringNull($templateVO->getTtype()) . ", " .
            DBPostgres::checkNull($templateVO->getUserId()) . ", " .
            DBPostgres::checkNull($templateVO->getProjectId()) . ", " .
            DBPostgres::checkNull($templateVO->getInitTime()) . ", " .
            DBPostgres::checkNull($templateVO->getEndTime()) . ", " .
            DBPostgres::checkNull($templateVO->getTaskStoryId()) .")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            throw new SQLQueryErrorException(pg_last_error());

        $templateVO->setId(DBPostgres::getId($this->connect, "template_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /**
     * Create Templates in group
     *
     * @param array $templates
     * @return int
     * @throws SQLQueryErrorException
     */
    public function batchCreate($templates) {
        $affectedRows = 0;

        foreach ($templates as $template) {
            $affectedRows += $this->create($template);
        }

        return $affectedRows;
    }

    /** Template deleter for PostgreSQL.
     *
     * This function deletes the data of a Task by its {@link TemplateVO}.
     *
     * @param TemplateVO $templateVO the {@link TemplateVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(TemplateVO $templateVO) {
        $affectedRows = 0;

        // Check for a task ID.
        if($templateVO->getId() >= 0) {
            $currTaskVO = $this->getById($templateVO->getId());
        }

        // Otherwise delete a task.
        if($currTaskVO) {
            $sql = "DELETE FROM template WHERE id=".$currTaskVO->getId();

            $res = pg_query($this->connect, $sql);
            if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

	/** Delete Templates in group
	 *
	 * @param $templates
	 * @return int
	 * @throws SQLQueryErrorException
	 */
	public function batchDelete($templates){
		$affectedRows = 0;

		foreach ($templates as $template) {
			$affectedRows += $this->delete($template);
		}

		return $affectedRows;
	}

    /** Fetch Templates from Database for a given user
     *
     * @param int $userId
     * @return array
     * @throws SQLQueryErrorException
     */
    public function getUserTemplates($userId) {
        $sql = "SELECT * FROM template where usrid=$userId";

        return $this->execute($sql);
    }
}