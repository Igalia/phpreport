#!/bin/sh

# Copyright (c) 1998-2018 the Tinyproxy authors.
# Copyright (C) 2019 Igalia, S.L. <info@igalia.com>
#
# This file is part of PhpReport.
# Based on code from the Tinyproxy project <https://tinyproxy.github.io/>
#
# PhpReport is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# PhpReport is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.

SCRIPT_DIR="$(cd "$(dirname "${0}")" && pwd)"
GIT_DIR="${SCRIPT_DIR}/.git"

if test -d "${GIT_DIR}" ; then
	if type git >/dev/null 2>&1 ; then
		git describe --tags
	else
		sed 's/$/-git/' < VERSION
	fi
else
	cat VERSION
fi
