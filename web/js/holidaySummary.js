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

const xmlHeaders = {
    method: 'GET',
    mode: 'same-origin',
    cache: 'no-cache',
    credentials: 'same-origin',
    headers: {
        'Content-Type': 'text/xml'
    },
    referrerPolicy: 'no-referrer',
}

var app = new Vue({
    el: '#holidaySummaryReport',
    data() {
        return {
            weeks: {},
            displayData: {},
            isLoading: true,
            isLoadingProjects: true,
            projectUsers: {},
            projectsList: [],
            allProjects: [],
            autocompleteIsActive: false,
            searchProject: "",
            activeProject: 0,
            year: new Date().getFullYear()
        };
    },
    created() {
        this.fetchSummary();
    },
    computed: {
        downloadUrl() {
            let url = `services/getHolidaySummary.php?format=csv&users=${Object.keys(this.displayData).join(",")}`;
            return this.year ? `${url}&year=${this.year}` : url;
        },
    },
    methods: {
        async fetchProjects() {
            let url = 'services/getProjectsService.php?active=true&users=true';
            const res = await fetch(url, xmlHeaders);
            const body = await res.text();
            parser = new DOMParser();
            xmlDoc = parser.parseFromString(body, "text/xml");
            const projects = xmlDoc.getElementsByTagName("project");
            let parsedProjects = [];
            for (var i = 0; i < projects.length; i++) {
                parsedProjects.push({
                    id: projects[i].getElementsByTagName("id")[0].innerHTML,
                    name: projects[i].getElementsByTagName("fullDescription")[0].innerHTML,
                });
                const users = projects[i].getElementsByTagName("user");
                let projectUsers = [];
                for (var j = 0; j < users.length; j++) {
                    projectUsers.push(users[j].getElementsByTagName("login")[0].innerHTML);
                }
                this.projectUsers[parsedProjects[i].id] = projectUsers;
                projectUsers.sort()
            }
            this.isLoadingProjects = false;
            this.projectsList = parsedProjects;
            this.allProjects = parsedProjects;
        },
        async fetchSummary() {
            let params = new URLSearchParams(window.location.search);
            let year = params.get('year');
            let url = 'services/getHolidaySummary.php';
            if (year) {
                url += `?year=${year}`;
                this.year = year;
            }
            const res = await fetch(url, {
                method: 'GET',
                mode: 'same-origin',
                cache: 'no-cache',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                referrerPolicy: 'no-referrer',
            });
            const body = await res.json();
            this.isLoading = false;
            this.weeks = Object.keys(body.weeks);
            this.displayData = body.holidays;
            this.rawData = body.holidays;
            await this.fetchProjects();
        },
        // TODO: we should implement the autocomplete as a
        // reusable Single File Component, but before that we need to improve our
        // static files bundling.
        onSelectProject(projectIndex) {
            if (!this.projectsList[projectIndex]) return;
            const project = this.projectsList[projectIndex];
            this.searchProject = project.name;
            this.autocompleteIsActive = false;
            this.projectsList = this.allProjects;
            this.displayData = {};
            // Diplays only users from the project
            let users = this.projectUsers[project.id];
            users.sort()
            for (let i = 0; i < users.length; i++) {
                if (this.rawData[users[i]]) {
                    this.displayData[users[i]] = this.rawData[users[i]];
                }
            }
        },
        showOptions() {
            this.autocompleteIsActive = true;
        },
        hideOptions(event) {
            if (!event.relatedTarget?.classList.contains('autocompleteItemBtn')) {
                this.autocompleteIsActive = false;
            }
            this.activeProject = 0;
        },
        prevProject() {
            if (this.activeProject > 0) {
                this.activeProject--;
            } else {
                this.activeProject = this.projectsList.length - 1;
            }
            this.scrollAutocomplete();
        },
        nextProject() {
            if (this.activeProject < this.projectsList.length - 1) {
                this.activeProject++;
            } else {
                this.activeProject = 0;
            }
            this.scrollAutocomplete();
        },
        scrollAutocomplete() {
            if (!document.getElementsByClassName('autocompleteItemBtn')[this.activeProject]) return;
            const elementHeight = document.getElementsByClassName('autocompleteItemBtn')[this.activeProject].offsetHeight;
            const offSet = document.getElementsByClassName('autocompleteItemBtn')[this.activeProject].offsetTop + elementHeight;
            const clientHeight = document.getElementById('projectsDropdown').clientHeight;
            document.getElementById('projectsDropdown').scrollTop = offSet - clientHeight;
        },
        filterProject(event) {
            this.autocompleteIsActive = true;
            if (!event.target.value) {
                // reset list of users when no project is selected
                this.displayData = this.rawData;
                this.projectsList = this.allProjects;
            } else {
                this.projectsList = this.allProjects.filter(project => project.name.toLowerCase().includes(event.target.value.toLowerCase()));
            }
        },
    }
})
