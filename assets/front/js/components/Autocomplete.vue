<template>
    <div>
        <label :for="id">{{ label }}</label>
        <input type="text"
               :id="id"
               :name="full_name"
               :required="required ? 'required' : null"
               autocomplete="off"
               class="form-control"
                :value="value">

        <ul v-if="results.length > 0"
            class="bg-white shadow pl-2 pr-2 list-unstyled">

            <li v-for="result in results"
                class="cursor-pointer">
                {{ result }}
            </li>

        </ul>
    </div>
</template>

<style lang="scss">
    .cursor-pointer {
        cursor: pointer;

        &:hover {
            background: #c9c9c9;
        }
    }
</style>

<script>
    import axios from "axios"

    export default {
        props: ['id', 'full_name', 'label', 'required', 'autocomplete_url'],
        data() {
            return {
                results: [],
                value: ''
            }
        },
        mounted() {
            axios
                .get(this.autocomplete_url, {
                    method: "GET",
                    params: {
                        title: this.value
                    }
                })
                .then(response => this.results = response.data ?? [])
        }
    }
</script>
