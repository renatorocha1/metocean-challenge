<template>
  <CardComponent title="View">
    <template slot="body">
      <div id="chart"></div>
    </template>
  </CardComponent>
</template>

<script>
import * as d3 from "d3"
import { randomColor, parseDataToGroups } from "../utils"
import CardComponent from "./CardComponent"

export default {
  props: {
    data: {
      default: [],
    },
    viewMode: {
      default: "all",
    },
  },
  components: {
    CardComponent,
  },
  data() {
    return {
      SVG: null,
      scatter: null,
      x: null,
      y: null,
      xAxis: null,
      yAxis: null,
    }
  },
  mounted() {
    this.drawChart()
  },
  watch: {
    viewMode() {
      d3.select("svg").remove()
      this.drawChart()
    },
  },
  methods: {
    async drawChart() {
      if (!this.data?.items) return
      // set the dimensions and margins of the graph
      const margin = { top: 30, right: 30, bottom: 30, left: 50 }
      const width = 460 - margin.left - margin.right
      const height = 400 - margin.top - margin.bottom
      const items = this.filterData(this.data, this.viewMode)

      // append the svg object to the body of the page
      this.SVG = d3
        .select("#chart")
        .append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", `translate(${margin.left},${margin.top})`)

      // add the x Axis
      this.x = d3
        .scaleTime()
        .domain(this.parseRangeDate(items))
        .range([0, width])
      this.xAxis = this.SVG.append("g")
        .attr("transform", `translate(0, ${height})`)
        .call(d3.axisBottom(this.x))

      // add the y Axis
      this.y = d3
        .scaleLinear()
        .domain(this.parseRangeValue(items))
        .range([height, 0])
      this.yAxis = this.SVG.append("g").call(d3.axisLeft(this.y))

      // Creating labels
      const labels = []
      const allGroups = parseDataToGroups(this.data.items)
      Object.entries(allGroups).map((group, index) => {
        const type = group[0]
        const color = randomColor(index)
        const { info } = this.getLabelDescription(type)
        labels.push({
          slug: type,
          text: info,
          color,
        })
      })
      this.$store.dispatch("setLabels", labels)

      // Adding lines
      const groups = parseDataToGroups(items)
      Object.entries(groups).map((group, index) => {
        const color = randomColor(index)
        this.addLine(group[1], color)
      })
    },
    // Add the line
    addLine(data, color = "black") {
      const { x, y } = this
      return this.SVG.append("path")
        .datum(data)
        .attr("fill", "none")
        .attr("stroke", color)
        .attr("stroke-width", 1.5)
        .attr(
          "d",
          d3
            .line()
            .curve(d3.curveBasis)
            .x(function (d) {
              return x(d3.timeParse("%Y-%m-%d %H:%M:%S")(d.happened_at))
            })
            .y(function (d) {
              return y(parseFloat(d.value))
            })
        )
    },
    filterData(data, viewMode) {
      const { items } = data
      if (viewMode && viewMode !== "all") {
        return items.filter((item) => item.label === viewMode)
      }
      return items
    },
    parseRangeDate(data) {
      const minMax = d3.extent(data, function (d) {
        return d3.timeParse("%Y-%m-%d %H:%M:%S")(d.happened_at)
      })
      return minMax
    },
    parseRangeValue(data) {
      const minMax = d3.extent(data, (d) => {
        return parseFloat(d.value)
      })
      return minMax
    },
    getLabelDescription(label) {
      return this.data?.info.find((item) => item.name === label)
    },
  },
}
</script>
