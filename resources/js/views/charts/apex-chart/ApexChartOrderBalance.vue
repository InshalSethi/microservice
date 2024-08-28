<script setup>
// import { getLineChartSimpleConfig } from '@core/libs/apex-chart/apexCharConfig';
import { getLineChartSimpleOrderConfig } from '@core/libs/apex-chart/apexCharOrderConfig';
import { computed, ref, watch } from 'vue';
import { useTheme } from 'vuetify';
import { useStore } from 'vuex';

const store = useStore()
const vuetifyTheme = useTheme()

const balanceChartConfig = computed(() => getLineChartSimpleOrderConfig(vuetifyTheme.current.value))

const series = ref([{
  data: []
}])

const updateChartData = () => {
  const analyticsOverview = store.getters.getAnalyticsOverviewOrder
  if (analyticsOverview && analyticsOverview.chart && analyticsOverview.chart.chart_data) {
    series.value = [{
      data: analyticsOverview.chart.chart_data
    }]
  }
}

// Watch for changes in the store data
watch(() => store.getters.getAnalyticsOverviewOrder, updateChartData, { immediate: true })

// Log for debugging
watch(series, (newValue) => {
  console.log('Chart series updated:', newValue)
}, { deep: true })
</script>

<template>
  <VueApexCharts
    type="line"
    height="400"
    :options="balanceChartConfig"
    :series="series"
  />
</template>
