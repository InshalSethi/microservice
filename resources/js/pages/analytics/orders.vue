<script setup>
import ApexChart from '@/pages/analytics/apexchart.vue';
import DashboardTable from '@/pages/analytics/dashboardTable.vue';
import LogisticsCardStatistics from '@/views/apps/logistics/LogisticsCardStatistics.vue';
import { computed, onBeforeMount, ref } from 'vue';
import { useStore } from 'vuex';

const store = useStore();
const date = ref('');
const orderStartdate = ref('');
const orderEnddate = ref('');
const orderData = ref([]);
const analyticsData = ref(null);
const patientData = ref([]);

const filter = ref({
  patient: 'all',
  order_status: 'all',
});

// Change this to a regular function that returns a computed ref
const useSortedPatient = () => {
  const isLoading = ref(false);
  const error = ref(null);

  const sortedPatient = computed(() => {
    const allOption = { id: 'all', patient_name: 'All' };
    const sortedData = patientData.value.slice().sort((a, b) => {
      return a.patient_name.localeCompare(b.patient_name);
    });
    return [allOption, ...sortedData];
  });

  const fetchPatientData = async () => {
    isLoading.value = true;
    error.value = null;
    try {
      await store.dispatch('getAnalyticOrderFilters');
      patientData.value = store.getters.getOrderFilters || [];
      console.log('Fetched patient data:', patientData.value);
    } catch (e) {
      console.error('Error fetching patient data:', e);
      error.value = 'Failed to fetch patient data';
    } finally {
      isLoading.value = false;
    }
  };

  onBeforeMount(fetchPatientData);

  return { sortedPatient, isLoading, error, fetchPatientData };
};

const { sortedPatient, isLoading, error, fetchPatientData } = useSortedPatient();

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
  setDateRange();
  const now = new Date();
  let startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
  const currentDate = formatDate(now);
  const monthStart = formatDate(startOfMonth);
  orderStartdate.value = monthStart;
  orderEnddate.value = currentDate;
  await fetchOrder();
});

const fetchOrder = async () => {
  await store.dispatch('getAdminAnalyticsOrder', {
    start_date: orderStartdate.value,
    end_date: orderEnddate.value,
    patientId: filter.value.patient,
    order_status: filter.value.order_status
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
const order_status = [
  { title: 'All', slug: 'all' },
  { title: 'Pending', slug: 'pending' },
  { title: 'On hold', slug: 'on hold' },
  { title: 'Completed', slug: 'completed' },
  { title: 'Canceled', slug: 'canceled' },
  { title: 'Failed', slug: 'failed' },
  { title: 'Refunded', slug: 'refunded' }
];

const onOrderStatusChange = () => {
  fetchOrder();
};

const onPatientChange = () => {
  fetchOrder();
};
</script>

<template>
  <VRow class="match-height">
    <VCol cols="12" md="4" class="px-3">
      <AppDateTimePicker v-model="date" label="Date Range" :config="{ mode: 'range' }" @change="changeDateRange" />
    </VCol>
    <VCol cols="12" md="3">
      <VAutocomplete v-model="filter.patient" label="Patient" placeholder="Patient" density="comfortable"
        :items="sortedPatient" item-title="patient_name" item-value="id" :loading="isLoading" :error-messages="error"
        @update:model-value="onPatientChange" />
    </VCol>
    <VCol cols="12" md="3">
      <VAutocomplete v-model="filter.order_status" label="Order Status" density="comfortable" :items="order_status"
        placeholder="Order Status" item-title="title" item-value="slug" @update:model-value="onOrderStatusChange" />
      <!-- <VSelect
        v-model="filter.order_status"
        label="Order Status"
        placeholder="Order Status"
        density="comfortable"
        :items="order_status"
        item-title="title"
        item-value="slug"
        @update:model-value="onOrderStatusChange"
      /> -->
    </VCol>
    <VCol cols="12" md="12">
      <LogisticsCardStatistics :overview="store.getters.getAnalyticsOverview" />
    </VCol>
    <VCol cols="12" md="12">
      <VCard title="Chart">
        <VCardText>
          <ApexChart />
        </VCardText>
      </VCard>
    </VCol>
    <VCol cols="12" md="12">
      <VCardTitle>
        Leadboards
      </VCardTitle>
    </VCol>
    <VCol cols="12">
      <DashboardTable :downloadable="true" />
    </VCol>
  </VRow>
</template>
