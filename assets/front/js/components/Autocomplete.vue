<template>
    <div :class="showCard ? 'shadow border border-primary rounded p-2' : ''" style="transition: 300ms">
        <label :for="id">{{ label }}</label>
        <div class="form-control" @click="showCard = !showCard">

            <span v-for="tag in tags" class="badge badge-info m-1" @click.stop>{{tag}}
                <span role="button" style="cursor: pointer" @click="removeTag(tag)">&times;</span>
            </span>

            <input type="text" :id="id" :name="full_name" :value="tagsInString" hidden>
        </div>

        <div class="p-2 mt-2" v-show="showCard">
            <input type="text" class="form-control" @keypress="searchByTitle">

            <ul class="list-unstyled" v-if="results.length > 0">
                <li v-for="result in results"
                    class="badge m-1 mt-2 mb-2"
                    :class="tagIsSet(result) ? 'badge-secondary' : 'badge-success'"
                    @click="addOrRemoveTag(result)"
                    style="cursor: pointer">{{result}}
                </li>
            </ul>
            <p v-else class="text-center m-3"><i class="far fa-keyboard display-4"></i></p>
        </div>
    </div>
</template>

<script>
    import axios from "axios";

    export default {
        props: ['id', 'full_name', 'label', 'value', 'autocomplete_url'],
        data() {
            return {
                tags: [],
                results: [],
                showCard: false
            }
        },
        mounted() {
            if (this.value.length > 0) {
                this.tags = this.value.split(',')
            }
        },
        computed: {
            tagsInString() {
                return this.tags.join(',')
            }
        },
        methods: {
            searchByTitle(input) {
                if (input.code === 'Enter'){
                    input.preventDefault()
                    const value = this.results[0]
                    this.addOrRemoveTag(value)
                }else {
                    const value = input.target.value
                    this.fetch(value)
                }
            },
            fetch(value) {
                if (value !== '') {
                    axios.get(this.autocomplete_url, {method: "GET", params: {title: value}})
                        .then(response => this.results = response.data ?? [])
                        .catch(error => console.log(error))
                } else {
                    this.results = []
                }
            },
            tagIsSet(tagName) {
                return this.tags.includes(tagName)
            },
            addOrRemoveTag(tagName) {
                if (!this.tags.includes(tagName)) {
                    this.tags = [...this.tags, tagName]
                } else {
                    this.tags = this.tags.filter(t => t !== tagName)
                }
            },
            removeTag(tagName) {
                this.tags = this.tags.filter(t => t !== tagName)
            }
        }
    }
</script>
