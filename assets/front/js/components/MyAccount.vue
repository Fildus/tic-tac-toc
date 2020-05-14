<template>
    <div class="card shadow">
        <div class="card-header">
            <h5 class="card-title">{{ cardTitle }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-4">
                    <div class="list-group" role="tablist">
                        <a v-for="l in list"
                           href="#"
                           class="list-group-item list-group-item-action"
                           :class="l[0] === active ? 'active' : ''"
                           @click="active = l[0]">
                            <slot :name="l[0]"></slot>
                        </a>
                    </div>
                </div>
                <div class="col-8">
                    <div class="tab-content h-100">
                        <div v-for="l in list"
                             class="tab-pane fade show h-100"
                             :class="l[0] === active ? 'active' : ''">
                            <slot :name="l[1]"></slot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['cardTitle', 'list'],
        data() {
            return {
                active: this.list[0][0]
            }
        },
        mounted() {
            console.log(this.$slots)
        }
    }
</script>
