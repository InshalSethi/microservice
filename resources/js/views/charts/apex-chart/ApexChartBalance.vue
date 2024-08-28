<script setup>
import { getLineChartSimpleConfig } from '@core/libs/apex-chart/apexCharConfig';
import { computed, ref, watch } from 'vue';
import { useTheme } from 'vuetify';
import { useStore } from 'vuex';

const store = useStore()
const vuetifyTheme = useTheme()

const balanceChartConfig = computed(() => getLineChartSimpleConfig(vuetifyTheme.current.value))

const series = ref([{
  data: []
}])

const updateChartData = () => {
  const analyticsOverview = store.getters.getAnalyticsOverview
  if (analyticsOverview && analyticsOverview.chart && analyticsOverview.chart.chart_data) {
    series.value = [{
      data: analyticsOverview.chart.chart_data
    }]
  }
}

// Watch for changes in the store data
watch(() => store.getters.getAnalyticsOverview, updateChartData, { immediate: true })

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
