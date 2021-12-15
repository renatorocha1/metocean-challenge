<template>
  <CardComponent title="Filter data:">
    <template slot="body">
      <div class="row">
        <div class="col-12">
          <label for="input-date">Date:</label>
          <input
            type="date"
            class="form-control"
            id="input-date"
            value="2014-02-10"
            @change="changeDate"
          />
        </div>
        <div class="col-12 mt-3">
          <label for="select-label">Type:</label>
          <select
            id="select-label"
            class="form-select"
            aria-label="Filter by type of data"
            @change="changeLabel"
          >
            <option value="all" selected>All</option>
            <option
              v-for="(item, index) in labels"
              :key="index"
              :value="item.slug"
            >
              {{ item.slug }}
            </option>
          </select>
        </div>
      </div>
    </template>
  </CardComponent>
</template>

<script>
import CardComponent from "./CardComponent"
export default {
  components: {
    CardComponent,
  },
  data() {
    return {
      form: {
        date: null,
        view: 1,
      },
    }
  },
  methods: {
    changeDate(e) {
      if (!e.target.value) return
      this.$emit("onChange", e.target.value)
    },
    changeLabel(e) {
      const type = e.target.value
      this.$store.dispatch("setViewMode", type)
    },
  },
  computed: {
    labels() {
      return this.$store.state.labels
    },
  },
}
</script>
