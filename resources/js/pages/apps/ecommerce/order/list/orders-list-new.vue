<script setup>

import API from '@/api';
import { ADMIN_GET_ORDER_API } from '@/constants';
import debounce from 'lodash.debounce';
import { useRoute, useRouter } from 'vue-router';
import { useStore } from "vuex";
const date = ref();
const router = useRouter();
const route = useRoute()
const store = useStore();
const ordersList = ref([]);
const widgetData = ref([
  {
    title: 'Total Orders',
    value: 0,
    icon: 'ri-calendar-2-line',
    key_value: 'total_order'
  },
  {
    title: 'Total Appointment Orders',
    value: 0,
    icon: 'ri-check-double-line',
    key_value: 'total_appointment_order'
  },
  {
    title: ' Without Appointment Orders',
    value: 0,
    icon: 'ri-wallet-3-line',
    key_value: 'total_appointment_order_without'
  },
  {
    title: 'Completed Meetings Orders',
    value: 0,
    icon: 'ri-error-warning-line',
    key_value: 'completedMeetings'
  },
])
const searchQuery = ref('')
// const search = ref('')

// Data table options
const page = ref(1)
const sortBy = ref()
const orderBy = ref()

const isLoading = ref(true);
// Data table Headers
const headers = [
  {
    title: 'Order',
    key: 'order_id',
    searchable:false
  },


  {
    title: 'Patient',
    key: 'patient_name',
    searchable:true
  },
  {
    title: 'Meeting Date',
    key: 'appointment_date',
    searchable:false
  },
  {
    title: 'Meeting Time',
    key: 'appointment_time',
    searchable:false
  },
  {
    title: 'Total Amount',
    key: 'total_amount',
  },
  {
    title: 'Status',
    key: 'status',
  },

  {
    title: 'Actions',
    key: 'actions',
    searchable:false
  },
]

const updateOptions = options => {
  page.value = options.page
  sortBy.value = options.sortBy[0]?.key
  orderBy.value = options.sortBy[0]?.order
}

const resolvePaymentStatus = status => {
  if (status === 1)
    return {
      text: 'Paid',
      color: 'success',
    }
  if (status === 2)
    return {
      text: 'Pending',
      color: 'warning',
    }
  if (status === 3)
    return {
      text: 'Cancelled',
      color: 'secondary',
    }
  if (status === 4)
    return {
      text: 'Failed',
      color: 'error',
    }
}

const resolveStatusVariant = (status) => {
  switch (status.toLowerCase()) {
    case 'completed':
      return { color: 'success', text: 'Completed' }
    case 'scheduled':
      return { color: 'primary', text: 'Scheduled' }
    case 'cancelled':
      return { color: 'error', text: 'Cancelled' }
    default:
      return { color: 'warning', text: 'Pending' }
  }
}



