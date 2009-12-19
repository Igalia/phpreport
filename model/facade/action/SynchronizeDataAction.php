<?php

/** File for SynchronizeDataAction
 *
 *  This file just contains {@link SynchronizeDataAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserVO.php');

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
