'use strict';

(function () {

    module.exports = function (dictionariesMap) {
        const Locale = (function () {
            let locale = null;

            function parseLocale () {
                return $('html').prop('lang')
            }

            return {
                getLocale: function () {
                    if (null === locale) {
                        locale = parseLocale();
                    }

                    return locale;
                },
            }
        })();

        return (function (locale, dictionaries) {
            return {
                locale: Locale.getLocale(),
                resolve: function () {
                    const loc = locale.getLocale();

                    return (loc.length && dictionaries.hasOwnProperty(loc)) ? dictionaries[loc] : null;
                }
            }
        })(Locale, dictionariesMap);
    };

})();
