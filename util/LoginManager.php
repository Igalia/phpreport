<?php

/** File for LoginManager
 *
 *  This file just contains {@link LoginManager}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage util
 * @author Jacobo Aragunde Perez <jaragunde@igalia.com>
 */

require_once('phpreport/model/facade/UsersFacade.php');
require_once('phpreport/model/vo/UserVO.php');
require_once('phpreport/model/vo/UserGroupVO.php');

/** Login Manager
 *
 * Utility class containing two functions used by the controller
 * to manage login and authorization.
 */
class LoginManager {

  /** Login utility function
   *
   * If invoked with the parameters $login and $pass, it will
   * perform the authentication over the model layer, get an
   * object with the user and save it into the session, under
   * $_SESSION['user']. If invoked without parameters, it will
   * check the existance of the session variable 'user' (which
   * is used to know if the user had already logged in).
   *
   * @param string $login The login name of the user.
   * @param string $password The password (clear) of the user.
   * @return boolean True if the user has (or already had) logged
   * in correctly.
   */
  public static function login($login=NULL, $password=NULL) {

    session_start();

    // we are already logged in
    if ($login==NULL && $password==NULL && isset($_SESSION['user']))
      return true;

    // if we receive the user and password, we try to log in
    try{
      $user = UsersFacade::Login($login, $password);

      unset($_SESSION['user']);
      $_SESSION['user'] = $user;

      return true;
    }
    catch(IncorrectLoginException $exc){
      return false;
    }
  }

  /** Login check utility function
   *
   * This function checks whether the User is logged or not,
   * through User in the session, and returns that User in that
   * case. If it's invoked with the parameter <var>$sid</var>, it'll
   * check the User in that session.
   *
   * @param string $sid Session identifier (optional).
   * @return UserVO the User if it's logged in already, or a NULL
   * value otherwise.
   */
  function isLogged($sid = NULL)
  {

    if ($sid)
      session_id($sid);

    session_start();

    if (empty($_SESSION['user']))
        return NULL;

    return $_SESSION['user'];

    }

  /** Authorization utility function
   *
   * It checks the logged user against the permissions array. It
   * will retrieve the user groups and check the permissions of
   * each one to access the current url. If the parameter $sid
   * is passed, it will try to retrieve the data from the session
   * with that identifier.
   *
   * @param string $sid Session identifier (optional).
   * @return boolean true if the user belongs to a group able
   * to open the current url, false otherwise.
   */
  public static function isAllowed($sid=NULL) {

    /* We include the file with the array of permissions */
    require('phpreport/config/permissions.php');

    if ($sid!=NULL)
      session_id($sid);

    session_start();

    if (isset($_SESSION['user'])) {
      $user=$_SESSION['user'];

      foreach ($user->getGroups() as $group) {
        $url = explode($urlHeader, $_SERVER["SCRIPT_NAME"]);
        if (in_array($url[1], $permissions[$group->getName()]))
          return true;
      }
    }
    return false;

  }
}
