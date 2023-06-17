const lang = document.querySelector('html').getAttribute('lang')

const translations = {}

const requireModules = require.context('./translations', false, /\.js$/)

requireModules.keys().forEach(modulePath => {
    const key = modulePath.replace(/(^.\/)|(.js$)/g, '')

    translations[key] = requireModules(modulePath).default
})

const toCapitalize = function() { 
    return this.split(' ').map(word => word[0].toUpperCase() + word.substr(1)).join(' ')
}

const toUpperFirst = function() {
    return this[0].toUpperCase() + this.substr(1)
}

const t = text => {
    const translated = translations[lang][text] || text

    translated.__proto__.toCapitalize = toCapitalize
    translated.__proto__.toUpperFirst = toUpperFirst

    return translated
}

export default t