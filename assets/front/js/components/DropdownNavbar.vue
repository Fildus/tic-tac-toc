<template>
    <li @mouseleave="showMenu = false">

        <a type="button"
           @click="showMenu = !showMenu"
           @mouseover="showMenu = true"
           :href="main.href !== 'undefined' ? main.href : ''"
           class="text-decoration-none"
           :class="showMenu ? 'text-white' : 'text-white-50'">{{main.html}}</a>

        <transition name="fade">
            <div v-show="showMenu"
                 class="bg-white position-absolute rounded shadow-sm"
                 :class="getDropdownDir()">
                <a v-for="s in subLinks"
                   class="dropdown-item bg-transparent app-link"
                   :href="s.href">{{ s.html }}</a>
            </div>
        </transition>
    </li>
</template>

<script>
    export default {
        props: ['main', 'subLinks', 'dropdownDir'],
        data() {
            return {
                showMenu: false,
            }
        },
        methods: {
            getDropdownDir() {
                if (this.dropdownDir === 'undefined') {
                    return ''
                }
                if (this.dropdownDir === 'rigth') {
                    return 'dropdown-menu-right mr-2'
                }
                if (this.dropdownDir === 'left') {
                    return 'dropdown-menu-left ml-2'
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .fade-enter-active, .fade-leave-active {
        transition: opacity .2s;
    }

    .fade-enter, .fade-leave-to {
        opacity: 0;
    }

    .app-link:hover {
        background: transparentize(#343a40, 0.8) !important;
    }
</style>
