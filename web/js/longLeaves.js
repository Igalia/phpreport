/*
 * Copyright (C) 2021 Igalia, S.L. <info@igalia.com>
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

const fetchItems = async (url, itemName) => {
    const res = await fetch(url, {
        method: 'GET',
        mode: 'same-origin',
        cache: 'no-cache',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'text/xml'
        },
        referrerPolicy: 'no-referrer',
    });
    const body = await res.text();
    parser = new DOMParser();
    xmlDoc = parser.parseFromString(body, "text/xml");
    return xmlDoc.getElementsByTagName(itemName);
}

var app = new Vue({
    el: '#longLeavesForm',
    data() {
        return {
          selectedProject: '',
          projects: [],
          selectedUser: '',
          users: [],
          serverMessages: [],
          initDate: '',
          endDate: ''
        }
    },
    created() {
        this.fetchInitialData();
    },
    methods: {
        fetchInitialData: async function () {
            let url = 'services/getProjectsService.php?active=true&type=leave';
            let items = await fetchItems(url, "project");
            let list = [];
            for (var i = 0; i < items.length; i++) {
                list.push({
                    value: items[i].getElementsByTagName("id")[0].innerHTML,
                    text: items[i].getElementsByTagName("fullDescription")[0].innerHTML,
                })
            }
            list.sort((a, b) => b.text > a.text ? -1 : 1);
            this.projects = list;

            url = 'services/getAllUsersService.php?active=true&filterEmployees=true';
            items = await fetchItems(url, "user");
            list = [];
            for (var i = 0; i < items.length; i++) {
                list.push({
                    value: items[i].children[1].innerHTML,
                    text: items[i].children[1].innerHTML,
                })
            }
            list.sort((a, b) => b.text > a.text ? -1 : 1);
            this.users = list;
        },
        submitForm: async function () {
            let url = 'services/updateLongLeaves.php?';
            url += `init=${this.initDate}&end=${this.endDate}`;
            url += `&projectId=${this.selectedProject}`;
            url += `&user=${this.selectedUser}`;

            const res = await fetch(url, {
                method: 'POST',
                mode: 'same-origin',
                cache: 'no-cache',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                referrerPolicy: 'no-referrer',
            });
            const body = await res.json();
            if ("error" in body) {
                this.serverMessages.push({ classes: "message error", text: `Error: ${body["error"]}` });
            } else {
                if (body["failed"] && body["failed"].length > 0) {
                    this.serverMessages.push({
                        classes: "message error",
                        text: `These dates couldn't be created: ${body["failed"].join(", ")}`
                    });
                }
                if (body["created"] && body["created"].length > 0) {
                    this.serverMessages.push({
                        classes: "message success",
                        text: `These dates were created: ${body["created"].join(", ")}`
                    });
                }
                if (this.serverMessages.length === 0) {
                    this.serverMessages.push({ classes: "message success", text: "Long Leave period was added." });
                }
            };
            this.$emit('flush-message');
        }
    },
    mounted() {
        let timer
        this.$on('flush-message', message => {
            clearTimeout(timer)
            timer = setTimeout(() => {
                this.serverMessages = []
            }, 5000)
        })
    }
})
