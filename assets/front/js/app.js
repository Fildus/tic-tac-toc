require('@fortawesome/fontawesome-free/css/all.min.css')
require('@fortawesome/fontawesome-free/js/all')

import Vue from 'vue'
import Navbar from './components/Navbar'
import MyAccount from './components/MyAccount'

new Vue({
    el: '#vue-app',
    components: {Navbar, MyAccount}
})
