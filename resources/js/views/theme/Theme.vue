<template>
    <PLayout sectioned>
        <PCard sectioned class="assets-wrapper" v-if="!isLoading">
            <PButtonGroup class="action-bar">
                <router-link to="/">Back</router-link>
                <PButton destructive>Revert all</PButton>
                <PButton primary>Minify all</PButton>
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
                            {{row.name}}
                        </td>
                        <td class="Polaris-DataTable__Cell">
                            <PBadge size="small" status="critical" v-if="row.status == 0">Not minified</PBadge>
                            <PBadge size="small" status="success" v-if="row.status == 1">Minified</PBadge>
                            <PBadge size="small" status="warning" v-if="row.status == 2">Can't be minified</PBadge>
                        </td>
                        <td class="Polaris-DataTable__Cell">
                            <PButtonGroup>
                                <PButton primary :disabled="row.status == 1 || row.status == 2">Minify</PButton>
                                <PButton destructive :disabled="row.status == 0 || row.status == 2">Revert</PButton>
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
                sort: {value: 'name', direction: 'ascending'},
                headings: [
                    {
                        content: 'File name',
                        value: 'name',
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
                rows: [
                    {
                        name: 'File1.js',
                        status: 0
                    },
                    {
                        name: 'File2.js',
                        status: 1
                    },
                    {
                        name: 'File3.js',
                        status: 2
                    },
                ],
                previewLink: ""
            }
        },
        created: function() {
            this.isLoading = true;
            this.axios
                .get('/api/assets/' + this.$route.params.id)
                .then((res) => {
                    this.rows = res.data.assets;
                    this.previewLink = res.data.previewLink;
                    this.isLoading = false;
                });
        },
        methods: {
            handleMinify: function(value) {
                console.log(value);
            },
            handleRevert: function(value) {
                console.log(value);
            },
            handleSortChange: function(sort, direction) {
                this.sort = {value: sort,direction: direction};
            }
        }
    }
</script>
<style lang="scss" src="./index.scss" scoped></style>