<template>
    <li @mouseleave="showMenu = false">

        <a type="button"
           @click="showMenu = !showMenu"
           @mouseover="showMenu = true"
           :href="main.href !== 'undefined' ? main.href : ''"
           class="text-decoration-none text-dark text-black-50"
           :class="showMenu ? 'text-shadow-lg' : ''">{{main.html}}</a>

        <transition name="fade">
            <div v-show="showMenu"
                 class="bg-white position-absolute bg-transparent position-left"
                 :class="getDropdownDir()">
                <div class="p-1 bg-transparent"></div>
                <div class="bg-white border rounded shadow-lg">
                    <a v-for="s in subLinks"
                       class="dropdown-item text-black-50 app-link"
                       :href="s.href">{{ s.html }}</a>
                </div>
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
        transition: opacity 0.5s;
    }

    .fade-enter, .fade-leave-to {
        opacity: 0;
    }

    .app-link:hover {
        color: #171717 !important;
        background: whitesmoke !important;
    }

    .position-left {
        @media (max-width: 991px) {
            left: 0;
            right: auto;
        }
    }
</style>