const ordersGetList = computed(() => {

  return ordersList.value.map((history) => ({
    ...history,
    //appointment_date: getConvertedDate(convertUtcTime(history.appointment_time, history.appointment_date, history.timezone)),
    //appointment_time: getConvertedTime(convertUtcTime(history.appointment_time, history.appointment_date, history.timezone)),

    // appointment_date: changeFormat(history.appointment_date),
    // appointment_time: convertUtcDateTimeToLocal(history.appointment_date, history.appointment_time, 'time'),
    // start_time: changeDateFormat(history.start_time),
    // end_time: changeDateFormat(history.end_time),
    // duration: totalCallDuration(history.start_time, history.end_time),
  }));
  // isLoading.value - false
  // ordersList.value.sort((a, b) => {
  //     return b.id - a.id;
  // });
  // return ordersList.value
});
onMounted(async () => {
  // setDateRange();
  store.dispatch("updateIsLoading", true);
  // await store.dispatch("orderList");
  // ordersList.value = store.getters.getOrderList;
  // ordersList.value.sort((a, b) => {
  //     return b.id - a.id;
  // });
  await store.dispatch("orderCount");
  let orderCount = store.getters.getOrderCount
  for (let data of widgetData.value) {
    if (data.key_value == 'total_order') {
      data.value = orderCount[data.key_value]
    }
    if (data.key_value == 'total_appointment_order') {
      data.value = orderCount[data.key_value]
    }
    if (data.key_value == 'total_appointment_order_without') {
      data.value = orderCount[data.key_value]
    }
    if (data.key_value == 'completedMeetings') {
      data.value = orderCount[data.key_value]
    }
    console.log(orderCount, orderCount[data.key_value])
  }
  console.log("ordersList", orderCount);
  loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] });
  isLoading.value = false;
});
const setDateRange = () => {
  const today = new Date();
  const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

  // Set the date range
  startRangeDate.value = formattedDate(startOfMonth);
  endRangeDate.value = formattedDate(today);
  date.value = [startOfMonth, today];
  // console.log("mmm>>>>",date.value, startOfMonth, today);
};
const formattedDate = (originalDate) => {
      // Convert the original date string into a Date object
      const date = new Date(originalDate);

      // Extract year, month, and day
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
      const day = String(date.getDate()).padStart(2, '0');

      // Return formatted date in YYYY-MM-DD format
      return `${month}-${day}-${year}`;
};
const convertUtcTime = (time, date, timezone) => {
  const timezones = {
    "EST": "America/New_York",
    "CST": "America/Chicago",
    "MST": "America/Denver",
    "PST": "America/Los_Angeles",
    "PST": "Asia/Karachi"
    // Add more mappings as needed
  };

  // Get the IANA timezone identifier from the abbreviation
  let ianaTimeZone = timezones[timezone];

  if (!ianaTimeZone) {
    // throw new Error(`Unknown timezone abbreviation: ${timezone}`);
    timezone = 'PST'
    ianaTimeZone = timezones[timezone];
  }

  // Combine date and time into a single string
  const dateTimeString = `${date}T${time}Z`;  // Assuming the input date and time are in UTC

  // Create a Date object from the combined string
  const dateObj = new Date(dateTimeString);

  // Options for the formatter
  const options = {
    timeZone: ianaTimeZone,
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false,
  };

  // Create the formatter
  const formatter = new Intl.DateTimeFormat('en-US', options);

  // Format the date
  const convertedDateTime = formatter.format(dateObj);

  return convertedDateTime;
};
const getConvertedTime = (inputDate) => {
  // Split the input date string into date and time components
  const [datePart, timePart] = inputDate.split(', ');

  // Split the time component into hours, minutes, and seconds
  let [hours, minutes, seconds] = timePart.split(':');

  // Convert the hours to an integer
  hours = parseInt(hours);

  // Determine the period (AM/PM) and adjust the hours if necessary
  const period = hours >= 12 ? 'PM' : 'AM';
  hours = hours % 12 || 12; // Convert 0 and 12 to 12, and other hours to 1-11

  // Format the time as desired
  const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes}${period}`;

  return formattedTime;
}
const getConvertedDate = (inputDate) => {
  // Split the input date string into date and time components
  const [datePart, timePart] = inputDate.split(', ');

  // Split the date component into month, day, and year
  const [month, day, year] = datePart.split('/');

  // Create a new Date object from the parsed components
  const dateObject = new Date(`${year}-${month}-${day}T${timePart}`);

  // Define an array of month names
  const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];

  // Format the date as desired
  const formattedDate = `${monthNames[dateObject.getMonth()]} ${day}, ${year}`;

  return formattedDate;
};
function changeFormat(dateFormat) {
  const dateParts = dateFormat.split('-'); // Assuming date is in yyyy-mm-dd format
  const year = parseInt(dateParts[0]);
  const month = String(dateParts[1]).padStart(2, '0'); // Pad single-digit months with leading zero
  const day = String(dateParts[2]).padStart(2, '0'); // Pad single-digit days with leading zero

  // Create a new Date object with the parsed values
  const date = new Date(year, month - 1, day); // Month is zero-based in JavaScript Date object

  // Format the date as mm-dd-yyyy
  const formattedDate = month + '-' + day + '-' + date.getFullYear();

  return formattedDate;
}
const viewOrder = (orderId) => {
  router.push({ name: "admin-order-detail", params: { id: orderId } });
};
const formatDate = (date) => {
  const messageDate = new Date(date);
  const options = {
    year: "numeric",
    month: "numeric",
    day: "numeric",
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  };
  const formattedDate = messageDate
    .toLocaleString("en-US", options)
    .replace(/\//g, "-")
    .replace(/,/, "");
  return `${formattedDate}`;
};
const getStatusColor = (status) => {
  switch (status) {
    case "pending":
      return "orange";
    case "Shipped":
      return "blue";
    case "Delivered":
      return "green";
    case "Cancelled":
      return "red";
    default:
      return "gray";
  }
};
const serverItems = ref([]);
const loading = ref(true);
const totalItems = ref(0);
const search = ref();
const itemsPerPage = ref(30);
const status = ref('All');
const loadItems = debounce(async ({ page, itemsPerPage, sortBy }) => {
  // if(status.value = 'All'){
  //   status.value = 'all'
  // }
  const payload = {
    page,
    itemsPerPage,
    sortBy,
    filters: {
      from_date:startRangeDate.value ? startRangeDate.value: 'all',
      to_date: endRangeDate.value ?endRangeDate.value : 'all',
      status:status.value.toLowerCase(),
    },
    search: search.value,
  }
  console.log("records", page, itemsPerPage, sortBy, ADMIN_GET_ORDER_API);
  loading.value = true;
  const data = await API.getDataTableRecord(ADMIN_GET_ORDER_API, payload, headers);
  console.log('patientData', data);
  serverItems.value = data.items;
  totalItems.value = data.total;
  loading.value = false;

}, 500);
const addNewOrder = () => {
  store.dispatch("updateIsLoading", true);
  router.replace(route.query.to && route.query.to != '/admin/orders' ? String(route.query.to) : '/admin/add-order')
  store.dispatch("updateIsLoading", false);
};

const startRangeDate = ref();
const endRangeDate = ref();
const changeDateRange = async () => {
  console.log('changed date', date.value, 'type:', typeof date.value);
  
  try {
    const dateString = typeof date.value === 'string' ? date.value : '';
    const [startDate, endDate] = dateString.split(" to ");
    
    if (startDate && endDate) {
      startRangeDate.value = formattedDate(startDate);
      endRangeDate.value = formattedDate(endDate);
      loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] });
      // await store.dispatch('getAdminAnalyticsOverview', {
      //   start_date: startDate,
      //   end_date: endDate,
      // });
      // analyticsData.value = store.getters.getAnalyticsOverview;
    } else {
      console.warn('Invalid date range');
      // Handle invalid date range
    }
  } catch (error) {
    console.error('Error processing date range:', error);
    // Handle the error appropriately
  }
};
const onStateChange = () =>{
   loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] });
};
const reset = () => {
  date.value = '';
  startRangeDate.value = 'all';
  endRangeDate.value = 'all';
  status.value = 'All';
  search.value= '';
  loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] });
}
const formatOrderId = (id) => {
    if (id >= 1 && id <= 9) {
        return id.toString().padStart(4, '0');
      } else if (id >= 10 && id <= 99) {
        return id.toString().padStart(4, '0');
      } else if (id >= 100 && id <= 999) {
        return id.toString().padStart(4, '0');
      } else {
        return id; // or handle cases for IDs outside these ranges
      }
}
</script>

<template>
  <div>

    <VCard class="mb-6">
      <VCardText class="px-2">
        <VRow>
          <template v-for="(data, index) in widgetData" :key="index">
            <VCol cols="12" sm="6" md="3" class="px-6">
              <div class="d-flex justify-space-between"
                :class="$vuetify.display.xs ? 'product-widget' : $vuetify.display.sm ? index < 2 ? 'product-widget' : '' : ''">
                <div class="d-flex flex-column gap-y-1">
                  <h4 class="text-h4">
                    {{ data.value }}
                  </h4>
                  <span class="text-base text-capitalize">
                    {{ data.title }}
                  </span>
                </div>

                <VAvatar variant="tonal" rounded size="42">
                  <VIcon :icon="data.icon" size="26" />
                </VAvatar>
              </div>
            </VCol>
            <VDivider
              v-if="$vuetify.display.mdAndUp ? index !== widgetData.length - 1 : $vuetify.display.smAndUp ? index % 2 === 0 : false"
              vertical inset length="100" />
          </template>
        </VRow>
      </VCardText>
    </VCard>
    <VRow>

      <VCol cols="12" md="12">
        <VCard title="Orders">
          <VCardText>
            <VRow>
              <VCol cols="12" md="4">
                <AppDateTimePicker
                  v-model="date"
                  label="Date Range"
                  :config="{ mode: 'range' }"
                  density="compact"
                  @change="changeDateRange()"
                >
                
                </AppDateTimePicker>
                
               </VCol>
              
              <VCol cols="12" md="2" class="px-0"> 
                <VAutocomplete v-model="status" label="Status" placeholder="Status" density="compact"
                :items="['All', 'Pending', 'Recevied', 'Complete','Shipped','Transit']" @update:model-value="onStateChange" />
                
              </VCol>
              <VCol cols="12" md="3"> 
                <VTextField v-model="search" placeholder="Search Order" density="compact"
                  style="max-inline-size: 200px; min-inline-size: 200px;" />
                
              </VCol>
              <!-- <VCol cols="12" md="1">
                
              </VCol> -->
              <VCol
              cols="12" class="pl-0"
              
              md="3"
            >
            <VBtn @click="reset()" class="mr-2">Reset</VBtn>
            
            <VBtn color="primary" @click="addNewOrder" v-if="$can('read', 'Order Add')">New Order</VBtn>
            </VCol>
            </VRow>
          </VCardText>

          <VCardText>
            <!-- <VDataTable :loading="isLoading" :headers="headers" :items="ordersGetList"
                            :search="searchQuery"  :items-per-page="5" class="text-no-wrap"> -->
            <v-data-table-server v-model:items-per-page="itemsPerPage" :headers="headers" :items="serverItems"
              :items-length="totalItems" :loading="loading" :search="search" :item-value="name"
              @update:options="loadItems">

              <!-- full name -->
              <template #item.order_id="{ item }">
                <RouterLink :to="{ name: 'admin-order-detail', params: { id: item.order_id } }">
                  {{ formatOrderId(item.order_id) }}
                </RouterLink>
              </template>
              <template #item.total_amount="{ item }"> ${{ parseFloat(item.total_amount  + item.order_total_shipping).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  }) }} 
                <!-- ${{ parseFloat(item.order_total_amount +
                  item.order_total_shipping).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  }) }} -->
              </template>
              <template #item.patient_name="{ item }">
                <div class="d-flex align-center">
                  <VAvatar size="32" :color="item.profile_picture ? '' : 'primary'"
                    :class="item.profile_picture ? '' : 'v-avatar-light-bg primary--text'"
                    :variant="!item.profile_picture ? 'tonal' : undefined">
                    <VImg v-if="item.profile_picture" :src="item.profile_picture" />
                    <span v-else>{{ avatarText(item.patient_name) }}</span>
                  </VAvatar>
                  <div class="d-flex flex-column ms-3">
                    <RouterLink :to="{ name: 'admin-patient-profile', params: { id: item.patient_id } }">
                      <div class=" font-weight-medium">
                        {{ item.patient_name }}
                      </div>
                    </RouterLink>
                    <small>{{ item.email }}</small>
                  </div>
                </div>
              </template>


              <template #item.date="{ item }">
                <span>

                  {{ formatDate(item.created_at) }}

                </span>
              </template>
              <template #item.appointment_date="{ item }">
                <div v-if="item.appointment_date">
                  {{ changeFormat(item.appointment_date) }}
                </div>
                <div v-else>

                </div>

              </template>

              <template #item.appointment_time="{ item }">
                <div v-if="item.appointment_time">

                  {{ getConvertedTime(convertUtcTime(item.appointment_time, item.appointment_date, item.timezone)) }}
                </div>
                <div v-else>

                </div>


              </template>

              <template #item.status="{ item }">
                <span>
                  <VChip variant="tonal" :color="getStatusColor(item.status)" size="small">
                    {{ item.status }}
                  </VChip>
                </span>
              </template>
              <!-- status -->

              <template #item.appointment_status="{ item }">
                <VChip :color="resolveStatusVariant(item.appointment_status).color" class="font-weight-medium"
                  size="small" :class="{ 'blink-status': item.appointment_status.toLowerCase() !== 'completed' }">
                  {{ resolveStatusVariant(item.appointment_status).text }}
                </VChip>

              </template>
              <template #item.actions="{ item }">
                <IconBtn size="small">
                  <VIcon icon="ri-more-2-line" />
                  <VMenu activator="parent">
                    <VList>
                      <VListItem value="view" v-if="$can('read', 'Order Detail Tab')">
                        <RouterLink :to="{ name: 'admin-order-detail', params: { id: item.order_id } }"
                          class="text-high-emphasis">
                          View
                        </RouterLink>
                      </VListItem>
                      <VListItem value="view" v-if="$can('read', 'Order Edit')">
                        <RouterLink :to="{ name: 'admin-order-edit', params: { id: item.order_id } }"
                          class="text-high-emphasis">
                          Edit
                        </RouterLink>
                      </VListItem>

                    </VList>
                  </VMenu>
                </IconBtn>
              </template>
            </v-data-table-server>
            <!-- </VDataTable> -->
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>

<style lang="scss" scoped>
#customer-link {
  &:hover {
    color: '#000' !important
  }
}

.product-widget {
  border-block-end: 1px solid rgba(var(--v-theme-on-surface), var(--v-border-opacity));
  padding-block-end: 1rem;
}
</style>
