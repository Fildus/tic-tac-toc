import Autocomplete from '../components/Autocomplete.vue'
import Vue from "vue"
import VueAxios from "vue-axios"
import axios from "axios"

Vue.use(VueAxios, axios)

for (const AutocompleteElt of document.getElementsByClassName('vue-autocomplete')) {
    new Vue({components: {Autocomplete}}).$mount(AutocompleteElt)
}
