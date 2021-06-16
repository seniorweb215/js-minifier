<template>
    <PLayout sectioned>
        <!-- <PSpinner accessibilityLabel="Loading" v-if="isLoading" /> -->
        <PCard sectioned class="themes-wrapper" v-if="!isLoading">
            <PFormLayout>
                <POptionList
                    :options="themes"
                    :selected="[]"
                    @change="handleSelect"
                >
                </POptionList>
            </PFormLayout>
        </PCard>
    </PLayout>
</template>

<script>
    export default {
        name: "Dashboard",
        data: function() {
            return {
                isLoading: false,
                themes: []
            }
        },
        created: function() {
            this.isLoading = true;
            this.axios
                .get('/api/themes')
                .then((res) => {
                    this.themes = res.data.themes;
                    this.isLoading = false;
                });
        },
        methods: {
            handleSelect: function(value) {
                this.$router.push('/theme/' + value);
            }
        }
    }
</script>
<style lang="scss" src="./index.scss" scoped></style>