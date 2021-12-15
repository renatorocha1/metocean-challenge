<template>
  <div class="card mt-3">
    <div class="card-header">{{ title }}</div>
    <div class="card-body">
      <slot name="body"></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      default: "Title",
      type: String,
    },
  },
}
</script>

<style lang="scss" scoped>
.card {
  padding: 0 !important;
  background-color: #13132c;
  color: #fff;
}
</style>
