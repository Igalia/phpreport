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

var app = new Vue({
    el: '#holidaysApp',
    data() {
        return {
            days: [],
            range: {},
            ranges: [],
            total: null,

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
            }));

            this.ranges = attributes;
            this.days = datesAndRanges.ranges;
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
    }
})
