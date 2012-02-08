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


/** File for UsersFacade
 *
 *  This file just contains {@link UsersFacade}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/CreateUserAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteUserAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUserAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetAllUsersAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUsersByAreaIdDateAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUserByLoginAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateUserAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/ExtraHoursReportAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetPendingHolidayHoursAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateCustomEventAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteCustomEventAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateCustomEventAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateExtraHourAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteExtraHourAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetAllExtraHoursAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateExtraHourAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateUserGroupAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetAllUserGroupsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteUserGroupAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUserGroupByNameAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/AssignUserToUserGroupAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeassignUserFromUserGroupAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateUserGroupAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUserCityHistoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateCityHistoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteCityHistoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateCityHistoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUserJourneyHistoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateJourneyHistoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteJourneyHistoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateJourneyHistoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUserAreaHistoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateAreaHistoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteAreaHistoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateAreaHistoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUserHourCostHistoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateHourCostHistoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteHourCostHistoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateHourCostHistoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetIterationProjectAreaTodayUsersAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetStoryIterationProjectAreaTodayUsersAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetModuleProjectAreaTodayUsersAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetSectionModuleProjectAreaTodayUsersAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/LoginAction.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomEventVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/ExtraHourVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGroupVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/AreaHistoryVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/JourneyHistoryVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CityHistoryVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/HourCostHistoryVO.php');

/** Users Facade
 *
 *  This Facade contains the functions used tasks related to Users.
 *
 * @package PhpReport
 * @subpackage facade
 * @todo create the retrieval functions.
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
abstract class UsersFacade {

    /** User login Function
     *
     *  This function is used for User's login.
     *
     * @param string $login the User's login.
     * @param string $password the User's password.
     * @return UserVO if the login was succesful, it returns the User that made it.
     * @throws {@link IncorrectLoginException}
     */
    static function Login($login, $password) {

    $action = new LoginAction($login, $password);

    return $action->execute();

    }

    /** Get User Function
     *
     *  This action is used for retrieving a User.
     *
     * @param int $id the database identifier of the User we want to retieve.
     * @return UserVO the User as a {@link UserVO} with its properties set to the values from the row.
     */
    static function GetUser($userId) {

    $action = new GetUserAction($userId);

    return $action->execute();

    }

    /** Get User By Login Function
     *
     *  This action is used for retrieving a User by his/her login.
     *
     * @param string $login the login of the User we want to retieve.
     * @return UserVO the User as a {@link UserVO} with its properties set to the values from the row.
     */
    static function GetUserByLogin($userLogin) {

    $action = new GetUserByLoginAction($userLogin);

    return $action->execute();

    }

    /** Get all Users Function
     *
     *  This action is used for retrieving all Users.
     *
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetAllUsers() {

    $action = new GetAllUsersAction();

    return $action->execute();

    }

    /** Create User Function
     *
     *  This function is used for creating a new User.
     *
     * @param UserVO $user the User value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateUser(UserVO $user) {

    $action = new CreateUserAction($user);

    return $action->execute();

    }

    /** Delete User Function
     *
     *  This function is used for deleting a User.
     *
     * @param UserVO $user the User value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteUser(UserVO $user) {

    $action = new DeleteUserAction($user);

    return $action->execute();

    }

    /** Update User Function
     *
     *  This function is used for updating a User.
     *
     * @param UserVO $user the User value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateUser(UserVO $user) {

    $action = new UpdateUserAction($user);

    return $action->execute();

    }

    /** Get Extra Hours report Function
     *
     *  This function is used for retrieving information about Extra Hours done by users.<br/>
     * It returns an associative array with the following data:
     * <ul>
     * <li>Position 0: it has other array with data from all Users:
     * <ul>
     * <li>'total_hours': number of hours all Users have worked in the specified interval.</li>
     * <li>'workable_hours': number of hours all Users must work in the specified interval according to their journey.</li>
     * <li>'extra_hours': number of extra hours all Users have worked, according to the previous data.</li>
     * <li>'total_extra_hours': number of extra hours all Users have worked as for now (not just in the given interval).
     * </ul></li>
     * <li>Position 1: it has other array with data related to each User's login:
     * <ul>
     * <li>'total_hours': number of hours a User has worked in the specified interval.</li>
     * <li>'workable_hours': number of hours a User must work in the specified interval according to his/her journey.</li>
     * <li>'extra_hours': number of extra hours a User has worked, according to the previous data.</li>
     * <li>'total_extra_hours': number of extra hours a User has worked as for now (not just in the given interval).</li>
     * </ul></li></ul>
     *
     * @param DateTime $init the initial date of the interval whose Extra Hours we want to retrieve.
     * @param DateTime $end the ending date of the interval whose Extra Hours we want to retrieve.
     * @param UserVO $user the User whose Extra Hours report we want to retrieve.
     * @return array an associative array (it's described above).
     */
    static function ExtraHoursReport(DateTime $init, DateTime $end, UserVO $user = NULL) {

    $action = new ExtraHoursReportAction($init, $end, $user);

    return $action->execute();

    }

    /** Get pending Holiday Hours Function
     *
     *  This function is used for retrieving pending holiday hours for Users.
     *
     * @param DateTime $init the initial date of the interval whose pending holiday hours we want to retrieve.
     * @param DateTime $end the ending date of the interval whose pending holiday hours we want to retrieve.
     * @param UserVO $user the User whose pending holiday hours we want to retrieve.
     * @return array an associative array with the number of pending holiday hours related to each User's login.
     */
    static function GetPendingHolidayHours(DateTime $init, DateTime $end, UserVO $user = NULL) {

    $action = new GetPendingHolidayHoursAction($init, $end, $user);

    return $action->execute();

    }

    /** User retriever by Project Iteration Function
     *
     * This function retrieves the Users assigned to the same Area as a Project Iteration with id <var>$iterationid</var> today.
     *
     * @param int $iterationid the id of the Project Iteration whose related Users (through Area) we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    static function GetIterationProjectAreaTodayUsers($iterationid) {

    $action = new GetIterationProjectAreaTodayUsersAction($iterationid);

    return $action->execute();

    }

    /** Users retriever By Area Id and Date function
     *
     *  This function is used for retrieving all Users that are assigned to an
     *  Area on a specific date.
     *
     * @param int $areaId the database identifier of the Area whose related
     * Users we want to retieve.
     * @param DateTime $date the date when we want to check the Area
     * assignment.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetUsersByAreaIdDate($areaId, $date) {

        $action = new GetUsersByAreaIdDateAction($areaId, $date);

        return $action->execute();

    }

    /** User retriever by Project Module Function
     *
     * This function retrieves the Users assigned to the same Area as a Project Module with id <var>$moduleid</var> today.
     *
     * @param int $moduleid the id of the Project Module whose related Users (through Area) we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    static function GetModuleProjectAreaTodayUsers($moduleid) {

    $action = new GetModuleProjectAreaTodayUsersAction($moduleid);

    return $action->execute();

    }

    /** User retriever by Project Iteration Story Function
     *
     * This function retrieves the Users assigned to the same Area as a Project Story with id <var>$storyid</var> today.
     *
     * @param int $storyid the id of the Project Story whose related Users (through Area) we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    static function GetStoryIterationProjectAreaTodayUsers($storyid) {

    $action = new GetStoryIterationProjectAreaTodayUsersAction($storyid);

    return $action->execute();

    }

    /** User retriever by Project Module Section Function
     *
     * This function retrieves the Users assigned to the same Area as a Project Section with id <var>$sectionid</var> today.
     *
     * @param int $sectionid the id of the Project Section whose related Users (through Area) we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    static function GetSectionModuleProjectAreaTodayUsers($sectionid) {

    $action = new GetSectionModuleProjectAreaTodayUsersAction($sectionid);

    return $action->execute();

    }

    /** Create Custom Event Function
     *
     *  This function is used for creating a new Custom Event.
     *
     * @param CustomEventVO $customEvent the Custom Event value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateCustomEvent(CustomEventVO $customEvent) {

    $action = new CreateCustomEventAction($customEvent);

    return $action->execute();

    }

    /** Delete Custom Event Function
     *
     *  This function is used for deleting a Custom Event.
     *
     * @param CustomEventVO $customEvent the Custom Event value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteCustomEvent(CustomEventVO $customEvent) {

    $action = new DeleteCustomEventAction($customEvent);

    return $action->execute();

    }

    /** Update Custom Event Function
     *
     *  This function is used for updating a Custom Event.
     *
     * @param CustomEventVO $customEvent the Custom Event value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateCustomEvent(CustomEventVO $customEvent) {

    $action = new UpdateCustomEventAction($customEvent);

    return $action->execute();

    }

    /** Get All Extra Hours Function
     *
     *  This action is used for retrieving all Extra Hour objects.
     *
     * @return array an array with value objects {@link ExtraHourVO} with their
     *   properties set to the values from the rows and ordered ascendantly by
     *   their database internal identifier.
     */
    static function GetAllExtraHours() {

        $action = new GetAllExtraHoursAction();

        return $action->execute();

    }

    /** Create Extra Hour Function
     *
     *  This function is used for creating a new Extra Hour.
     *
     * @param ExtraHourVO $extraHour the Extra Hour value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateExtraHour(ExtraHourVO $extraHour) {

    $action = new CreateExtraHourAction($extraHour);

    return $action->execute();

    }

    /** Delete Extra Hour Function
     *
     *  This function is used for deleting an Extra Hour.
     *
     * @param ExtraHourVO $extraHour the Extra Hour value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteExtraHour(ExtraHourVO $extraHour) {

    $action = new DeleteExtraHourAction($extraHour);

    return $action->execute();

    }

    /** Update Extra Hour Function
     *
     *  This function is used for updating an Extra Hour.
     *
     * @param ExtraHourVO $extraHour the Extra Hour value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateExtraHour(ExtraHourVO $extraHour) {

    $action = new UpdateExtraHourAction($extraHour);

    return $action->execute();

    }

    /** Create User Group Function
     *
     *  This function is used for creating a new User Group.
     *
     * @param UserGroupVO $userGroup the User Group value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateUserGroup(UserGroupVO $userGroup) {

    $action = new CreateUserGroupAction($userGroup);

    return $action->execute();

    }

    /** Get all User Groups Function
     *
     *  This action is used for retrieving all User Groups.
     *
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetAllUserGroups() {

    $action = new GetAllUserGroupsAction();

    return $action->execute();

    }

    /** Get UserGroup by Name Function
     *
     *  This function is used for retrieving a UserGroup by its name.
     *
     * @param string $name the name of the UserGroup we want to retieve.
     * @return UserGroupVO the UserGroup as a {@link UserGroupVO} with its properties set to the values from the row.
     */
    static function GetUserGroupByName($userGroupName) {

        $action = new GetUserGroupByNameAction($userGroupName);

        return $action->execute();

    }

    /** User Group Assigning
     *
     *  This function is used for assigning a User to a User Group by their ids.
     *
     * @param int $userId the id of the User we want to assign.
     * @param int $userGroupId the UserGroup which we want to assign the User to.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function AssignUserToUserGroup($userId, $userGroupId) {

        $action = new AssignUserToUserGroupAction($userId, $userGroupId);

        return $action->execute();

    }

    /** User Group Deassigning
     *
     *  This function is used for deassigning a User from a User Group by their ids.
     *
     * @param int $userId the id of the User we want to deassign.
     * @param int $userGroupId the UserGroup which we want to deassign the User from.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function DeassignUserFromUserGroup($userId, $userGroupId) {

        $action = new DeassignUserFromUserGroupAction($userId, $userGroupId);

        return $action->execute();

    }

    /** Delete User Group Function
     *
     *  This function is used for deleting a new User Group.
     *
     * @param UserGroupVO $userGroup the User Group value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteUserGroup(UserGroupVO $userGroup) {

    $action = new DeleteUserGroupAction($userGroup);

    return $action->execute();

    }

    /** Update User Group Function
     *
     *  This function is used for updating a User Group.
     *
     * @param UserGroupVO $userGroup the User Group value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateUserGroup(UserGroupVO $userGroup) {

    $action = new UpdateUserGroupAction($userGroup);

    return $action->execute();

    }

    /** Get User Area Histories Function
     *
     *  This action is used for retrieving the whole Area History related to a User.
     *
     * @param string $userLogin the login of the User whose Area History entries we want to retieve.
     * @return array an array with value objects {@link AreaHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetUserAreaHistories($userLogin) {

    $action = new GetUserAreaHistoriesAction($userLogin);

    return $action->execute();

    }

    /** Create Area History entry Function
     *
     *  This function is used for creating a new entry on Area History.
     *
     * @param AreaHistoryVO $areaHistory the Area History value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateAreaHistory(AreaHistoryVO $areaHistory) {

    $action = new CreateAreaHistoryAction($areaHistory);

    return $action->execute();

    }

    /** Delete Area History entry Function
     *
     *  This function is used for deleting an entry on Area History.
     *
     * @param AreaHistoryVO $areaHistory the Area History value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteAreaHistory(AreaHistoryVO $areaHistory) {

    $action = new DeleteAreaHistoryAction($areaHistory);

    return $action->execute();

    }

    /** Update Area History entry Function
     *
     *  This function is used for updating an entry on Area History.
     *
     * @param AreaHistoryVO $areaHistory the Area History value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateAreaHistory(AreaHistoryVO $areaHistory) {

    $action = new UpdateAreaHistoryAction($areaHistory);

    return $action->execute();

    }

    /** Get User Journey Histories Function
     *
     *  This action is used for retrieving the whole Journey History related to a User.
     *
     * @param string $userLogin the login of the User whose Journey History entries we want to retieve.
     * @return array an array with value objects {@link JourneyHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetUserJourneyHistories($userLogin) {

    $action = new GetUserJourneyHistoriesAction($userLogin);

    return $action->execute();

    }

    /** Create Journey History entry Function
     *
     *  This function is used for creating a new entry on Journey History.
     *
     * @param JourneyHistoryVO $journeyHistory the Journey History value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateJourneyHistory(JourneyHistoryVO $journeyHistory) {

    $action = new CreateJourneyHistoryAction($journeyHistory);

    return $action->execute();

    }

    /** Delete Journey History entry Function
     *
     *  This function is used for deleting an entry on Journey History.
     *
     * @param JourneyHistoryVO $journeyHistory the Journey History value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteJourneyHistory(JourneyHistoryVO $journeyHistory) {

    $action = new DeleteJourneyHistoryAction($journeyHistory);

    return $action->execute();

    }

    /** Update Journey History entry Function
     *
     *  This function is used for updating an entry on Journey History.
     *
     * @param JourneyHistoryVO $journeyHistory the Journey History value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateJourneyHistory(JourneyHistoryVO $journeyHistory) {

    $action = new UpdateJourneyHistoryAction($journeyHistory);

    return $action->execute();

    }

    /** Get User City Histories Function
     *
     *  This action is used for retrieving the whole City History related to a User.
     *
     * @param string $userLogin the login of the User whose City History entries we want to retieve.
     * @return array an array with value objects {@link CityHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetUserCityHistories($userLogin) {

    $action = new GetUserCityHistoriesAction($userLogin);

    return $action->execute();

    }

    /** Create City History entry Function
     *
     *  This function is used for creating a new entry on City History.
     *
     * @param CityHistoryVO $cityHistory the City History value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateCityHistory(CityHistoryVO $cityHistory) {

    $action = new CreateCityHistoryAction($cityHistory);

    return $action->execute();

    }

    /** Delete City History entry Function
     *
     *  This function is used for deleting an entry on City History.
     *
     * @param CityHistoryVO $cityHistory the City History value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteCityHistory(CityHistoryVO $cityHistory) {

    $action = new DeleteCityHistoryAction($cityHistory);

    return $action->execute();

    }

    /** Update City History entry Function
     *
     *  This function is used for updating an entry on City History.
     *
     * @param CityHistoryVO $cityHistory the City History value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateCityHistory(CityHistoryVO $cityHistory) {

    $action = new UpdateCityHistoryAction($cityHistory);

    return $action->execute();

    }

    /** Get User Hour Cost Histories Function
     *
     *  This action is used for retrieving the whole Hour Cost History related to a User.
     *
     * @param string $userLogin the login of the User whose Hour Cost History entries we want to retieve.
     * @return array an array with value objects {@link HourCostHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetUserHourCostHistories($userLogin) {

    $action = new GetUserHourCostHistoriesAction($userLogin);

    return $action->execute();

    }

    /** Create Hour Cost History entry Function
     *
     *  This function is used for creating a new entry on Hour Cost History.
     *
     * @param HourCostHistoryVO $hourCostHistory the Hour Cost History value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateHourCostHistory(HourCostHistoryVO $hourCostHistory) {

    $action = new CreateHourCostHistoryAction($hourCostHistory);

    return $action->execute();

    }

    /** Delete Hour Cost History entry Function
     *
     *  This function is used for deleting an entry on Hour Cost History.
     *
     * @param HourCostHistoryVO $hourCostHistory the Hour Cost History value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteHourCostHistory(HourCostHistoryVO $hourCostHistory) {

    $action = new DeleteHourCostHistoryAction($hourCostHistory);

    return $action->execute();

    }

    /** Update Hour Cost History entry Function
     *
     *  This function is used for updating an entry on Hour Cost History.
     *
     * @param HourCostHistoryVO $hourCostHistory the Hour Cost History value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateHourCostHistory(HourCostHistoryVO $hourCostHistory) {

    $action = new UpdateHourCostHistoryAction($hourCostHistory);

    return $action->execute();

    }


}

//var_dump(UsersFacade::GetUserByLogin('jaragunde'));
