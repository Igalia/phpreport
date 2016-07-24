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


/** File for TemplateDAO
 *
 *  This file just contains {@link TemplateDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Tony Thomas <tthomas@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/TemplateVO.php');
/** DAO for Tasks
 *
 *  This is the base class for all types of Task DAOs responsible for working with data from Task table, providing a common interface.
 *
 * @see DAOFactory::getTaskDAO(), TaskVO
 */
abstract class TemplateDAO extends BaseDAO{

    /** Template DAO constructor.
     *
     * This is the base constructor of Template DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
        parent::__construct();
    }

    /** Template retriever by id.
     *
     * This function retrieves the row from Template table with the id <var>$templateId</var> and creates a {@link TemplateVO} with its data.
     *
     * @param int $templateId the id of the row we want to retrieve.
     * @return TemplateVO a value object {@link TemplateVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($templateId);

    /** Template retriever by User id.
     *
     * This function retrieves the rows from Template table that are associated with the User with
     * the id <var>$templateId</var> and creates a {@link TemplateVO} with data from each row.
     *
     * @param int $templateId the id of the User whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByUserId($templateId);

    /** Template creator.
     *
     * This function creates a new row for a Template by its {@link TemplateVO}.
     * The internal id of <var>$taskVO</var> will be set after its creation.
     *
     * @param TemplateVO $templateVO the {@link TemplateVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(TemplateVO $templateVO);

    /** Template batch creator.
     *
     * Equivalent to {@see create} for arrays of tasks.
     *
     * @param array $templates array of {@link TemplateVO} objects to be created.
     * @return int the number of rows that have been affected (it should be
     *         equal to the size of $tasks).
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function batchCreate($templates);

    /** Template deleter.
     *
     * This function deletes the data of a Template by its {@link TemplateVO}.
     *
     * @param TemplateVO $templateVO the {@link TaskVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(TemplateVO $templateVO);

	/** Template batch deleter.
	 *
	 * Equivalent to {@see delete} for arrays of tasks.
	 *
	 * @param array $templates array of {@link TemplateVO} objects to be deleted.
	 * @return int the number of rows that have been affected (it should be
	 *         equal to the size of $tasks).
	 * @throws {@link SQLQueryErrorException}
	 */
	public abstract function batchDelete($templates);


	/** Fetch Templates from database for a given user
	 *
	 * @param int $userId
	 * @return mixed
	 */
	public abstract function getUserTemplates($userId);

}
