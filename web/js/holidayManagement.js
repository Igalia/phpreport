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

function formatDate(date) {
    // Receives a date object and returns a string in the format YYYY-MM-DD.
    // The subtract below is to discard timezone.
    return new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
        .toISOString()
        .split("T")[0];
}

function deleteRangeAndDays(ranges, day, days) {
    if (ranges.length == 0 || days.length == 0)
        return { ranges, days };
    let updatedRanges = ranges;
    let updatedDays = days;
    rangeIdx = ranges.findIndex(range => range.coveredDates?.includes(day));
    let daysToDelete = [];
    // Remove the overlaping range
    if (rangeIdx > -1) {
        daysToDelete = updatedRanges[rangeIdx].coveredDates;
        updatedRanges.splice(rangeIdx, 1);
        // Remove all days covered in that range, including partial leaves
        updatedDays = updatedDays.filter(d => !daysToDelete.includes(d));
        for (let index = 0; index < daysToDelete.length; index++) {
            let res = this.deleteRangeAndDays(updatedRanges, daysToDelete[index], updatedDays);
            updatedRanges = res.ranges;
            updatedDays = res.days;
        }
    };
    return { ranges: updatedRanges, days: updatedDays };
}

function indexOfRange(attrs, date) {
    return attrs.findIndex(attr => attr.dates.findIndex(dt => formatDate(dt.end) == formatDate(date)) >= 0);
}

function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    return result;
}

