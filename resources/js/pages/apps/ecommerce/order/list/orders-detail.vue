<script setup>
import avatar1 from "@images/avatars/avatar-1.png";
import moment from 'moment-timezone';
import { computed, onMounted, reactive, ref } from "vue";
import { useStore } from "vuex";
const route = useRoute();
const isConfirmDialogVisible = ref(false);
const isUserInfoEditDialogVisible = ref(false);
const isEditAddressDialogVisible = ref(false);
const scheduleDate = ref('');
const scheduleTime = ref('');
const isLoading = ref(true);
const state = reactive({
    addLabOrder: false,
    selectedTestKitId: null,
    valid: false,
    testKits: [],
    item_id: null,
  labKitList: [],
    statusItem:null
});
const getFieldRules = (fieldName, errorMessage) => {
    if (fieldName) {
        return [
            v => !!v || `${errorMessage}`,
            // Add more validation rules as needed
        ];
    }

};
const headers = [
    {
        title: "Product",
        key: "title",
    },
    {
        title: "Price",
        key: "price",
    },
    {
        title: "Quantity",
        key: "quantity",
    },
    {
        title: "status",
        key: "status",
    },

    {
        title: "Total",
        key: "total",
        sortable: false,
    },
];
const items = [{ 'key': 'Pending','value':'Pending' }, { 'key':'Processing','value':'Processing' }, { 'key':'Delivered','value':'Delivered' }, { 'key':'Canceled','value':'Canceled' },{ 'key':'Failed','value':'Failed' },{ 'key':'Refunded','value':'Refunded' }]


const headersLab = [
    {
        title: "Product",
        key: "item_name",
    },
    {
        title: "Lab Kit",
        key: "lab_kit_name",
    },
    {
        title: "Status",
        key: "status",
    },
    {
        title: "Results",
        key: "result",
    },

];
const openDialog = (item) => {

    state.item_id = item.id
    state.addLabOrder = true
};
const openPdfInNewTab = (url) => {
    window.open(url, '_blank')
}
const storeTestKit = async () => {
    await store.dispatch('saveOrderLabKitBYitems', {
        lab_kit_id: state.selectedTestKitId,
        item_id: state.item_id,
        cart_id: route.params.id
    })

    console.log('Selected Test Kit:', state.selectedTestKitId);

    state.addLabOrder = false;
    state.selectedTestKitId = null
    state.item_id = null

};
const store = useStore();
const orderData = ref(null);
const pateintDetail = ref({});
const productItems = ref([]);
const filteredOrders = computed(() => {
    let filtered = store.getters.getPatientOrderDetail;

    return filtered;
});

