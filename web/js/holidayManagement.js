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
    rangeIdx = ranges.findIndex(range => range.coveredDates.includes(day));
    let daysToDelete = [];
    // Remove the overlaping range
    if (rangeIdx > -1) {
        daysToDelete = ranges[rangeIdx].coveredDates;
        ranges.splice(rangeIdx, 1);
        // Remove all days covered in that range
        days = days.filter(d => !daysToDelete.includes(d));
    };
    return { ranges, days };
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
            total: null,
            latestDelete: null,
            isEndOfRange: false,

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
        const fetchData = async () => {
            const currentYear = new Date().getFullYear();
            const url = `services/getHolidays.php?init=${currentYear}-01-01&end=${currentYear}-12-31`;
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

            const attributes = datesAndRanges.ranges.map(dt => ({
                highlight: {
                    start: { fillMode: 'outline' },
                    base: { fillMode: 'light' },
                    end: { fillMode: 'outline' },
                },
                dates: { start: new Date(dt.start + 'T00:00:00'), end: new Date(dt.end + 'T00:00:00') },
                coveredDates: datesAndRanges.dates.filter(d => d >= dt.start && d <= dt.end)
            }));

            this.ranges = attributes;
            this.days = datesAndRanges.dates;
            this.total = datesAndRanges.dates.length;
        };

        fetchData();
    },
    computed: {
        dates() {
            return this.days;
        },
        attributes() {
            return this.ranges;
        },
        totalHolidays() {
            return this.total;
        }
    },
    methods: {
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
            const currentYear = new Date().getFullYear();
            const url = ` services/updateHolidays.php?init=${currentYear}-01-01&end=${currentYear}-12-31`;

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
            const datesAndRanges = await res.json();
        }
    },
})
