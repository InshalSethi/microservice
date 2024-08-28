<script setup>
import { onMounted, ref, watchEffect } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useStore } from 'vuex';
const store = useStore()
const router = useRouter()
const route = useRoute()
const props = defineProps({
    overview: {
        type: String,
        required: true,
    },

    isDate:{
      type: String,
        required: true,
    },
    
    
})

const logisticData = ref([
  {
    icon: 'ri-pie-chart-2-line',
    color: 'primary',
    title: 'Total Sales',
    value: 0,
    change: 0,
    isHover: false,
  },
  {
    icon: 'ri-shopping-cart-line',
    color: 'warning',
    title: 'Total Orders',
    value: 8,
    change: 0,
    isHover: false,
  },
  {
    icon: 'ri-medicine-bottle-line',
    color: 'error',
    title: 'Products Sold',
    value: 0,
    change: 0,
    isHover: false,
  },
  {
    icon: 'ri-money-dollar-circle-line',
    color: 'info',
    title: 'Net Sales',
    value: 0,
    change: 0,
    isHover: false,
  },
]);

watchEffect(() => {
   
  if (store.getters.getAnalyticsOverview.totals && store.getters.getAnalyticsOverview.totals[0]) {
  const totals = store.getters.getAnalyticsOverview.totals[0];

  logisticData.value[0].value = '$' + (totals.sales_amount !== null ? parseFloat(totals.sales_amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  }) : 0);
  logisticData.value[1].value = totals.total_sales !== null ? totals.total_sales : 0;
  logisticData.value[2].value = totals.products_sold !== null ? totals.products_sold : 0;
  logisticData.value[3].value = '$' + (totals.sales_amount !== null ? parseFloat(totals.sales_amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  }) : 0);
  // Update other values as needed
}

});
onMounted(async () => {
  
  console.log('data1',props.overview);
})



const handleCardClick = async(title) => {
  if (title === 'Products Sold') {
    console.log("isDate",props.isDate);
    await store.dispatch('SaveCurrentDate',{
    filter_date:props.isDate
  });
    router.replace(route.query.to ? String(route.query.to) : '/admin/analytics/products');
  }
  if (title === 'Total Orders') {
    console.log("isDaTotal Order",props.isDate);
    await store.dispatch('SaveCurrentDate',{
    filter_date:props.isDate
  });
    router.replace(route.query.to ? String(route.query.to) : '/admin/analytics/overview-order');
  }
}


</script>

<template>
  <VRow>
    <VCol
      v-for="(data, index) in logisticData"
      :key="index"
      cols="12"
      md="3"
      sm="6"
    >
      <div>
        <VCard
          class="logistics-card-statistics cursor-pointer"
          :style="data.isHover ? `border-block-end-color: rgb(var(--v-theme-${data.color}))` : `border-block-end-color: rgba(var(--v-theme-${data.color}),0.7)`"
          @mouseenter="data.isHover = true"
          @mouseleave="data.isHover = false"
          @click="handleCardClick(data.title)"
        >
          <VCardText>
            <div class="d-flex align-center gap-x-4 mb-2">
              <VAvatar
                variant="tonal"
                :color="data.color"
                rounded
              >
                <VIcon
                  :icon="data.icon"
                  size="24"
                />
              </VAvatar>
              <h4 class="text-h4">
                {{ data.value }}
                
              </h4>
            </div>
            <h6 class="text-h6 font-weight-regular">
              {{ data.title }}
            </h6>
            <!-- <div class="d-flex align-center">
              <div class="text-body-1 font-weight-medium me-2">
                {{ data.change }}%
              </div>
              <span class="text-sm text-disabled">than last week</span>
            </div> -->
          </VCardText>
        </VCard>
      </div>
    </VCol>
  </VRow>
</template>

<style lang="scss" scoped>
@use "@core-scss/base/mixins" as mixins;

.logistics-card-statistics {
  border-block-end-style: solid;
  border-block-end-width: 2px;

  &:hover {
    border-block-end-width: 3px;
    margin-block-end: -1px;

    @include mixins.elevation(10);

    transition: all 0.1s ease-out;
  }
}

.skin--bordered{
  .logistics-card-statistics {
    &:hover {
      margin-block-end: -2px;
    }
  }
}
</style>
