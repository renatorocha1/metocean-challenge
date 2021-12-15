<template>
  <CardComponent v-if="labels" title="Labels:">
    <template v-slot:body>
      <ul class="list-group list-group-flush">
        <li
          class="list-group-item"
          v-for="(item, index) in labels"
          :key="index"
        >
          <div
            class="list-group-item-color"
            :style="`background: ${item.color}`"
          ></div>
          [{{ item.slug }}] - {{ item.text }}
        </li>
      </ul>
    </template>
  </CardComponent>
</template>

<script>
import CardComponent from "../components/CardComponent"
export default {
  components: {
    CardComponent,
  },
  computed: {
    labels() {
      return this.$store.state.labels
    },
  },
}
</script>

<style lang="scss" scoped>
.list-group {
  max-height: 350px;
  overflow: auto;
}
.list-group-item {
  display: flex;
  align-items: center;
  padding: 0.5rem 0;
  background-color: #13132c;
}
.list-group-item-color {
  width: 15px;
  height: 15px;
  margin-right: 10px;
}
</style>