const formatDateActviy1 = (date) => {
    const messageDate = new Date(date);
    const dayFormatter = new Intl.DateTimeFormat('en-US', { weekday: 'long' });
    const timeFormatter = new Intl.DateTimeFormat('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
    return `${dayFormatter.format(messageDate)} ${timeFormatter.format(messageDate)}`;
};
const formatDateActviy = (date) => {
    const messageDate = new Date(date);
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return messageDate.toLocaleDateString('en-US', options).replace(/\//g, '-');
};
const formatDate = (date) => {
    const messageDate = new Date(date);
    const options = {
        year: "numeric",
        month: "numeric",
        day: "numeric",
        hour: "numeric",
        minute: "2-digit",

        hour12: false,
    };
    const formattedDate = messageDate
        .toLocaleString("en-US", options)
        .replace(/\//g, "-");
    return `${formattedDate}`;
};
const convertUtcDateTimeToLocal = (utcDate, utcTime, type) => {
    const utcDateTime = `${utcDate}T${utcTime}Z`;  // Use Z to denote UTC timezone explicitly
    const momentObj = moment.utc(utcDateTime).local();  // Convert UTC to local time

    if (type === 'date') {
        return momentObj.format('YYYY-MM-DD');  // Return local date
    } else if (type === 'time') {
        return momentObj.format('HH:mm:ss');  // Return local time
    } else {
        throw new Error("Invalid type specified. Use 'date' or 'time'.");
    }
};



function totalCallDuration(start_time, end_time) {
    console.log(start_time, end_time);
    const startMoment = moment(start_time);
    const endMoment = moment(end_time);

    // Calculate the duration
    const duration = moment.duration(endMoment.diff(startMoment));
    const hours = duration.hours();
    const thours = `${String(hours).padStart(2, "0")}`;
    const minutes = duration.minutes();
    const tminutes = `${String(minutes).padStart(2, "0")}`;
    const seconds = duration.seconds();
    const tsecond = `${String(seconds).padStart(2, "0")}`;
    let durationText;
    if (hours === 0 && minutes === 0) {
        //for second
        durationText = ` 00:00:${tsecond}`;
    } else if (hours === 0 && minutes > 0) {
        //for minutes
        durationText = `00:${tminutes}:${tsecond}`;
    } else if (hours > 0) {
        //for hours
        durationText = `${thours}:${tminutes}:${tsecond}`;
    }
    const totalDuration = durationText;
    console.log("Duration:", durationText);
    // You may need to adjust this function based on your actual data structure
    // For example, if you have separate first name and last name properties in each appointment object
    return totalDuration; // For now, just return the first name
}
const testKits = computed(async () => {
    //await store.dispatch('getLabKitProductList', {})
    // console.log(store.getters.getLabOrderProductList)

    //state.testKits = store.getters.getLabOrderProductList
});
onMounted(async () => {
    await store.dispatch("orderDetailAgent", {
        id: route.params.id,
    });
    orderData.value = store.getters.getPatientOrderDetail;
    console.log(orderData.value);
    if (orderData.value.appointment_details) {
        scheduleDate.value = getConvertedDate(
            convertUtcTime(
                orderData.value.appointment_details.appointment_time,
                orderData.value.appointment_details.appointment_date,
                orderData.value.appointment_details.timezone
            )
        );
        scheduleTime.value = getConvertedTime(
            convertUtcTime(
                orderData.value.appointment_details.appointment_time,
                orderData.value.appointment_details.appointment_date,
                orderData.value.appointment_details.timezone
            )
        );
       
    }

        // let appointmentDate = convertUtcDateTimeToLocal(orderData.value.appointment_details.appointment_date, orderData.value.appointment_details.appointment_time, 'date')
        // let appointmentTime = convertUtcDateTimeToLocal(orderData.value.appointment_details.appointment_date, orderData.value.appointment_details.appointment_time, 'time')
        // scheduleDate.value = moment(appointmentDate, "YYYY-MM-DD").format("MMMM DD, YYYY")
        // scheduleTime.value = moment(appointmentTime, "HH:mm:ss").format("hh:mm A");


        await store.dispatch('getOrderLabKit', { cart_id: route.params.id })

    state.labKitList = store.getters.getOrderLabKit
    console.log('state.testKits', state.labKitList)
    isLoading.value = false

});
const convertUtcTime = (time, date, timezone) => {
    const timezones = {
        "EST": "America/New_York",
        "CST": "America/Chicago",
        "MST": "America/Denver",
        "PST": "America/Los_Angeles",
        // Add more mappings as needed
    };

    // Get the IANA timezone identifier from the abbreviation
    const ianaTimeZone = timezones[timezone];

    if (!ianaTimeZone) {
        throw new Error(`Unknown timezone abbreviation: ${timezone}`);
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
const getStatusColor = (status) => {
    switch (status) {
        case "pending":
            return "warning";
        case "Shipped":
            return "info";
        case "Delivered":
            return "success";
        case "Cancelled":
            return "error";
        default:
            return "warning";
    }

};
const updateStatus = async (item,event) => {
  console.log(item.id, event)
  await store.dispatch('updateStatusItem', { order_id: route.params.id, item_id: item.id, status: event })
    isLoading.value = true
   await store.dispatch("orderDetailAgent", {
        id: route.params.id,
    });
    orderData.value = store.getters.getPatientOrderDetail;
  console.log(orderData.value);
  isLoading.value = false
}

const getStatusColorLabKit = (status) => {
    switch (status) {
        case "Ordered":
            return "info";
        case "Shipped":
            return "info";
        case "Delivered":
            return "success";
        case "Cancelled":
            return "red";
        case "Waiting For Results":
            return "error";
        default:
            return "gray";
    }
};

const formatCurrency = (amount) => {
    let formattedAmount = amount.toString();

    // Remove '.00' if present
    if (formattedAmount.includes('.00')) {
        formattedAmount = formattedAmount.replace('.00', '');
    }

    // Split into parts for integer and decimal
    let parts = formattedAmount.split('.');

    // Format integer part with commas
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    // Return formatted number
    return parts.join('.');
}
const formatTotalCurrency = (amount) => {
    let formattedAmount = amount.toString();

    // Remove '.00' if present
    // if (formattedAmount.includes('.00')) {
    //     formattedAmount = formattedAmount.replace('.00', '');
    // }

    // Split into parts for integer and decimal
    let parts = formattedAmount.split('.');

    // Format integer part with commas
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    // Return formatted number
    return parts.join('.');
}
</script>
<template>
    
    <div>
        <div class="d-flex justify-space-between align-center flex-wrap gap-y-4 mb-6">
            <div>
                <div class="d-flex gap-2 align-center mb-2 flex-wrap">
                    <h5 class="text-h5">Order #{{ route.params.id }}</h5>
                    <div class="d-flex gap-x-2">

                        <!-- <VChip variant="tonal" color="success" size="small">
                            Paid
                        </VChip>
                        <VChip variant="tonal" color="info" size="small">
                            Ready to Pickup
                        </VChip> -->
                    </div>
                </div>
                <div>
                    <span class="text-body-1"> </span>
                </div>
            </div>
        </div>

        <VRow v-if="filteredOrders">
            <VCol cols="12" md="8">
                <!-- 👉 Order Details -->
                <VCard class="mb-6">
                    <VCardItem>
                        <template #title>
                            <h5>Order Details</h5>

                        </template>
                    </VCardItem>
                    <div class="table-container">
                        <VDataTable :headers="headers" :items="filteredOrders.order_items.items"
                            item-value="productName" class="text-no-wrap " :loading="isLoading">
                            <template #item.title="{ item }">
                                <div class="d-flex gap-x-3">
                                    <VAvatar size="34" variant="tonal" :image="item.image_url" rounded />

                                    <div class="d-flex flex-column text-left">
                                        <h5 style="margin-bottom: 0px; font-size: 0.83em; white-space: normal;word-break: break-word;" >
                                            {{ item.title }}
                                        </h5>

                                        <span class="text-sm text-start align-self-start">
                                            {{ item.list_sub_title }}
                                        </span>
                                    </div>
                                </div>
                            </template>

                            <template #item.price="{ item }">
                                <span>${{ item.price }}</span>
                            </template>

                            
                            <template #item.status="{ item }" >
                                <span>
                                   
                                    <VSelect
                                      :items="items"
                                      v-model="item.status"
                                      label="Status"
                                      placeholder="Select Status"
                                       density="compact"
                                      style="width: 150px;"
                                      item-title="key"
                                      item-value="value"
                                      @update:model-value="updateStatus(item,$event)"
                                    />
                                    
                                  </span>
                            </template>

                            <template #item.total="{ item }">

                                <span> ${{ parseFloat(item.price * item.quantity).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) }} </span>
                            </template>
                            <template #bottom />
                        </VDataTable>
                    </div>
                    <VDivider />

                    <VCardText>
                        <div class="d-flex align-end flex-column">
                            <table class="text-high-emphasis">
                                <tbody>
                                    <tr>
                                        <td width="200px">Subtotal:</td>
                                        <td class="font-weight-medium">

                                            ${{
        formatTotalCurrency(parseFloat(
            filteredOrders.order_items
                .total_amount
        ).toFixed(2))
    }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Shipping fee:</td>
                                        <td class="font-weight-medium">
                                            ${{
            parseFloat(
                filteredOrders.order_items
                    .total_shipping_cost
            ).toFixed(2)
        }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="font-weight-medium">
                                            Total:
                                        </td>
                                        <td class="font-weight-medium">
                                            ${{
            formatTotalCurrency(parseFloat(
                filteredOrders.order_items
                    .total
            ).toFixed(2))
        }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </VCardText>
                </VCard>
                <v-dialog v-model="state.addLabOrder" max-width="400">
                    <v-card>
                        <v-card-title>Add Lab Kit</v-card-title>
                        <v-card-text>
                            <v-form ref="form" v-model="state.valid" class="mt-1">
                                <v-row v-if="testKits">
                                    <v-col cols="12" md="12">
                                        <v-autocomplete label="Test Kit" v-model="state.selectedTestKitId"
                                            style="column-gap: 0px;" :items="state.testKits" item-title="name"
                                            item-value="id"
                                            :rules="getFieldRules('Test Kit', 'Test Kit is required')"></v-autocomplete>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="primary" text @click="state.addLabOrder = false">Cancel</v-btn>
                            <v-btn color="primary" @click="storeTestKit" :disabled="!state.valid">Save</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
                <!-- 👉 Shipping Activity -->
               

                <VCard title="Lab Kits" v-if="state.labKitList.length > 0" class="mb-6">
                    <VCardText>
                        <div class="table-container">
                            <VDataTable :headers="headersLab" :loading="isLoading" :items="state.labKitList"
                                class="text-no-wrap ">
                                <template #item.item_name="{ item }">
                                    <div class="d-flex gap-x-3">

                                        <div class="d-flex flex-column align-center">
                                            <h5 style="margin-bottom: 0px;">
                                                {{ item.item_name }}
                                            </h5>

                                        </div>
                                    </div>
                                </template>
                                <template #item.lab_kit_name="{ item }">
                                    <div class="d-flex gap-x-3">

                                        <div class="d-flex flex-column align-center">
                                            <h5 style="margin-bottom: 0px;">
                                                {{ item.lab_kit_name }}
                                            </h5>

                                        </div>
                                    </div>
                                </template>


                                <template #item.status="{ item }">
                                    <span>
                                        <VChip variant="tonal" :color="getStatusColorLabKit(item.status)" size="small">
                                            {{ item.status }}
                                        </VChip>
                                    </span>
                                </template>
                                <template #item.result="{ item }">
                                    <span v-if="item.result">
                                        <a href="#" @click="openPdfInNewTab(item.result)" target="_blank"
                                            class="custom-link">
                                            <div class="d-inline-flex align-center">
                                                <img :src="pdf" height="20" class="me-2" alt="img">
                                                <span class="app-timeline-text font-weight-medium">
                                                    results.pdf
                                                </span>
                                            </div>
                                        </a>

                                    </span>
                                    <span v-else>
                                        Waiting For Result
                                    </span>
                                </template>


                                <template #bottom />
                            </VDataTable>
                        </div>
                    </VCardText>
                </VCard>
 <VCard title="Shipping Activity" class="mb-6" >
                    <VCardText>
                        <VTimeline truncate-line="both" align="start" side="end" line-inset="10" line-color="primary"
                            density="compact" class="v-timeline-density-compact">
                            <VTimelineItem dot-color="primary" size="x-small"
                                v-for="item in filteredOrders.items_activity" :key="item.id">
                                <div class="d-flex justify-space-between align-center mb-3">
                                    <span class="app-timeline-title"> {{ item.note }}</span>
                                    <span class="app-timeline-meta">{{ formatDateActviy(item.created_at) }}</span>
                                </div>
                                <p class="app-timeline-text mb-0">
                                    {{ item.item_name }} {{ item.short_description }}
                                </p>
                            </VTimelineItem>


                        </VTimeline>
                    </VCardText>
                </VCard>
            </VCol>

            <VCol cols="12" md="4">
                <VCard class="mb-6" v-if="filteredOrders.appointment_details">
                    <VCardText>
                        <div class="d-flex align-center justify-space-between gap-1 mb-6">
                            <div class="text-body-1 text-high-emphasis font-weight-medium">
                                <v-icon class="mr-2" color="primary">ri-calendar-event-line</v-icon>
                                Appointment Details
                            </div>
                        </div>
                        <div class="appointment-details">
                            <div class="detail-item">
                                <span class="detail-label">Appointment At:</span>
                                <span class="detail-value">{{ scheduleDate + ' ' + scheduleTime }}</span>
                            </div>
                            <div class="detail-item"
                                v-if="filteredOrders.appointment_details.start_time && filteredOrders.appointment_details.end_time">
                                <span class="detail-label">Start Time:</span>
                                <span class="detail-value">{{
        formatDate(filteredOrders.appointment_details.start_time)
    }}</span>
                            </div>
                            <div class="detail-item"
                                v-if="filteredOrders.appointment_details.start_time && filteredOrders.appointment_details.end_time">
                                <span class="detail-label">End Time:</span>
                                <span class="detail-value">{{
        formatDate(filteredOrders.appointment_details.end_time)
    }}</span>
                            </div>
                            <div class="detail-item"
                                v-if="filteredOrders.appointment_details.start_time && filteredOrders.appointment_details.end_time">
                                <span class="detail-label">Duration:</span>
                                <span class="detail-value">{{
        totalCallDuration(filteredOrders.appointment_details.start_time,
            filteredOrders.appointment_details.end_time) }}</span>
                            </div>
                        </div>
                    </VCardText>
                </VCard>
                <!-- 👉 Customer Details  -->
                <VCard class="mb-6" v-if="filteredOrders.patient_details">
                    <VCardText class="d-flex flex-column gap-y-6">
                        <h3>Patient Details</h3>

                        <div class="d-flex align-center">
                            <VAvatar :image="avatar1" class="me-3" />
                            
                            <div>
                                <div class="text-body-1 text-high-emphasis font-weight-medium">
                                    {{
        filteredOrders.patient_details.first_name + ' ' +
                                    filteredOrders.patient_details.last_name
                                    }}

                                </div>

                            </div>
                        </div>

                        <div class="d-flex align-center" style="display: none;">
                            <VAvatar variant="tonal" color="success" class="me-3" style="display: none;">
                                <VIcon icon="ri-shopping-cart-line" />
                            </VAvatar>

                            <h4 style="display: none;">
                                {{ filteredOrders.order_items.total_products }}
                                Products
                            </h4>
                        </div>

                        <div class="d-flex flex-column gap-y-1">
                            <div class="d-flex justify-space-between gap-1 text-body-2">
                                <h5>Contact Info</h5>
                            </div>

                            <span>Email:
                                {{ filteredOrders.patient_details.email }}</span>
                            <span>Mobile:
                                {{ filteredOrders.patient_details.phone_no }}</span>
                        </div>
                    </VCardText>
                </VCard>

                <!-- 👉 Shipping Address -->
                <VCard class="mb-6">
                    <VCardText>
                        <div class="d-flex align-center justify-space-between gap-1 mb-6">
                            <div class="text-body-1 text-high-emphasis font-weight-medium">
                                <v-icon class="mr-2" color="primary">ri-truck-line</v-icon>
                                Shipping Address
                            </div>
                            <!-- <span
                                class="text-base text-primary font-weight-medium cursor-pointer"
                                @click="
                                    isEditAddressDialogVisible =
                                        !isEditAddressDialogVisible
                                "
                                >Edit</span
                            > -->
                        </div>
                        <div>
                            {{ filteredOrders.order_details.shipping_address1 }}
                            <br />
                            {{ filteredOrders.order_details.shipping_city }}
                            <br />
                            {{ filteredOrders.order_details.shipping_state }},
                            {{ filteredOrders.order_details.shipping_zipcode }}
                            <br />
                            {{ filteredOrders.order_details.shipping_country }}
                        </div>
                    </VCardText>
                </VCard>

                <!-- 👉 Billing Address -->
                <VCard style="display: none;">
                    <VCardText>
                        <div class="d-flex align-center justify-space-between gap-1 mb-3">
                            <div class="text-body-1 text-high-emphasis font-weight-medium">
                                Billing Address
                            </div>
                            <!-- <span
                                class="text-base text-primary font-weight-medium cursor-pointer"
                                @click="
                                    isEditAddressDialogVisible =
                                        !isEditAddressDialogVisible
                                "
                                >Edit</span
                            > -->
                        </div>
                        <div>
                            {{ filteredOrders.order_details.billing_address1 }}
                            <br />
                            {{ filteredOrders.order_details.billing_city }}
                            <br />
                            {{ filteredOrders.order_details.billing_state }},
                            {{ filteredOrders.order_details.billing_zipcode }}
                            <br />
                            {{ filteredOrders.order_details.billing_country }}
                        </div>

                        <!-- <div class="mt-6">
                            <h6 class="text-h6 mb-1">Mastercard</h6>
                            <div class="text-base">Card Number: ******4291</div>
                        </div> -->
                    </VCardText>
                </VCard>
            </VCol>
        </VRow>

        <!-- <ConfirmDialog
            v-model:isDialogVisible="isConfirmDialogVisible"
            confirmation-question="Are you sure to cancel your Order?"
            cancel-msg="Order cancelled!!"
            cancel-title="Cancelled"
            confirm-msg="Your order cancelled successfully."
            confirm-title="Cancelled!"
        /> -->

        <!-- <UserInfoEditDialog
            v-model:isDialogVisible="isUserInfoEditDialogVisible"
        />

        <AddEditAddressDialog
            v-model:isDialogVisible="isEditAddressDialogVisible"
        /> -->
    </div>
</template>
<style scoped>
.appointment-details {
    display: flex;
    flex-direction: column;
}

.detail-item {
    display: flex;
    margin-bottom: 10px;
}

.detail-label {
    font-weight: bold;
    min-width: 120px;
}

.detail-value {
    flex: 1;
}

::-webkit-scrollbar {
    width: 10px;
    /* Width of the scrollbar */
}

/* Track */
::-webkit-scrollbar-track {
    background: #f1f1f1;
    /* Color of the track */
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: #888;
    /* Color of the handle */
    border-radius: 5px;
    /* Roundness of the handle */
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: #555;
    /* Color of the handle on hover */
}
</style>
