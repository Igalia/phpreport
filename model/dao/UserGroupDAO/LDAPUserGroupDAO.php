<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
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


/** File for LDAPUserGroupDAO
 *
 *  This file just contains {@link LDAPUserGroupDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/LDAPConnectionErrorException.php');
include_once(PHPREPORT_ROOT . '/util/LDAPInvalidOperationException.php');
include_once(PHPREPORT_ROOT . '/util/LDAPOperationErrorException.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGroupVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserGroupDAO/UserGroupDAO.php');

/** DAO for User Groups working with LDAP
 *
 *  This is the implementation for LDAP of {@link UserGroupDAO}.
 *
 * @see UserGroupDAO, UserGroupVO
 */
class LDAPUserGroupDAO extends UserGroupDAO{

    /** User Group DAO for LDAP constructor.
     *
     * This is the constructor of the implementation for LDAP of {@link UserGroupDAO}, and it creates a LDAP connection.
     *
     * @throws {@link LDAPConnectionErrorException}
     * @todo create connection pool and get LDAP connection from there.
     * @see UserGroupDAO::__construct()
     */
    function __construct() {

        $parameters[] = ConfigurationParametersManager::getParameter('LDAP_SERVER');
        $parameters[] = ConfigurationParametersManager::getParameter('LDAP_PORT');

        $this->connect = ldap_connect($parameters[0], $parameters[1]);
        if ($this->connect == NULL)
            throw new LDAPConnectionErrorException("Server:" . $parameters[0] . " | Port:" . $parameters[1]);
    }

    /** User Group value object constructor for LDAP.
     *
     * This function just throws an exception. It exists only to fulfil the interface
     * declared in parent classes.
     *
     * @throws {@link LDAPInvalidOperationException}
     */
    protected function setValues($row) {

         throw new LDAPInvalidOperationException('setValues');
    }

    /** User Group retriever by name for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param string $groupName the name of the user group we want to retrieve.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function getByUserGroupName($groupName) {

         throw new LDAPInvalidOperationException('getByUserGroupName');
    }

    /** User Group retriever by id for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param int $groupId the id of the row we want to retrieve.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function getById($groupId) {

        throw new LDAPInvalidOperationException('getById');

    }

    /** Users retriever by User Group id for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param int $userGroupId the id of the User Group whose Users we want to retrieve.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function getUsers($userGroupId) {

        throw new LDAPInvalidOperationException('getUsers');

    }

    /** Users retriever by User Group name for LDAP.
     *
     * This function retrieves the rows from User that are assigned through relationship Belongs to the User Group with
     * the name <var>$userGroupName</var> and creates a {@link UserVO} with data from each row.
     *
     * @param int $userGroupName the name of the User Group whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows.
     * @see BelongsDAO, UserDAO
     * @throws {@link LDAPOperationErrorException}
     */
    public function getUsersByUserGroupName($userGroupName) {

        $dao = DAOFactory::getBelongsDAO();
        return $dao->getByUserGroupName($userGroupName);
    }

    /** Belongs relationship entry creator by User Group id and User id for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param int $userGroupId the id of the User Group we want to relate to the User.
     * @param int $userId the id of the User we want to relate to the User Group.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function addUser($userGroupId, $userId) {

        throw new LDAPInvalidOperationException('addUser');

    }

    /** Belongs relationship entry deleter by User Group id and User id for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param int $userGroupId the id of the User Group whose relation to the User we want to delete.
     * @param int $userId the id of the User whose relation to the User Group we want to delete.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function removeUser($userGroupId, $userId) {

        throw new LDAPInvalidOperationException('removeUser');

    }

    /** User Groups retriever for LDAP.
     *
     * Retrieves the list of LDAP user groups that PhpReport takes into account,
     * which are set in the configuration parameter USER_GROUPS.
     *
     * @return array an array with value objects {@link UserGroupVO}.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function getAll() {
        $groups = array();
        $groupNames = explode(',', ConfigurationParametersManager::getParameter('USER_GROUPS'));
        foreach ($groupNames as $groupName) {
            $userGroupVO = new UserGroupVO();
            $userGroupVO->setName($groupName);
            $groups[] = $userGroupVO;
        }
        return $groups;
    }

    /** User Group updater for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param UserGroupVO $userGroupVO the {@link UserGroupVO} with the data we want to update on database.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function update(UserGroupVO $userGroupVO) {

        throw new LDAPInvalidOperationException('update');
    }

    /** User Group creator for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param UserGroupVO $userGroupVO the {@link UserGroupVO} with the data we want to insert on database.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function create(UserGroupVO $userGroupVO) {

        throw new LDAPInvalidOperationException('create');

    }

    /** User Group deleter for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param UserGroupVO $userGroupVO the {@link UserGroupVO} with the data we want to delete from database.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function delete(UserGroupVO $userGroupVO) {

        throw new LDAPInvalidOperationException('delete');

    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new LDAPUserGroupDAO();

$users = $dao->getUsersByUserGroupName("informesadm");

foreach ($users as $user)
    print $user->getLogin() . "\n";*/
