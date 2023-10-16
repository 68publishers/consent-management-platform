'use strict';

const dayjs = require('dayjs');
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
};

module.exports = () => ({
    request: null,
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
        if (!this.$el.hasAttribute('data-request')) {
            throw new Error('Missing attribute "data-request".');
        }

        this.request = JSON.parse(this.$el.getAttribute('data-request')) || {};

        if ('object' !== typeof this.request || !('endpoint' in this.request)) {
            throw new Error('Missing key "endpoint" the the attribute "data-request".');
        }

        if (!('query' in this.request)) {
            throw new Error('Missing key "query" the the attribute "data-request".');
        }

        // create projects
        if (!this.$el.hasAttribute('data-projects')) {
            throw new Error('Missing attribute "data-projects".');
        }

        const projects = JSON.parse(this.$el.getAttribute('data-projects'));

        for (let i in projects) {
            this.addProject(projects[i]);
        }

        const currentEnvironmentsState = JSON.parse(window.localStorage.getItem('cmp_dashboard_environments') || '{}');

        for (let project of this.projects) {
            if (!(project.code in currentEnvironmentsState)) {
                continue;
            }

            let environmentFound = false;

            for (let environment of project.environments) {
                if (environment.code === currentEnvironmentsState[project.code]) {
                    environmentFound = true;
                    project.currentEnvironment = environment;

                    break;
                }
            }

            if (!environmentFound) {
                delete currentEnvironmentsState[project.code];
            }
        }

        window.localStorage.setItem('cmp_dashboard_environments', JSON.stringify(currentEnvironmentsState));

        // set up the default range
        const range = this.createRangeFromDate(-6, 0);
        this.setRange(range[0], range[1]);

        // watch range changes
        this.$watch('range', (() => {
            this.reloadAllProjectsData();
        }));
    },

    changeProjectEnvironment(code, environment) {
        for (let i in this.projects) {
            const project = this.projects[i];

            if (code !== project.code) {
                continue;
            }

            if (null !== project.currentEnvironment && project.currentEnvironment.code === environment.code) {
               continue;
            }

            project.currentEnvironment = environment;
            project.status = this.STATUS_LOADING();

            const currentEnvironmentsState = JSON.parse(window.localStorage.getItem('cmp_dashboard_environments') || '{}');

            if (null !== environment) {
                currentEnvironmentsState[project.code] = environment.code;
            } else if (project.code in currentEnvironmentsState) {
                delete currentEnvironmentsState[project.code];
            }

            window.localStorage.setItem('cmp_dashboard_environments', JSON.stringify(currentEnvironmentsState));

            this.loadProjectsData(this.range.startDate, this.range.endDate);

            break;
        }

        return true;
    },

    reloadAllProjectsData() {
        for (let i in this.projects) {
            this.projects[i].status = this.STATUS_LOADING();
        }

        this.loadProjectsData(this.range.startDate, this.range.endDate);
    },

    reloadProjectData(code, force = false) {
        for (let i in this.projects) {
            const project = this.projects[i];

            if (code !== project.code) {
                continue;
            }

            if (force || this.STATUS_LOADING() !== project.status) {
                project.status = this.STATUS_LOADING();
                this.loadProjectsData(this.range.startDate, this.range.endDate);
            }

            break;
        }
    },

    loadProjectsData(start, end) {
        const doLoad = (project) => {
            let query = [];

            for (let requestQueryName in this.request.query) {
                query.push(`${requestQueryName}=${encodeURIComponent(this.request.query[requestQueryName])}`);
            }

            query.push(`startDate=${encodeURIComponent(start.format('YYYY-MM-DD'))}`);
            query.push(`endDate=${encodeURIComponent(end.format('YYYY-MM-DD'))}`);
            query.push(`projects[]=${encodeURIComponent(project.code)}`);

            if (null !== project.currentEnvironment && null !== project.currentEnvironment.code) {
                query.push(`environment=${encodeURIComponent(project.currentEnvironment.code)}`);
            }

            const promise = fetch(this.request.endpoint + '?' + query.join('&'), {
                method: 'GET',
                credentials: 'omit',
            });

            promise.then(response => {
                return response.json();
            }).then(json => {
                if ('success' !== json.status) {
                    return Promise.reject(json);
                }

                const data = json.data;
                const projectData = data[project.code];

                if (!projectData) {
                    project.status = this.STATUS_MISSING();

                    return;
                }

                project.data = mergeObjects(project.data, projectData);
                project.status = this.STATUS_LOADED();
            }).catch((e) => {
                console.warn(e);
                project.status = this.STATUS_ERROR();
            });
        };

        for (let i in this.projects) {
            const project = this.projects[i];

            if (this.STATUS_LOADING() !== project.status || !project.visible) {
                continue;
            }

            project.status = this.STATUS_PROCESSING();
            doLoad(project);
        }
    },

    toggleProjectVisibility(code, visible) {
        let found = null;

        for (let project of this.projects) {
            if (project.code === code) {
                found = project;
                break;
            }
        }

        if (null === found) {
            return;
        }

        found.visible = visible;

        if (visible && this.STATUS_LOADING() === found.status) {
            this.reloadProjectData(found.code, true);
        }
    },

    addProject(data) {
        const def = {
            code: null,
            domain: null,
            name: null,
            color: null,
            fontColor: '#ffffff',
            environments: [],
            status: this.STATUS_LOADING(),
            data: this.createEmptyProjectData(),
            visible: false,
            currentEnvironment: null,
        };

        const project = mergeObjects(def, data);

        if (!project.hasOwnProperty('code') || !project.hasOwnProperty('name') || !project.hasOwnProperty('color')) {
            throw new Error('Invalid project data, the required keys are "code", "name" and "color".');
        }

        if (project.environments.length) {
            project.currentEnvironment = project.environments[0];
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
            cookieSuggestions: {
                enabled: false,
                missing: 0,
                unassociated: 0,
                problematic: 0,
                unproblematic: 0,
                ignored: 0,
            }
        };
    },

    setRange(start, end) {
        this.range = {
            startDate: start,
            endDate: end,
            daysDiff: Math.abs(start.diff(end, 'day')),
        };
    },

    moveToPreviousRange() {
        const range = this.createRangeFromDate((this.range.daysDiff * -1) - 1, -1, this.range.startDate);

        this.fp.setDate([range[0].toDate(), range[1].toDate()], true);
    },

    moveToNextRange() {
        const range = this.createRangeFromDate(1, this.range.daysDiff + 1, this.range.endDate);

        this.fp.setDate([range[0].toDate(), range[1].toDate()], true);
    },

    createToday() {
        return dayjs().hour(0).minute(0).second(0).millisecond(0);
    },

    createRangeFromDate(dayDiffStart, dayDiffEnd, date = undefined) {
        date = date ? date : this.createToday();
        const start = 0 === dayDiffStart ? date.clone() : (0 < dayDiffStart ? date.add(dayDiffStart, 'day') : date.subtract(dayDiffStart * -1, 'day'));
        const end = 0 === dayDiffEnd ? date.clone() : (0 < dayDiffEnd ? date.add(dayDiffEnd, 'day') : date.subtract(dayDiffEnd * -1, 'day'));

        return [start, end];
    },

    formatNumber(number) {
        try {
            return number.toLocaleString(document.documentElement.lang);
        } catch (e) {
            return number;
        }
    },

    STATUS_LOADING() {
        return 'loading';
    },

    STATUS_PROCESSING() {
        return 'processing';
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
                defaultDate: [this.range.startDate.toDate(), this.range.endDate.toDate()],
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

            const start = dayjs(dates[0]).hour(0).minute(0).second(0).millisecond(0);
            const end = dayjs(dates[1]).hour(0).minute(0).second(0).millisecond(0);

            this.setRange(start, end);
            this.rangeText = this.fp.input.value;
        },
    },
    rangeButton: {
        ['x-on:click']() {
            const dayDiffStart = this.$el.getAttribute('data-day-diff-start') || 0;
            const dayDiffEnd = this.$el.getAttribute('data-day-diff-end') || 0;

            const range = this.createRangeFromDate(parseInt(dayDiffStart), parseInt(dayDiffEnd));
            this.fp.setDate([range[0].toDate(), range[1].toDate()], true);
        },
        [':class']() {
            const dayDiffStart = this.$el.getAttribute('data-day-diff-start') || 0;
            const dayDiffEnd = this.$el.getAttribute('data-day-diff-end') || 0;
            const range = this.createRangeFromDate(parseInt(dayDiffStart), parseInt(dayDiffEnd));

            if (this.range.startDate.isSame(range[0], 'day') && this.range.endDate.isSame(range[1], 'day')) {
                return 'text-white bg-indigo-600 hover:bg-indigo-700';
            }

            return 'text-gray-700 hover:bg-gray-200';
        },
    },
});
