import DropdownNavbar from './components/DropdownNavbar'
import Autocomplete from './components/Autocomplete'
import Vue from "vue"
import VueAxios from "vue-axios"
import axios from "axios"

Vue.use(VueAxios, axios)

new Vue({
    el: '#app-vue',
    components: {DropdownNavbar, Autocomplete}
})

