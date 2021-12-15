<template>
  <div class="container">
    <div class="row mt-5">
      <div class="col text-center">
        <h1>MetOcean Challenge</h1>
        <p>
          This is a <strong>sample page</strong> to view an
          <strong>example of reported data</strong> which contain informations
          about meteorology and oceanography provided by MetOcean.
        </p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <SearchDataComponent @onChange="loadData" />
        <CardLabelComponent />
      </div>
      <div class="col-md-6">
        <PreLoaderComponent v-if="isItLoading" />
        <ChartComponent v-else :data="data" :viewMode="viewMode" />
      </div>
    </div>
  </div>
</template>

<script>
import SearchDataComponent from "../components/SearchDataComponent"
import PreLoaderComponent from "../components/PreLoaderComponent"
import CardLabelComponent from "../components/CardLabelComponent"
import ChartComponent from "../components/ChartComponent"
export default {
  components: {
    SearchDataComponent,
    PreLoaderComponent,
    CardLabelComponent,
    ChartComponent,
  },
  created() {
    this.loadData()
  },
  methods: {
    loadData(date = "2014-02-10") {
      this.$store.dispatch("loadData", date)
    },
  },
  computed: {
    data() {
      return this.$store.state.data
    },
    viewMode() {
      return this.$store.state.viewMode
    },
    isItLoading() {
      return this.$store.state.loading
    },
  },
}
</script>
