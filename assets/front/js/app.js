require('@fortawesome/fontawesome-free/css/all.min.css')
require('@fortawesome/fontawesome-free/js/all')
require('bootstrap')
import $ from 'jquery'
import Vue from 'vue'
import Navbar from './components/Navbar'

new Vue({
    el: '#vue-app',
    components: {Navbar}
})

setTimeout(function () {
    const alerts = document.getElementsByClassName('alert')
    for (let alert of alerts) {
        $(alert).alert('close')
    }
}, 3000)
