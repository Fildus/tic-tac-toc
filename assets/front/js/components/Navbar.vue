<template>
    <nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
        <a :href="parsedConfig.home.href" class="navbar-brand">{{ parsedConfig.home.html }}</a>
        <button @click="toggle = !toggle" class="navbar-toggler" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div v-show="toggle" class="navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <DropdownNavbar :main="parsedConfig.dropdown.projects.main"
                                :sub-links="parsedConfig.dropdown.projects.subLinks"/>
            </ul>
            <ul class="navbar-nav">
                <DropdownNavbar :main="parsedConfig.dropdown.connection.main"
                                :subLinks="parsedConfig.dropdown.connection.subLinks"
                                dropdown-dir="rigth"/>
            </ul>
        </div>
    </nav>
</template>

<script>
    import DropdownNavbar from "@front/components/DropdownNavbar";

    export default {
        props: ['config'],
        components: {DropdownNavbar},
        data() {
            return {
                toggle: false,
                parsedConfig: null
            }
        },
        created() {
            this.parsedConfig = JSON.parse(this.config)
            if (window.innerWidth > 991) {
                this.toggle = true
            }
        }
    }
</script>


