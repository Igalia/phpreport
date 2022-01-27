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
include_once(PHPREPORT_ROOT . '/model/vo/TemplateVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TemplateDAO/TemplateDAO.php');

/** DAO for Templates in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link TaskDAO}.
 *
 * @see TaskDAO, TaskVO
 */
class PostgreSQLTemplateDAO extends TemplateDAO{

    /** Template DAO constructor.
     *
     * Default constructor of TemplateDAO, it just calls parent constructor.
     *
     * @see BaseDAO::__construct()
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * This method is declared to fulfill TemplateVO as non-abstract, but it should not be used.
     * PDO::FETCH_CLASS now takes care of transforming DB rows into VO objects.
     */
    protected function setValues($row) {
        error_log("Unused TemplateVO::setValues() called");
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
        $result = $this->runSelectQuery(
            "SELECT * FROM template WHERE id=:id",
            [':id' => $templateId],
            'TemplateVO');
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
        $result = $this->runSelectQuery(
            "SELECT * FROM template WHERE usrid=:usrid",
            [':usrid' => $userId],
            'TemplateVO');
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

        $sql = "INSERT INTO template (name, story, telework, onsite, text, " .
                   "ttype, usrid, projectid, init_time, end_time) " .
               "VALUES(:name, :story, :telework, :onsite, :text, :ttype, " .
                   ":usrid, :projectid, :init_time, :end_time)";

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":name", $templateVO->getName(), PDO::PARAM_STR);
            $statement->bindValue(":story", $templateVO->getStory(), PDO::PARAM_STR);
            $statement->bindValue(":telework", $templateVO->isTelework(), PDO::PARAM_BOOL);
            $statement->bindValue(":onsite", $templateVO->isOnsite(), PDO::PARAM_BOOL);
            $statement->bindValue(":text", $templateVO->getText(), PDO::PARAM_STR);
            $statement->bindValue(":ttype", $templateVO->getTtype(), PDO::PARAM_STR);
            $statement->bindValue(":usrid", $templateVO->getUserId(), PDO::PARAM_INT);
            $statement->bindValue(":projectid", $templateVO->getProjectId(), PDO::PARAM_INT);
            $statement->bindValue(":init_time", $templateVO->getInitTime(), PDO::PARAM_INT);
            $statement->bindValue(":end_time", $templateVO->getEndTime(), PDO::PARAM_INT);
            $statement->execute();

            $templateVO->setId($this->pdo->lastInsertId('template_id_seq'));

            $affectedRows = $statement->rowCount();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new SQLQueryErrorException($e->getMessage());
        }
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

        $sql = "DELETE FROM template WHERE id=:id";

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute([':id' => $templateVO->getId()]);
            $affectedRows = $statement->rowCount();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new SQLQueryErrorException($e->getMessage());
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
        return $this->getByUserId($userId);
    }
}
