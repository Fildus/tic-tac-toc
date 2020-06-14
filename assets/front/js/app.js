require('@fortawesome/fontawesome-free/css/all.min.css')
require('@fortawesome/fontawesome-free/js/all')
require('bootstrap')
import $ from 'jquery'
import './app-vue'

setTimeout(function () {
    const alerts = document.getElementsByClassName('alert')
    for (let alert of alerts) {
        $(alert).alert('close')
    }
}, 3000)
