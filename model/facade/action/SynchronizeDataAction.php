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


/** File for SynchronizeDataAction
 *
 *  This file just contains {@link SynchronizeDataAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

/** Synchronize Data Action
 *
 *  This action is used for synchronizing data on DB and LDAP. It can synchronize data of only a user, or for all them if we don't pass one in the constructor.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class SynchronizeDataAction extends Action{

    /** The User
     *
     * This variable contains the User whose data we want to synchronize.
     *
     * @var UserVO
     */
    protected $user;

    /** SynchronizeDataAction constructor.
     *
     * This is just the constructor of this action. We can pass a user with optional parameter <var>$user</var> if we want to synchronize only its data.
     *
     * @param UserVO $user the User whose data we want to synchronize.
     */
    public function __construct(UserVO $user = NULL) {

    $this->user = $user;

        $this->preActionParameter="SYNCHRONIZE_DATA_PREACTION";
        $this->postActionParameter="SYNCHRONIZE_DATA_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that synchronizes data on DB and LDAP.
     *
     * @return int number of entries that have changed.
     */
    protected function doExecute() {

    $dao = DAOFactory::getUserGroupDAO();

    $users = $dao->getUsersByUserGroupName(ConfigurationParametersManager::getParameter("ALL_USERS_GROUP"));

    $different = 0;

    if (is_null($this->user))
        foreach($users as $user)
        {
            if (is_null($user->getId()))
            {
                $different++;
                $dao->create($user);
            }
            elseif (count($user->getGroups())==0)
            {
                $different++;
                $dao->delete($user);
            }
            var_dump($user->getGroups());
        }
    else
        foreach($users as $user)
        {
            if ($user->getLogin() == $this->user->getLogin())
            {
                if (is_null($user->getId()))
                {
                    $different++;
                    $dao->create($user);
                }
                elseif (is_null($user->getGroups()))
                {
                    $different++;
                    $dao->delete($user);
                }
                break;
            }
        }

    return $different;

    }

}


/*//Test code

$action= new SynchronizeDataAction();
$different = $action->execute();
print ("Different: " . $different . "\n");
*/
