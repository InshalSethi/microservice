<script setup>
import ProductsAnalyticsTable from '@/pages/analytics/ProductsAnalyticsTable.vue';
import ProductsCardStatistics from '@/views/apps/logistics/ProductsCardStatistics.vue';
import { getLineChartConfig } from '@core/libs/chartjs/chartjsConfig';
import LineChart from '@core/libs/chartjs/components/LineChart';
import { computed, onBeforeMount, ref, watch } from 'vue';
import { useTheme } from 'vuetify';
import { useStore } from 'vuex';

const store = useStore();
const date = ref('');
const orderStartdate = ref('');
const orderEnddate = ref('');
const orderData = ref([]);
const analyticsData = ref(null);
const productData = ref([]);

const vuetifyTheme = useTheme()

const data = computed(() => ({
     labels: store.getters.getProductsAnalytics.chart.chart_dates,
     datasets: [
          {
               fill: false,
               tension: 0.5,
               pointRadius: 1,
               label: 'Orders',
               pointHoverRadius: 5,
               pointStyle: 'circle',
               borderColor: 'orange',
               backgroundColor: 'orange',
               pointHoverBorderWidth: 5,
               pointHoverBorderColor: 'white',
               pointBorderColor: 'transparent',
               pointHoverBackgroundColor: 'orange',
               data: store.getters.getProductsAnalytics.chart.chart_data,
          }
     ],
}));

const chartConfig = computed(() => getLineChartConfig(vuetifyTheme.current.value))

const filter = ref({
     show_by: 'all_products',
     product: 'all',
});

// Change this to a regular function that returns a computed ref
const useSortedProduct = () => {
     const isLoading = ref(false);
     const error = ref(null);

     const sortedProduct = computed(() => {
          const allOption = { id: 'all', title: 'All' };
          const sortedData = productData.value.slice().sort((a, b) => {
               return a.title.localeCompare(b.title);
          });
          return [allOption, ...sortedData];
     });

     const fetchProductData = async () => {
          isLoading.value = true;
          error.value = null;
          try {
               await store.dispatch('getAllProductsList');
               productData.value = store.getters.getProductsList || [];
               console.log('Fetched Product data:', productData.value);
          } catch (e) {
               console.error('Error fetching Product data:', e);
               error.value = 'Failed to fetch Product data';
          } finally {
               isLoading.value = false;
          }
     };

     onBeforeMount(fetchProductData);

     return { sortedProduct, isLoading, error, fetchProductData };
};

const { sortedProduct, isLoading, error, fetchProductData } = useSortedProduct();

const setDateRange = () => {
     
     const today = new Date();
     const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
     date.value = [startOfMonth, today];
     console.log("xxxxxxxx", date.value);
};

const formatDate = (date) => {
     const year = date.getFullYear();
     const month = String(date.getMonth() + 1).padStart(2, '0');
     const day = String(date.getDate()).padStart(2, '0');
     return `${year}-${month}-${day}`;
};

onBeforeMount(async () => {
     let productRange =  store.getters.getCurrentFilterDate.filter_date;
     console.log("productRange",productRange);
     if(productRange){
          try {
          date.value = productRange;
          const dateString = typeof productRange === 'string' ? productRange : '';
          const [startDate, endDate] = dateString.split(" to ");
          if (startDate && endDate) {
               orderStartdate.value = startDate;
               orderEnddate.value = endDate;
               await fetchOrder();
          } else {
               console.warn('Invalid date range');
          }
     } catch (error) {
          console.error('Error processing date range:', error);
     }
          await store.dispatch('getAllProductsList');
          await fetchOrder();
     
     }else{

     setDateRange();
     const now = new Date();
     let startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
     const currentDate = formatDate(now);
     const monthStart = formatDate(startOfMonth);
     orderStartdate.value = monthStart;
     orderEnddate.value = currentDate;
          await store.dispatch('getAllProductsList');
          await fetchOrder();
     }
});

const fetchOrder = async () => {
     await store.dispatch('getAdminProductsAnalytics', {
          start_date: orderStartdate.value,
          end_date: orderEnddate.value,
          single_product: filter.value.product
     });
};

const changeDateRange = async () => {
     console.log('changed date', date.value, 'type:', typeof date.value);
     try {
          const dateString = typeof date.value === 'string' ? date.value : '';
          const [startDate, endDate] = dateString.split(" to ");
          if (startDate && endDate) {
               orderStartdate.value = startDate;
               orderEnddate.value = endDate;
               await fetchOrder();
          } else {
               console.warn('Invalid date range');
          }
     } catch (error) {
          console.error('Error processing date range:', error);
     }
};
const showRecordsByList = [
     { title: 'All Products', slug: 'all_products' },
     { title: 'Single Product', slug: 'single_product' },
];

const onOrderStatusChange = () => {
     if (filter.value.show_by !== 'single_product')
          fetchOrder();
};

const onProductChange = () => {
     fetchOrder();
};
// Watch for changes in the store state
watch(() => store.getters.getProductsAnalytics, () => {
     // Force re-render of the chart
     data.value = { ...data.value };
}, { deep: true });
</script>

<template>
     <VRow class="match-height">
          <VCol cols="12" md="3" class="px-3">
               <AppDateTimePicker v-model="date" label="Date Range" :config="{ mode: 'range' }"
                    @change="changeDateRange" />
          </VCol>
          <VCol cols="12" md="3">
               <VAutocomplete v-model="filter.show_by" label="Show" density="comfortable" :items="showRecordsByList"
                    placeholder="Show" item-title="title" item-value="slug" @update:model-value="onOrderStatusChange" />
          </VCol>
          <VCol cols="12" md="3" v-if="filter.show_by === 'single_product'">
               <VAutocomplete v-model="filter.product" label="Products" placeholder="Products" density="comfortable"
                    :items="sortedProduct" item-title="title" item-value="id" :loading="isLoading"
                    :error-messages="error" @update:model-value="onProductChange" />
          </VCol>
     </VRow>
     <VRow>
          <VCol cols="12" md="12">
               <ProductsCardStatistics :isDate="date"  />
          </VCol>
          <VCol cols="12" md="12">
               <VCard title="Chart">
                    <VCardText>
                         <LineChart :chart-options="chartConfig" :height="400" :chart-data="data" />
                    </VCardText>
               </VCard>
          </VCol>
          <VCol cols="12">
               <ProductsAnalyticsTable />
          </VCol>
     </VRow>
</template>