var app = new Vue({
    el: '#holidaysApp',
    data() {
        return {
            days: [],
            range: {},
            ranges: [],
            scheduledHolidays: null,
            availableHolidays: null,
            enjoyedHolidays: null,
            daysByWeek: null,
            latestDelete: null,
            isEndOfRange: false,
            init: new Date(new Date().getFullYear(), 0, 1),
            end: new Date(new Date().getFullYear(), 11, 31),
            pendingHolidays: null,
            serverMessages: [],

            // Clean selected range styles to avoid confusion when
            // removing dates
            selectAttribute: {
                highlight: {
                    start: {
                        style: {
                            backgroundColor: 'transparent',
                        },
                    },
                    base: {
                        style: {
                            backgroundColor: 'transparent',
                        }
                    },
                    end: {
                        style: {
                            backgroundColor: 'transparent',
                        }
                    },
                },
            },
        };
    },
    created() {
        const fetchHolidays = async () => {
            const url = `services/getHolidays.php?init=${formatDate(this.init)}&end=${formatDate(this.end)}`;
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
            const datesAndRanges = await res.json();
            this.updateDates(datesAndRanges);
        }
        fetchHolidays();
        this.fetchSummary();
    },
    computed: {
        attributes() {
            return this.ranges;
        },
    },
    methods: {
        async fetchSummary() {
            const url = `services/getPersonalSummaryByDateService.php?date=${formatDate(this.end)}`;
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
            this.pendingHolidays = xmlDoc.getElementsByTagName("pending_holidays")[0].childNodes[0].nodeValue;
            this.scheduledHolidays = xmlDoc.getElementsByTagName("scheduled_holidays")[0].childNodes[0].nodeValue;
            this.enjoyedHolidays = xmlDoc.getElementsByTagName("enjoyed_holidays")[0].childNodes[0].nodeValue;
            this.availableHolidays = xmlDoc.getElementsByTagName("available_holidays")[0].childNodes[0].nodeValue;
        },
        updateDates(datesAndRanges) {
            const attributes = datesAndRanges.ranges.map(dt => ({
                highlight: {
                    start: { fillMode: 'outline' },
                    base: { fillMode: 'light' },
                    end: { fillMode: 'outline' },
                },
                dates: { start: new Date(dt.start + 'T00:00:00'), end: new Date(dt.end + 'T00:00:00') },
                coveredDates: Object.keys(datesAndRanges.dates).filter(d => d >= dt.start && d <= dt.end)
            }));
            // Add today
            attributes.push({
                bar: 'orange',
                dates: { start: new Date(), end: new Date() },
                popover: {
                    label: "Today"
                },
            });
            // Add partial leaves
            Object.keys(datesAndRanges.dates).forEach(d => {
                if (datesAndRanges.dates[d].isPartialLeave) {
                    attributes.push({
                        highlight: {
                            color: 'orange',
                            fillMode: 'light',
                        },
                        dates: new Date(d + 'T00:00:00'),
                        popover: {
                            label: "Contains partial leave"
                        },
                        coveredDates: [d],
                    })
                }
            });

            this.ranges = attributes;
            this.days = Object.keys(datesAndRanges.dates);
            this.daysByWeek = Object.keys(datesAndRanges.weeks).sort().map((week, idx) => ({ weekNumber: week, total: datesAndRanges.weeks[week] }));
        },
        onDayClick(day) {
            let endDay = day.date;

            // Check if the selected day is already in the list, if it is, it means the user
            // is edditing or removing some range, so delete respective range and dates
            if (this.days.findIndex(d => d === day.id) >= 0) {
                const { ranges, days } = deleteRangeAndDays(this.ranges, day.id, this.days);
                this.ranges = ranges;
                this.days = days;

                // We need to keep track of the latest deleted day in case the user is removing
                // a single date range
                this.latestDelete = day.id;
            }

            // The data structure of v-calendar is a bit confusing and inconsistent, so we need to
            // find out wich of the ranges corresponds to the current one to make sure we grab the
            // correct start date
            const idx = indexOfRange(day.attributes, day.date);

            if (this.isEndOfRange) {
                const startDay = day.attributes[idx].dates[0].start;
                const diff = Math.floor((day.date - startDay) / 86400000);
                const rangeDays = [];

                // If the latest deleted day is the same that was clicked, it means
                // it was just deleted so we don't want to add it again
                if ((diff != 0) || (this.latestDelete != day.id)) {
                    for (let i = 0; i <= diff; i++) {
                        const currentDay = formatDate(addDays(startDay, i));

                        // Remove any overlaping range in the middle
                        if (this.days.findIndex(d => d === currentDay) >= 0) {
                            const { ranges, days } = deleteRangeAndDays(this.ranges, currentDay, this.days);
                            this.ranges = ranges;
                            this.days = days;
                        }

                        rangeDays.push(currentDay);
                        this.days.push(currentDay);
                    }
                    this.ranges.push({
                        highlight: {
                            start: { fillMode: 'outline' },
                            base: { fillMode: 'light' },
                            end: { fillMode: 'outline' },
                        },
                        dates: { start: startDay, end: endDay },
                        coveredDates: rangeDays
                    });
                }
                this.latestDelete = null;
                this.range = null;
                this.value = null;
            }

            // Next click will be the opposite of the current state
            this.isEndOfRange = !this.isEndOfRange;
        },
        onSaveClick: async function () {
            const url = `services/updateHolidays.php?init=${formatDate(this.init)}&end=${formatDate(this.end)}`;
            const res = await fetch(url, {
                method: 'POST',
                mode: 'same-origin',
                cache: 'no-cache',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                referrerPolicy: 'no-referrer',
                body: JSON.stringify(this.days)
            });
            const body = await res.json();
            if ("error" in body) {
                this.serverMessages.push({ classes: "message error", text: `Error: ${body["error"]}` });
            } else {
                this.updateDates(body["datesAndRanges"]);
                if (body["resultCreation"] && body["resultCreation"]["failed"] && body["resultCreation"]["failed"].length > 0) {
                    this.serverMessages.push({
                        classes: "message error",
                        text: `These dates couldn't be created: ${body["resultCreation"]["failed"].join(", ")}`
                    });
                }
                if (body["resultDeletion"] && body["resultDeletion"]["failed"] && body["resultDeletion"]["failed"].length > 0) {
                    this.serverMessages.push({
                        classes: "message error",
                        text: `These dates couldn't be removed: ${body["resultDeletion"]["failed"].join(", ")}`
                    });
                }
                if (this.serverMessages.length === 0) {
                    this.serverMessages.push({ classes: "message success", text: "Holidays were updated." });
                }
            };
            this.fetchSummary();
            this.$emit('flush-message')
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
