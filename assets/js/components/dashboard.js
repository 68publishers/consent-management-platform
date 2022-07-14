'use strict';

const flatpickr = require('flatpickr').default;
const dictionary = require('../dictionary')({
    'cs': require('flatpickr/dist/l10n/cs').default.cs,
});

const mergeObjects = (target, source) => {
    for (const key of Object.keys(source)) {
        if (source[key] instanceof Object) Object.assign(source[key], mergeObjects(target[key], source[key]));
    }

    Object.assign(target || {}, source);

    return target;
}

const formatDate = date => {
    return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds()))
        .toISOString()
        .slice(0, 10);
}

module.exports = () => ({
    endpoint: null,
    projects: [],
    range: {
        startDate: null,
        endDate: null,
        daysDiff: 0,
    },
    rangeText: '',
    fp: null,

    init() {
        // setup endpoint
        if (!this.$el.hasAttribute('data-endpoint')) {
            throw new Error('Missing attribute "data-endpoint".');
        }

        this.endpoint = this.$el.getAttribute('data-endpoint');

        // create projects
        if (!this.$el.hasAttribute('data-projects')) {
            throw new Error('Missing attribute "data-projects".');
        }

        const projects = JSON.parse(this.$el.getAttribute('data-projects'));

        for (let i in projects) {
            this.addProject(projects[i]);
        }

        // setup the default range
        const range = this.createRangeFromToday(6, 0);
        this.setRange(range[0], range[1]);

        // watch range changes
        this.$watch('range', (() => {
            this.reloadAllProjectsData();
        }));

        // load projects
        this.$nextTick(() => {
            this.reloadAllProjectsData();
        });
    },

    reloadAllProjectsData() {
        for (let i in this.projects) {
            this.projects[i].status = this.STATUS_LOADING();
        }

        this.loadProjectsData(this.range.startDate, this.range.endDate);
    },

    reloadProjectData(code) {
        for (let i in this.projects) {
            const project = this.projects[i];

            if (code !== project.code) {
                continue;
            }

            if (this.STATUS_LOADING() !== project.status) {
                project.status = this.STATUS_LOADING();
                this.loadProjectsData(this.range.startDate, this.range.endDate);
            }

            break;
        }
    },

    loadProjectsData(start, end) {
        const codes = [];

        for (let i in this.projects) {
            const project = this.projects[i];

            if (this.STATUS_LOADING() !== project.status) {
                continue;
            }

            codes.push(project.code);
        }

        let query = `?locale=${document.documentElement.lang}&startDate=${formatDate(start)}&endDate=${formatDate(end)}`;

        for (let i in codes) {
            query += `&projects[]=${codes[i]}`;
        }

        const promise = fetch(this.endpoint + query, {
            method: 'GET'
        });

        promise.then(response => {
            return response.json();
        }).then(json => {
            if ('success' !== json.status) {
                return Promise.reject(json);
            }

            const data = json.data;

            for (let i in this.projects) {
                const project = this.projects[i];

                if (-1 === codes.indexOf(project.code)) {
                    continue;
                }

                const projectData = data[project.code];

                if (!projectData) {
                    project.status = this.STATUS_MISSING();

                    continue;
                }

                project.data = mergeObjects(project.data, projectData);
                project.status = this.STATUS_LOADED();
            }
        }).catch((e) => {
            console.warn(e);

            for (let i in this.projects) {
                if (-1 !== codes.indexOf(this.projects[i].code)) {
                    this.projects[i].status = this.STATUS_ERROR();
                }
            }
        });
    },

    addProject(data) {
        const def = {
            code: null,
            name: null,
            color: null,
            status: this.STATUS_LOADING(),
            data: this.createEmptyProjectData(),
        };

        const project = mergeObjects(def, data);

        if (!project.hasOwnProperty('code') || !project.hasOwnProperty('name') || !project.hasOwnProperty('color')) {
            throw new Error('Invalid project data, the required keys are "code", "name" and "color".');
        }

        this.projects.push(project);
    },

    createEmptyProjectData() {
        return {
            allConsents: {
                value: null,
                percentageDiff: null,
            },
            uniqueConsents: {
                value: null,
                percentageDiff: null,
            },
            allPositive: {
                value: null,
                percentageDiff: null,
            },
            uniquePositive: {
                value: null,
                percentageDiff: null,
            },
            lastConsent: {
                value: null,
                formattedValue: null,
                text: null,
            },
            providers: {
                value: null,
            },
            cookies: {
                commonValue: null,
                privateValue: null,
            },
        };
    },

    setRange(start, end) {
        this.range = {
            startDate: start,
            endDate: end,
            daysDiff: Math.ceil((end.getTime() - start.getTime()) / (1000 * 3600 * 24)),
        };
    },

    createToday() {
        let today = Date.now();
        today = new Date(today);
        today.setHours(0, 0, 0, 0);

        return today;
    },

    createRangeFromToday(dayDiffStart, dayDiffEnd) {
        const today = this.createToday();
        const start = 0 === dayDiffStart ? today : new Date(today.getTime() - (1000 * 60 * 60 * 24 * dayDiffStart));
        const end = 0 === dayDiffEnd ? today : new Date(today.getTime() - (1000 * 60 * 60 * 24 * dayDiffEnd));

        return [start, end];
    },

    STATUS_LOADING() {
        return 'loading';
    },

    STATUS_LOADED() {
        return 'loaded';
    },

    STATUS_MISSING() {
        return 'missing';
    },

    STATUS_ERROR() {
        return 'error';
    },

    datepicker: {
        ['x-ref']: 'flatpickr',
        ['x-init']() {
            const flatpickrOptions = {
                mode: 'range',
                dateFormat: 'j.n.Y',
                disableMobile: true,
                defaultDate: [this.range.startDate, this.range.endDate],
                inline: true,
            };

            const translations = dictionary.resolve();

            if (null !== translations) {
                flatpickrOptions['locale'] = translations;
            }

            this.fp = flatpickr(this.$el, flatpickrOptions);
            this.rangeText = this.fp.input.value;
        },
        ['x-on:change']() {
            if (!this.fp) {
                return;
            }

            const dates = this.fp.selectedDates;

            if (2 > dates.length) {
                return;
            }

            const start = dates[0];
            const end = dates[1];

            start.setHours(0, 0, 0, 0);
            end.setHours(0, 0, 0, 0);

            this.setRange(start, end);
            this.rangeText = this.fp.input.value;
        },
    },
    rangeButton: {
        ['x-on:click']() {
            const dayDiffStart = this.$el.getAttribute('data-day-diff-start') || 0;
            const dayDiffEnd = this.$el.getAttribute('data-day-diff-end') || 0;

            const range = this.createRangeFromToday(parseInt(dayDiffStart), parseInt(dayDiffEnd));
            this.fp.setDate(range, true);
        },
        [':class']() {
            const dayDiffStart = this.$el.getAttribute('data-day-diff-start') || 0;
            const dayDiffEnd = this.$el.getAttribute('data-day-diff-end') || 0;
            const range = this.createRangeFromToday(parseInt(dayDiffStart), parseInt(dayDiffEnd));

            if (this.range.startDate.getTime() === range[0].getTime() && this.range.endDate.getTime() === range[1].getTime()) {
                return 'text-white bg-indigo-600 hover:bg-indigo-700';
            }

            return 'text-indigo-700 bg-indigo-100 hover:bg-indigo-200';
        },
    },
});
