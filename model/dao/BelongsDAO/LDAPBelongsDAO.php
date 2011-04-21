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


/** File for LDAPBelongsDAO
 *
 *  This file just contains {@link LDAPBelongsDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGroupVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BelongsDAO/BelongsDAO.php');
include_once(PHPREPORT_ROOT . '/util/LDAPConnectionErrorException.php');
include_once(PHPREPORT_ROOT . '/util/LDAPInvalidOperationException.php');
include_once(PHPREPORT_ROOT . '/util/LDAPOperationErrorException.php');

/** DAO for relationship Belongs working with LDAP
 *
 *  This is the implementation for LDAP of {@link BelongsDAO}.
 *
 * @see BelongsDAO
 */
class LDAPBelongsDAO extends BelongsDAO{

    protected $ldapConnect;

    /** Belongs DAO for LDAP constructor.
     *
     * This is the constructor of the implementation for LDAP of {@link BelongsDAO}, and it calls its parent's constructor and creates a LDAP connection.
     *
     * @throws {@link DBConnectionErrorException}
     * @see BelongsDAO::__construct()
     */
    function __construct() {

        parent::__construct();

        $parameters[] = ConfigurationParametersManager::getParameter('LDAP_SERVER');
        $parameters[] = ConfigurationParametersManager::getParameter('LDAP_PORT');

        $this->ldapConnect = ldap_connect($parameters[0], $parameters[1]);
        if ($this->ldapConnect == NULL)
            throw new LDAPConnectionErrorException("Server:" . $parameters[0] . " | Port:" . $parameters[1]);

    }

    /** Value object constructor from edge A for LDAP.
     *
     * This function creates a new {@link UserVO} with data retrieved from LDAP edge A (User).
     *
     * @param array $row an array with the values from a row.
     * @return UserVO a {@link UserVO} with its properties set to the values from <var>$row</var>.
     */
    protected function setAValues($row)
    {

        $userVO = new UserVO();

        $userVO->setId($row['id']);
        $userVO->setLogin($row['login']);
        $userVO->setPassword($row['password']);
        $userVO->setGroups((array) $this->getByUserLogin($userVO->getLogin()));

        return $userVO;
    }

    /** Value object constructor from edge B for LDAP.
     *
     * This function creates a new {@link UserGroupVO} with data retrieved from LDAP edge B (User Group).
     *
     * @param array $row an array with the values from a row.
     * @return UserGroupVO a {@link UserGroupVO} with its properties set to the values from <var>$row</var>.
     */
    protected function setBValues($row)
    {

        $userGroupVO = new UserGroupVO();

        $userGroupVO->setName($row['name']);

        return $userGroupVO;
    }

    /** Belongs entry retriever by id's for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param int $userId the id (that matches with a User) of the row we want to retrieve.
     * @param int $userGroupId the id (that matches with a User Group) of the row we want to retrieve.
     * @throws {@link LDAPInvalidOperationException}
     */
    protected function getByIds($userId, $userGroupId) {

        throw new LDAPInvalidOperationException('getByIds');
    }

    /** User Groups retriever by User id for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param int $userId the id of the User whose User Groups we want to retrieve.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function getByUserId($userId) {

        throw new LDAPInvalidOperationException('getByUserId');

    }

    /** User Groups retriever by User login for LDAP.
     *
     * This function retrieves the rows from User Group that are assigned through relationship Belongs to the User with
     * the login <var>$userLogin</var> and creates a {@link UserGroupVO} with data from each row.
     *
     * @param string $userLogin the login of the User whose User Groups we want to retrieve.
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see UserDAO, UserGroupDAO
     * @throws {@link LDAPOperationErrorException}
     */
    public function getByUserLogin($userLogin) {

        if (!$sr=@ldap_list($this->ldapConnect,"ou=Group," . ConfigurationParametersManager::getParameter('LDAP_BASE'),
                "(&(objectClass=posixGroup)(uniqueMember=uid=$userLogin,ou=People," . ConfigurationParametersManager::getParameter('LDAP_BASE') . "))",
                array("cn")
            )) {
            print("Can't get the LDAP's groups of this user\n");
        }

        $info = @ldap_get_entries($this->ldapConnect, $sr);
        $groups=array();
        for ($i=0;$i<$info["count"];$i++) {
            $group["name"]=$info[$i]["cn"][0];
            $groups[] = $this->setBValues($group);
        }

        return $groups;

    }

    /** Users retriever by User Group id for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param int $userGroupId the id of the User Group whose Users we want to retrieve.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function getByUserGroupId($userGroupId) {

        throw new LDAPInvalidOperationException('getByUserGroupId');

    }

    /** Users retriever by User Group name for LDAP.
     *
     * This function retrieves the rows from User table that are assigned through relationship Belongs to the User Group with
     * the name <var>$userGroupName</var> and creates a {@link UserVO} with data from each row.
     * If a user exists in LDAP but isn't present in DB, it won't be listed.
     *
     * @param string $userGroupName the name of the User Group whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows.
     * @see UserGroupDAO, UserDAO
     * @throws {@link LDAPOperationErrorException}
     */
    public function getByUserGroupName($userGroupName) {

        if (!$sr=ldap_list($this->ldapConnect,"ou=Group," . ConfigurationParametersManager::getParameter('LDAP_BASE'),
                "(&(objectClass=posixGroup)(cn=" . $userGroupName . "))",
                array("uniqueMember")
            ))
            throw new LDAPOperationErrorException("getByUserGroupName($userGroupName)");

        $info = ldap_get_entries($this->ldapConnect, $sr);

        for ($i=0;$i<$info[0]["uniquemember"]["count"];$i++) {
            strtok($info[0]["uniquemember"][$i],"=,");
            $user["login"]=strtok("=,");
            $usersLDAP[]=$this->setAValues($user);
        }

        $sql = "SELECT * FROM usr ORDER BY id ASC";

        $usersDB = $this->executeFromB($sql);

        foreach($usersLDAP as $index => $userLDAP) {
            foreach($usersDB as $userDB)
                if ($userLDAP->getLogin() == $userDB->getLogin())
                {
                    $userLDAP->setId($userDB->getId());
                    $userLDAP->setPassword($userDB->getPassword());
                }
            if($userLDAP->getId() == null)
                //the user doesn't exist in DB, delete it
                unset($usersLDAP[$index]);
        }

        return $usersLDAP;

    }

    /** Belongs relationship entry creator by User id and User Group id for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param int $userId the id of the User we want to relate to the User Group.
     * @param int $groupId the id of the User Group we want to relate to the User.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function create($userId, $userGroupId) {

        throw new LDAPInvalidOperationException('create');

    }

    /** Belongs relationship entry deleter by User id and User Group id for LDAP.
     *
     * This function just throws an exception. It exists only for maintaining a common interface.
     *
     * @param int $userId the id of the User whose relation to the User Group we want to delete.
     * @param int $groupId the id of the User Group whose relation to the User we want to delete.
     * @throws {@link LDAPInvalidOperationException}
     */
    public function delete($userId, $userGroupId) {

        throw new LDAPInvalidOperationException('delete');

    }
}




/*//Uncomment these lines in order to do a simple test of the Dao


$dao = new LDAPBelongsDAO();

// We recover all groups for a user

$groups = $dao->getByUserLogin("jaragunde");

foreach($groups as $group)
    print "Group: " . $group->getName() . "\n";

// We recover all users for a group

$users = $dao->getByUserGroupName("informesadm");

foreach($users as $user)
    print "User: " . $user->getLogin() . "\n";
*/
