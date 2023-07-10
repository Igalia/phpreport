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

// Check if session exists, or alert user

function checkIfSessionExists() {
  Ext.Ajax.request({
    url: 'services/checkSessionActive.php',
    failure: function (response) {
      if (response.status == 401) {
        Ext.MessageBox.confirm('Session Expired', 'Do you want to login again?', function (btn) {
          if (btn === 'yes') {
            window.location.reload();
          }
        });
      }
    },
    success: function (response) {
      Ext.MessageBox.hide();
    }
  });
}

window.onload = function () {
  if (!window.location.pathname.endsWith('login.php')) {
    //do not run this check in the login screen!
    window.setInterval(checkIfSessionExists, 3600000);
    document.addEventListener('visibilitychange', checkIfSessionExists);
  }
};
