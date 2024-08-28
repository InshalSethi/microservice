<script setup>
import ApexChart from '@/pages/analytics/apexchart.vue';
import DashboardTable from '@/pages/analytics/dashboardTable.vue';
import LogisticsCardStatistics from '@/views/apps/logistics/LogisticsCardStatistics.vue';
import { useStore } from 'vuex';
const store = useStore()
const date = ref('')
const orderData = ref([]);
const analyticsData = ref(null);
const chartJsCustomColors = {
  white: '#fff',
  yellow: '#ffe802',
  primary: '#836af9',
  areaChartBlue: '#2c9aff',
  barChartYellow: '#ffcf5c',
  polarChartGrey: '#4f5d70',
  polarChartInfo: '#299aff',
  lineChartYellow: '#d4e157',
  polarChartGreen: '#28dac6',
  lineChartPrimary: '#9e69fd',
  lineChartWarning: '#ff9800',
  horizontalBarInfo: '#26c6da',
  polarChartWarning: '#ff8131',
  scatterChartGreen: '#28c76f',
  warningShade: '#ffbd1f',
  areaChartBlueLight: '#84d0ff',
  areaChartGreyLight: '#edf1f4',
  scatterChartWarning: '#ff9f43',
}

onBeforeMount(async () => {
setDateRange();
const now = new Date()
      let startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1)
const formatDate = (date) => {
        const year = date.getFullYear()
        const month = String(date.getMonth() + 1).padStart(2, '0')
        const day = String(date.getDate()).padStart(2, '0')
        return `${year}-${month}-${day}`
      }

      const currentDate = formatDate(now)
      const monthStart = formatDate(startOfMonth)
console.log(date.value[0]);
// const formattedDates = date.value.map(formatDate);
  // console.log('Default date',formattedDates[0],formattedDates[1])
  await store.dispatch('getAdminAnalyticsOverview', {
    start_date:monthStart,
    end_date:currentDate,
  })
    orderData.value = store.getters.getAnalyticsOverview.orders;
    console.log("orderData",orderData.value);
});

// const formatDate = (dateString) => {
//   const date = new Date(dateString);
//   return date.toISOString().split('T')[0];
// };
const selectedDate = ref();
const setDateRange = () => {
  const today = new Date();
  const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

  // Set the date range
  // date.value = [startOfMonth, today];
  date.value = formatDate(startOfMonth)  + ' to ' +  formatDate(today);
  
  console.log("helllowwewew",date.value, formatDate(startOfMonth)  + ' to ' +  formatDate(today));
};
const formatDate = (date)  => {
const month = date.getMonth() + 1; // Months are zero-indexed
      const day = date.getDate();
      const year = date.getFullYear();

      // Pad month and day with leading zeros if needed
      const formattedMonth = month.toString().padStart(2, '0');
      const formattedDay = day.toString().padStart(2, '0');
      // console.log('xnmzcnmasdasdas',`${formattedMonth}-${formattedDay}-${year}`);
      return `${year}-${formattedMonth}-${formattedDay}`;
}
const changeDateRange = async () => {
   
  // console.log('shgdhasgdshla',store.getters.getCurrentFilterDate);
  console.log('changed date', date.value, 'type:', typeof date.value);
  
  try {
    const dateString = typeof date.value === 'string' ? date.value : '';
    const [startDate, endDate] = dateString.split(" to ");
    
    if (startDate && endDate) {
      await store.dispatch('getAdminAnalyticsOverview', {
        start_date: startDate,
        end_date: endDate,
      });
      analyticsData.value = store.getters.getAnalyticsOverview;
    } else {
      console.warn('Invalid date range');
      
    }
  } catch (error) {
    console.error('Error processing date range:', error);
    
  }
};
const standardPlan = {
  plan: 'Standard',
  price: 99,
  benefits: [
    '10 Users',
    'Up to 10GB storage',
    'Basic Support',
  ],
}
const porgressPercentage = (value) => {
  if(value){
console.log('>>>>',value.replace(/%/g, ''));
return value.replace(/%/g, '');
  }
  
};
</script>

<template>
  <VRow class="match-height">
    <VCol cols="12" md="5" class="mt-3 px-3">
                    <AppDateTimePicker
                        v-model="date"
                        label="Date Range"
                        :config="{ mode: 'range' }"
                        @change="changeDateRange()"
                        >
                    </AppDateTimePicker>
            </VCol>
            
    <VCol cols="12">
      <LogisticsCardStatistics :isDate="date" :overview="store.getters.getAnalyticsOverview"/>
    </VCol>
    
    <VCol cols="12" md="4">
        
      <VCard title="Patient Stats">
        
          <VCardText class="d-flex">
          <!-- 👉 Standard Chip -->
          <VRow>
            <VCol cols="6" class="text-center"> 
              <p class="mb-3">New Users</p>
              <h1>{{ store.getters.getAnalyticsOverview.patient_stats.new_users[0] }}</h1>
            </VCol>
            <VCol cols="6" class="text-center"> 
               <p class="mb-3">Return Visitors</p>
              <h1>{{ store.getters.getAnalyticsOverview.patient_stats.returning_users[0]}}</h1>
            </VCol>
          </VRow>
           
        </VCardText>
            <VSpacer />
            <VCardText>
          
            <VDivider/>
          <div class="my-6">
            <div class="d-flex mt-3 mb-2">
              <h6 class="text-h6 font-weight-medium">
                New Users
              </h6>
              <VSpacer />
              <h6 class="text-h6 font-weight-medium">
                {{ store.getters.getAnalyticsOverview.patient_stats.new_users[1]}}
              </h6>
            </div>

            <!-- 👉 Progress -->
            <VProgressLinear
              rounded
              :model-value="porgressPercentage(store.getters.getAnalyticsOverview.patient_stats.new_users[1])"
              height="3"
              color="success"
            />

            
          </div>
          <div class="my-6">
            <div class="d-flex mt-3 mb-2">
              <h6 class="text-h6 font-weight-medium">
                Return Visitors
              </h6>
              <VSpacer />
              <h6 class="text-h6 font-weight-medium">
                {{ store.getters.getAnalyticsOverview.patient_stats.returning_users[1]}}
              </h6>
            </div>

            <!-- 👉 Progress -->
            <VProgressLinear
              rounded
              :model-value="porgressPercentage(store.getters.getAnalyticsOverview.patient_stats.returning_users[1])"
              height="3"
              color="warning"
            />
          </div>
          <div class="my-6">
            <div class="d-flex mt-3 mb-2">
              <h6 class="text-h6 font-weight-medium">
                Engagement
              </h6>
              <VSpacer />
              <h6 class="text-h6 font-weight-medium">
                10%
              </h6>
            </div>

            <!-- 👉 Progress -->
            <VProgressLinear
              rounded
              :model-value="10"
              height="3"
              color="primary"
            />
          </div>
        </VCardText>
      </VCard>
    </VCol>
    <VCol cols="12" md="8">
      <VCard title="Chart">
        <VCardText>
          <ApexChart/>
        </VCardText>
      </VCard>
    </VCol>
        <VCol cols="12" md="12">
            <VCardTitle>
                    Leadboards
                </VCardTitle>
    </VCol>
    <VCol cols="12">
        <DashboardTable :downloadable="false"/>
    </VCol>
  </VRow>
</template>
