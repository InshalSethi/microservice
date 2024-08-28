<script setup>
import { onMounted, ref } from 'vue';
import { useStore } from 'vuex';
import * as XLSX from 'xlsx';
const store = useStore();
const OrdersData = ref([]);
// const isExpanded = ref(false);

const props = defineProps({
    downloadable: {
        type: Boolean,
        required: true,
    },
})
onMounted(() => {
    OrdersData.value = store.getters.getOverviewOrder;
    console.log("OrdersData onMounted:", OrdersData.value);
});

const headers = [
  { title: 'Date', key: 'date' },
  { title: '#Order', key: 'order_id' },
  { title: 'Status', key: 'status' },
  { title: 'Patient', key: 'patient_name' },
  { title: 'Patient type', key: 'customer_type' },
  { title: 'Product(s)', key: 'products' },
  { title: 'Item sold', key: 'item_sold' },
  { title: 'Net Sale', key: 'total_amount' },
  { title: 'Attribution', key: 'attribution' },
];

function changeFormat(dateFormat) {
    const dateParts = dateFormat.split('-');
    const year = parseInt(dateParts[0]);
    const month = parseInt(dateParts[1]);
    const day = parseInt(dateParts[2]);
    const date = new Date(year, month - 1, day);
    return `${month}-${day}-${date.getFullYear()}`;
}

const getStatusColor = (status) => {
    switch (status) {
        case "pending":
            return "warning";
        case "shipped":
            return "primary";
        case "delivered":
            return "success";
        case "cancelled":
            return "error";
        default:
            return "gray";
    }
};
// const tableData = computed(() => {
//     let d = store.getters.getOverviewOrder.map((order) => ({
//         ...order,
//         products:order.products

             
//     }));
//     return d;
// });

const downloadExcel = () => {
    const data = store.getters.getOverviewOrder.map(order => ({
        'Date': changeFormat(order.date),
        '#Order': order.order_id,
        'Status': order.status,
        'Patient': order.patient_name,
        'Customer type': order.customer_type,
        'Product(s)': order.products,
        'Item sold': order.item_sold,
        'Net Sale': order.total_amount,
        'Attribution': order.attribution,
    }));

    console.log('Mapped Data:', data);

    const worksheet = XLSX.utils.json_to_sheet(data);
    XLSX.utils.sheet_add_aoa(worksheet, [
        headers.map(header => header.title)
    ], { origin: 'A1' });
    XLSX.utils.sheet_add_json(worksheet, data, { origin: 'A2', skipHeader: true });

    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Orders');
    XLSX.writeFile(workbook, 'OrdersData.xlsx');
};


// const isLongText = computed(() => {
//     console.log("currentText",currentText.value.length);
//     return currentText.value.length > 20;
// });
const currentText = ref();
const expandedItems = ref(new Map());
const  isLongText  = (item) => {
  return item.length > 20;
}
// Display text based on whether it's expanded
const displayText = (item) => {
    const isExpanded = expandedItems.value.get(item);
    return isExpanded || !isLongText(item)
        ? item
        : item.slice(0, 20) + '...';
};
const toggleText = (item) => {
  const currentState = expandedItems.value.get(item) || false;
  expandedItems.value.set(item, !currentState);
}

// Function to check if item is expanded
const isExpanded = (item)  =>{
  return expandedItems.value.get(item) || false;
}
// const toggleText = (item) => {
//     currentText.value = item;
//     // alert(isExpanded.value);
//     if(isExpanded.value == false){
//         isExpanded.value == true;
//         return isExpanded.value || !isLongText.value
//             ? item
//             : item.slice(0, 20) + '...';
            
//     }
    // if(isExpanded.value || !isLongText.value){
        // return isExpanded.value || !isLongText.value
        //     ? item
        //     : item.slice(0, 20) + '...';
    // }
// };

// Toggle text display
// const toggleText = () => {
//   isExpanded.value = !isExpanded.value;
//   alert(isExpanded.value);
// }

// const isLongText = computed(() => props.item.products.length > 30);
// const displayText = (item) => {
//         if(item.length >= 20){
//             isExpanded.value = false;
//             return  item.slice(0, 20) + '...';

//         }else{
//             isExpanded.value = true;
//             return  item;
//         }
// };


// const toggleText = (isExp) => {
//     alert(isExp);
    
// };
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
    <VCard>
      <VRow v-if="props.downloadable">
        <VCol cols="12" >
          <VBtn @click="downloadExcel" class="float-right mt-2 mb-2 mr-2" color="black" variant="text" v-if="$can('read', 'Analytics Orders Download Data')">
            <VIcon start icon="ri-download-cloud-fill" />Download
          </VBtn>
        </VCol>
      </VRow>
      <VDataTable
          :headers="headers"
          :items="store.getters.getOverviewOrder"
          :items-per-page="5"
          class="text-no-wrap"
      >
          <template #item.order_id="{ item }">
                <RouterLink :to="{ name: 'admin-order-detail', params: { id: item.order_id } }">
                  <span class="text-h6 text-primary">{{ formatOrderId(item.order_id) }}</span>
                </RouterLink>
              
          </template>
          <template #item.date="{ item }">
              <span class="text-h6">{{ changeFormat(item.date) }}</span>
          </template>
          <template #item.total_amount="{ item }">
                    ${{ parseFloat(item.total_amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  }) }}
              <!-- <span class="text-h6">{{ '$'+item.total_amount }}</span> -->
          </template>
          <template #item.status="{ item }">
              <span>
                  <VChip variant="tonal" :color="getStatusColor(item.status)" size="small">
                      {{ item.status }}
                  </VChip>
              </span>
          </template>
          <template #item.products="{ item }">
            <div>
                <!-- Display truncated or full text based on isExpanded -->
                <span style="white-space: normal; word-break: break-word; width:100px">
                {{ displayText(item.products) }}
                <!-- {{ item.products }} -->
                </span>
                <!-- Conditionally render Show More/Show Less button -->
                <button v-if="isLongText(item.products)" @click="toggleText(item.products)" style="font-size: 12px">
                {{ isExpanded(item.products) ? 'Show Less' : 'Show More' }}
                </button>
            </div>
              <!-- <span style="white-space: normal;word-break: break-word;">
                  {{  displayText(item.products)  }}
                  
              </span>
              <button size="small" style="font-size: 10px;">
                {{ isExpanded ? 'Show Less' : 'Show More' }}
                </button> -->
          </template>
          <template #item.item_sold="{ item }">
              <span style="white-space: normal;word-break: break-word;">
                  {{ item.item_sold }}
              </span>
          </template>
      </VDataTable>
    </VCard>
</template>
