// grunt exec:mykhailok_luma_en_US && grunt less:mykhailok_luma_en_US && grunt watch
module.exports = {
    mykhailok_luma_uk_UA: {
        area: 'frontend',
        name: 'Mykhailok/luma',
        locale: 'uk_UA',
        files: [
            'css/styles-m',
            'css/styles-l'
        ],
        dsl: 'less'
    },
    mykhailok_luma_ru_RU: {
        area: 'frontend',
        name: 'Mykhailok/luma',
        locale: 'ru_RU',
        files: [
            'css/styles-m',
            'css/styles-l'
        ],
        dsl: 'less'
    },
    mykhailok_luma_en_US: {
        area: 'frontend',
        name: 'Mykhailok/luma',
        locale: 'en_US',
        files: [
            'css/styles-m',
            'css/styles-l'
        ],
        dsl: 'less'
    }
};