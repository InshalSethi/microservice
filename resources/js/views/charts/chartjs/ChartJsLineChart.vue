<script setup>
import { getLineChartConfig } from '@core/libs/chartjs/chartjsConfig';
import LineChart from '@core/libs/chartjs/components/LineChart';
import { useTheme } from 'vuetify';

const props = defineProps({
  colors: {
    type: null,
    required: true,
  },
})

const vuetifyTheme = useTheme()

const data = {
  labels: [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "May",
    "Jun",
    "Jul",
    "Aug",
    "Sep",
    "Oct",
    "Nov",
    "Dec",
  ],
  datasets: [
    {
      fill: false,
      tension: 0.5,
      pointRadius: 1,
      label: 'Europe',
      pointHoverRadius: 5,
      pointStyle: 'circle',
      borderColor: props.colors.primary,
      backgroundColor: props.colors.primary,
      pointHoverBorderWidth: 5,
      pointHoverBorderColor: props.colors.white,
      pointBorderColor: 'transparent',
      pointHoverBackgroundColor: props.colors.primary,
      data: [
        80,
        150,
        180,
        270,
        210,
        160,
        160,
        202,
        265,
        210,
        270,
        255,
        290,
        360,
        375,
      ],
    },
  ],
}

const chartConfig = computed(() => getLineChartConfig(vuetifyTheme.current.value))
</script>

<template>
  <LineChart
    :chart-options="chartConfig"
    :height="400"
    :chart-data="data"
  />
</template>
