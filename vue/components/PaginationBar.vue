<template>
    <nav class="pagination" role="navigation" aria-label="pagination">
        <a class="pagination-previous" :disabled="currentPage <= 0" @click="changePage(currentPage - 1)">{{strings.previous_page}}</a>
        <a class="pagination-next" :disabled="currentPage >= pages - 1" @click="changePage(currentPage + 1)">{{strings.next_page}}</a>
        <ul class="pagination-list" v-if="pages.length < 10">
            <li v-for="page in Array(pages).keys()">
                <a v-if="currentPage === page" class="pagination-link is-current" :aria-label="'Goto page' + page" aria-current="page">{{page + 1}}</a>
                <a v-else @click="changePage(page)" class="pagination-link" :aria-label="'Goto page' + (page + 1)">{{page + 1}}</a>
            </li>
        </ul>
        <ul v-else class="pagination-list">
            <li v-show="currentPage > 1">
                <a @click="changePage(0)" class="pagination-link" aria-label="Goto page 1">1</a>
            </li>
            <li v-show="currentPage > 2">
                <span class="pagination-ellipsis">&hellip;</span>
            </li>
            <li v-show="currentPage > 0">
                <a @click="changePage(currentPage - 1)" class="pagination-link" :aria-label="'Goto page' + (currentPage) ">{{currentPage}}</a>
            </li>
            <li>
                <a @click="changePage(currentPage)" class="pagination-link is-current" :aria-label="'Goto page' + (currentPage + 1) ">{{currentPage + 1}}</a>
            </li>
            <li v-show="currentPage < pages - 1">
                <a @click="changePage(currentPage + 1)" class="pagination-link" :aria-label="'Goto page' + (currentPage + 2) ">{{currentPage + 2}}</a>
            </li>
            <li v-show="pages - currentPage > 3">
                <span class="pagination-ellipsis">&hellip;</span>
            </li>
            <li v-show="pages - currentPage > 2">
                <a @click="changePage(pages - 1)" class="pagination-link" :aria-label="'Goto page' + pages">{{pages}}</a>
            </li>
        </ul>
    </nav>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: "PaginationBar",
        computed: {
            ...mapState(['pages', 'filters', 'strings']),
            currentPage: {
                get() {
                    return this.$store.state.filters.page;
                },
                set(currentPage) {
                    this.$store.commit('setFilters', {page: currentPage});
                }
            }
        },
        data() {
            return {
                showNextButton: true,
                showPreviousButton: true
            }
        },
        methods: {
            changePage(page) {
                if (page >= 0 && page < this.pages) {
                    this.currentPage = page;
                    this.$store.dispatch('getResources', {});
                    document.getElementById('resources').scrollIntoView();
                }
            }
        }
    }
</script>

<style scoped lang="scss">

</style>
