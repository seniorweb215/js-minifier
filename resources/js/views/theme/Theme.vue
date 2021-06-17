<template>
    <PLayout sectioned>
        <PCard sectioned class="assets-wrapper" v-if="!isLoading">
            <PButtonGroup class="action-bar">
                <router-link to="/">Back</router-link>
                <PButton destructive @click="handleRevertAll">Revert all</PButton>
                <PButton primary @click="handleMinifyAll">Minify all</PButton>
                <PLink :url="previewLink" external>Preview</PLink>
            </PButtonGroup>
            <PDataTable
                :hasPagination="false"
                :hasFilter="false"
                :headings="headings"
                :ids="[1,2,3]"
                @sort-changed="handleSortChange"
            >
                <template slot="body">
                    <tr
                        class="Polaris-DataTable__TableRow"
                        v-for="(row, rowIndex) in rows"
                        :key="`row-${rowIndex}`">
                        <td class="Polaris-DataTable__Cell">
                            {{row.asset_key}}
                        </td>
                        <td class="Polaris-DataTable__Cell">
                            <PBadge size="small" status="critical" v-if="row.status == 0">Not minified</PBadge>
                            <PBadge size="small" status="success" v-if="row.status == 1">Minified</PBadge>
                            <PBadge size="small" status="warning" v-if="row.status == 2">Can't be minified</PBadge>
                        </td>
                        <td class="Polaris-DataTable__Cell">
                            <PButtonGroup>
                                <PButton primary :disabled="row.status == 1 || row.status == 2 || isSaving" @click="handleMinify(row.id)">Minify</PButton>
                                <PButton destructive :disabled="row.status == 0 || row.status == 2 || isSaving" @click="handleRevert(row.id)">Revert</PButton>
                            </PButtonGroup>
                        </td>
                    </tr>
                </template>
            </PDataTable>
        </PCard>
    </PLayout>
</template>
<script>
    export default {
        name: "Theme",
        data: function() {
            return {
                isLoading: false,
                isSaving: false,
                sort: {value: 'asset_key', direction: 'ascending'},
                headings: [
                    {
                        content: 'File name',
                        value: 'asset_key',
                        type: 'text',
                        sortable: true,
                        width: '40%'
                    },
                    {
                        content: 'Status',
                        value: 'status',
                        type: 'string',
                        sortable: false
                    },
                    {
                        content: 'Actions',
                        value: '',
                        type: 'string',
                        sortable: false
                    }
                ],
                rows: [],
                previewLink: ""
            }
        },
        created: function() {
            this.isLoading = true;
            this.axios
                .get('/api/assets/' + this.$route.params.id)
                .then((res) => {
                    this.rows = res.data.rows;
                    this.previewLink = res.data.previewLink;
                    this.isLoading = false;
                });
        },
        methods: {
            handleMinify: function(id) {
                this.isSaving = true;
                this.axios
                    .post('/api/assets/minify/' + id)
                    .then((res) => {
                        this.isSaving = false;
                        this.rows = res.data.rows;
                        this.previewLink = res.data.previewLink;
                    });
            },
            handleRevert: function(id) {
                this.isSaving = true;
                this.axios
                    .post('/api/assets/revert/' + id)
                    .then((res) => {
                        this.isSaving = false;
                        this.rows = res.data.rows;
                        this.previewLink = res.data.previewLink;
                    });
            },
            handleMinifyAll() {
                this.axios
                    .post('/api/assets/minifyAll/' + this.$route.params.id)
                    .then((res) => {
                        this.isSaving = false;
                        this.rows = res.data.rows;
                        this.previewLink = res.data.previewLink;
                    });
            },
            handleRevertAll() {
                this.axios
                    .post('/api/assets/revertAll/' + this.$route.params.id)
                    .then((res) => {
                        this.isSaving = false;
                        this.rows = res.data.rows;
                        this.previewLink = res.data.previewLink;
                    });
            },
            handleSortChange: function(sort, direction) {
                this.sort = {value: sort,direction: direction};
            }
        }
    }
</script>
<style lang="scss" src="./index.scss" scoped></style>