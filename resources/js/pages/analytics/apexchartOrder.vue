<script setup>
import ApexChartOrderBalance from '@/views/charts/apex-chart/ApexChartOrderBalance.vue';
</script>

<template>
  <VRow id="apex-chart-wrapper">
    

    <!-- 👉 Balance Line Chart  -->
    <VCol cols="12">
      <ApexChartOrderBalance />
    </VCol>

   
    

   
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/libs/apex-chart.scss";

.date-picker-wrapper {
  inline-size: 10.5rem;
}

#apex-chart-wrapper {
  .v-card-item__append {
    padding-inline-start: 0;
  }
}
</style>
