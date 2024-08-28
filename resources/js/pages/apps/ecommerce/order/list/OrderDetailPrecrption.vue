<script setup>
import AddPrescrption from '@/pages/apps/ecommerce/order/list/AddPrescrption.vue';
import EditPrescrption from '@/pages/apps/ecommerce/order/list/EditPrescrption.vue';

import { computed, onMounted, ref } from "vue";
import { useRoute, useRouter } from 'vue-router';
import { useStore } from "vuex";
const store = useStore();
const props = defineProps({
    orderData: {
        type: Object,
        required: true,
    },
})
const isAddCustomerDrawerOpen = ref(false)
const isEditCustomerDrawerOpen = ref(false)
const editedItem = ref()
const deleteDialog = ref(false)
const router = useRouter()
const route = useRoute()
const patient_id = ref()
const itemsPrescriptions = ref([]);
// const patientId = route.params.patient_id;
const prescriptionLoaded = ref(false)
const doctorName = ref('');
const prescription = computed(async () => {
    await fetchPrescriptions()
    return prescriptionLoaded.value ? itemsPrescriptions.value : null
})
const fetchPrescriptions = async () => {
    store.dispatch('updateIsLoading', true)
    await getprescriptionList()
    doctorName.value = props.orderData.appointment_details.provider_name
    store.dispatch('updateIsLoading', false)
    prescriptionLoaded.value = true
}
const getprescriptionList = async () => {

  let prescriptions = props.orderData.prescription;
    console.log('edit item',prescriptions)
    // itemsPrescriptions.value = store.getters.getPrescriptionList
    for (let data of prescriptions) {
        let dataObject = {}
        dataObject.brand = data.brand
        dataObject.direction_one = data.direction_one
        dataObject.direction_quantity = data.direction_quantity
        dataObject.direction_two = data.direction_two
        dataObject.date = formatDateDate(data.created_at)
        dataObject.dosage = data.dosage
        dataObject.from = data.from
        dataObject.name = data.name
        dataObject.quantity = data.quantity
        dataObject.refill_quantity = data.refill_quantity
        dataObject.status = data.status
        dataObject.comments = data.comments
        dataObject.created_by = data.created_by
        dataObject.prescription_date=data.created_at
        itemsPrescriptions.value.push(dataObject)
    }
    itemsPrescriptions.value.sort((a, b) => {
        return b.id - a.id;
    });
    console.log("itemsPrescriptions", itemsPrescriptions.value);

};
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: 'numeric'
    })
}
const getStatusColor = (status) => {
    switch (status) {
        case 'pending':
            return 'warning'; // Use Vuetify's warning color (typically yellow)
        case 'shipped':
            return '#45B8AC'; // Use Vuetify's primary color (typically blue)
        case 'delivered':
            return 'green';
        case 'returned':
            return 'red';
        case 'results':
            return 'blue';
        default:
            return 'grey'; // Use Vuetify's grey color for any other status
    }
};
onMounted(async () => {
    let prescriptions = props.orderData.prescription;
  console.log('props.orderData', props.orderData.prescription)
    patient_id.value= props.orderData.patient_details.id
    itemsPrescriptions.value = prescriptions
});
const editItem = async (item) => {
  isEditCustomerDrawerOpen.value = true

  await store.dispatch('getOrderDetailPercrptionByID', {
    id: item.patient_prescription_id,
  })

  editedItem.value = store.getters.getOrderDetailPrecriptionList
  console.log(store.getters.getOrderDetailPrecriptionList)
  // editDialog.value = true
}
const deleteItem = item => {
  
  console.log('del', item)
  deleteDialog.value = true
  editedItem.value=item
}
const deleteItemConfirm = async () => {
  console.log('editedIndex.value', editedItem.value.patient_prescription_id)
  await store.dispatch('deleteOrderDetailPerscription', {
    id: editedItem.value.patient_prescription_id
  })
  closeDelete()
}
const closeDelete = async() => {
  
    deleteDialog.value = false
    await handlePatientAdded('success')
}
const handlePatientAdded = async (msg) => {
  if (msg == 'success') {

 store.dispatch("updateIsLoading", true);
    await store.dispatch("orderDetailAgent", {
        id: route.params.id,
    });
    let orderData = store.getters.getPatientOrderDetail;
    itemsPrescriptions.value =orderData.prescription
    
     store.dispatch("updateIsLoading", false);
   
  }
}
</script>
<template>
      <VCol cols="12" md="3" v-if="$can('read', 'Prescription Add')">
              <VBtn prepend-icon="ri-add-line" @click="isAddCustomerDrawerOpen = !isAddCustomerDrawerOpen">
                Add Prescription
              </VBtn>
            </VCol>  
    <template v-if="itemsPrescriptions.length > 0">
        <v-row>
            <v-col v-for="prescription in itemsPrescriptions" :key="prescription.id" cols="12" md="4">
          
                <v-card class="mx-auto mb-4" elevation="4" hover>
                    <v-img height="200" src="https://cdn.pixabay.com/photo/2016/11/23/15/03/medication-1853400_1280.jpg"
                        class="white--text align-end" gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)">
                        <v-card-title class="text-h5" style="color: #fff;">{{ prescription.prescription_name
                            }}</v-card-title>
                    </v-img>

                    <v-card-text>



                        <v-chip :color="getStatusColor(prescription.status)" text-color="white" small class="mr-2">
                            {{ prescription.status }}
                        </v-chip>
                         <IconBtn
                        size="small"
                        
                        @click="editItem(prescription)"
                        v-if="$can('read', 'Prescription Edit')"
                      >
                      
                        <VIcon icon="ri-pencil-line" />
                      </IconBtn>
                        <IconBtn
                            size="small"
                            @click="deleteItem(prescription)"
                            v-if="$can('read', 'Prescription Delete')"
                          >
                            <VIcon icon="ri-delete-bin-line" />
                          </IconBtn>     
                    </v-card-text>

                    <v-divider class="mx-4"></v-divider>

                    <v-card-text>
                        <v-row dense>
                            <v-col cols="6">
                                <v-icon small color="primary">ri-capsule-line</v-icon>
                                <span class="ml-1">Dosage:{{ prescription.dosage }}</span>
                            </v-col>
                            <v-col cols="6">
                                <v-icon small color="primary">ri-medicine-bottle-line</v-icon>
                                <span class="ml-1">Quantity:{{ prescription.quantity }}</span>
                            </v-col>
                            <v-col cols="6">
                                <v-icon small color="primary">ri-calendar-line</v-icon>
                                <span class="ml-1">{{ formatDate(prescription.prescription_date) }}</span>
                            </v-col>
                            <v-col cols="6">
                                <v-icon small color="primary">ri-time-line</v-icon>
                                <span class="ml-1">{{ formatTime(prescription.prescription_date) }}</span>
                            </v-col>
                        </v-row>
                    </v-card-text>

                    <v-card-actions>
                        <v-btn color="primary" text>
                            More Details
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn @click="prescription.show = !prescription.show">
                        {{ prescription.show ? '' : '' }}
                        <v-icon right color="primary">
                          {{ prescription.show ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line' }}
                        </v-icon>
                      </v-btn>
                    </v-card-actions>

                    <v-expand-transition>
                        <div v-show="prescription.show">
                            <v-divider></v-divider>
                            <v-card-text>
                                <v-row dense>
                                    <v-col cols="12">
                                        <strong>Add By:</strong> {{ prescription.created_by}}
                                    </v-col>
                                    <v-col cols="12">
                                        <strong>Brand:</strong> {{ prescription.brand }}
                                    </v-col>
                                    <v-col cols="12">
                                        <strong>From:</strong> {{ prescription.from }}
                                    </v-col>
                                    <v-col cols="12">
                                        <strong>Direction One:</strong> {{ prescription.direction_one }}
                                    </v-col>
                                    <v-col cols="12">
                                        <strong>Direction Two:</strong> {{ prescription.direction_two }}
                                    </v-col>
                                    <v-col cols="12">
                                        <strong>Refill Quantity:</strong> {{ prescription.refill_quantity }}
                                    </v-col>
                                    <v-col cols="12">
                                        <strong>Direction Quantity:</strong> {{ prescription.direction_quantity }}
                                    </v-col>
                                    <v-col cols="12" v-if="prescription.comments">
                                        <strong>Comments:</strong> {{ prescription.comments }}
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </div>
                    </v-expand-transition>
                </v-card>
            </v-col>
        </v-row>
        <VExpansionPanels variant="accordion" style="display: none;">
            <VExpansionPanel v-for="(item, index) in itemsPrescriptions" :key="index">
                <div>

                    <VExpansionPanelTitle collapse-icon="mdi-chevron-down" expand-icon="mdi-chevron-right"
                        style="margin-left: 0px !important;">
                        <p class=""><b> {{ item.name }}</b>
                            <br />
                        <div class=" pt-2"> {{ doctorName }}</div>
                        <div class=" pt-2">{{ item.date }}</div>
                        </p>


                        <v-row>

                        </v-row>

                        <span class="v-expansion-panel-title__icon badge text-warning"
                            v-if="item.status == null">Pending</span>
                        <span class="v-expansion-panel-title__icon badge" v-else>
                            <v-chip :color="getStatusColor(item.status)" label size="small" variant="text">
                                {{ item.status }}
                            </v-chip></span>
                    </VExpansionPanelTitle>
                    <VExpansionPanelText class="pt-0">

                        <v-row class='mt-1'>
                            <v-col cols="12" md="4" sm="6">
                                <p class='heading'><b>Brand:</b></p>
                            </v-col>
                            <v-col cols="12" md="4" sm="6">
                                <p>{{ item.brand }}</p>
                            </v-col>
                        </v-row>
                        <v-row class='mt-1'>
                            <v-col cols="12" md="4" sm="6">
                                <p class='heading'><b>From:</b></p>
                            </v-col>
                            <v-col cols="12" md="4" sm="6">
                                <p>{{ item.from }}</p>
                            </v-col>
                        </v-row>
                        <v-row class='mt-1'>
                            <v-col cols="12" md="4" sm="6">
                                <p class='heading'><b>Dosage:</b></p>
                            </v-col>
                            <v-col cols="12" md="4" sm="6">
                                <p>{{ item.dosage }}</p>
                            </v-col>
                        </v-row>
                        <v-row class='mt-1'>
                            <v-col cols="12" md="4" sm="6">
                                <p class='heading'><b>Quantity:</b></p>
                            </v-col>
                            <v-col cols="12" md="4" sm="6">
                                <p>{{ item.quantity }}</p>
                            </v-col>
                        </v-row>
                        <v-row class='mt-1'>
                            <v-col cols="12" md="4" sm="6">
                                <p class='heading'><b>Direction Quantity:</b></p>
                            </v-col>
                            <v-col cols="12" md="8" sm="6">
                                <p>{{ item.direction_quantity }}</p>
                            </v-col>
                        </v-row>
                        <v-row class='mt-1'>
                            <v-col cols="12" md="4" sm="6">
                                <p class='heading'><b>Direction One:</b></p>
                            </v-col>
                            <v-col cols="12" md="8" sm="6">
                                <p>{{ item.direction_one }} </p>
                            </v-col>
                        </v-row>
                        <v-row class='mt-1'>
                            <v-col cols="12" md="4" sm="6">
                                <p class='heading'><b>Direction Two:</b></p>
                            </v-col>
                            <v-col cols="12" md="8" sm="6">
                                <p>{{ item.direction_two }} </p>
                            </v-col>
                        </v-row>
                        <v-row class='mt-1'>
                            <v-col cols="12" md="4" sm="6">
                                <p class='heading'><b>Refill Quantity:</b></p>
                            </v-col>
                            <v-col cols="12" md="8" sm="6">
                                <p>{{ item.refill_quantity }}</p>
                            </v-col>

                        </v-row>
                        <v-row class='mt-1'>
                            <v-col cols="12" md="4" sm="6">
                                <p class='heading'><b>Status:</b></p>
                            </v-col>
                            <v-col cols="12" md="8" sm="6">
                                <p v-if="item.status == null" class="text-warning">Pending</p>
                                <p v-else>{{ item.status }}</p>
                            </v-col>
                        </v-row>
                        <v-row class='mt-1'>
                            <v-col cols="12" md="4" sm="6">
                                <p class='heading'><b>Comments:</b></p>
                            </v-col>
                            <v-col cols="12" md="8" sm="8">
                                <p>{{ item.comments }} </p>
                            </v-col>

                        </v-row>

                    </VExpansionPanelText>
                </div>


            </VExpansionPanel>
            <br />
        </VExpansionPanels>
    </template>
    <template v-else="prescriptionLoaded">
        <VCard>
            <VCard>
                <VAlert border="start" color="primary" variant="tonal">
                    <div class="text-center">No data found</div>
                </VAlert>

            </VCard>
        </VCard>

    </template>
<AddPrescrption v-model:is-drawer-open="isAddCustomerDrawerOpen" :patient-id="patient_id" @patientAdded="handlePatientAdded" />
<EditPrescrption v-model:is-drawer-open="isEditCustomerDrawerOpen" :patient-id="patient_id"  :user-data="store.getters.getOrderDetailPrecriptionList"  @patientAdded="handlePatientAdded" />
  <VDialog v-model="deleteDialog" max-width="500px">
    <VCard>
      <VCardTitle>
        Are you sure you want to delete this item?
      </VCardTitle>

      <VCardActions>
        <VSpacer />

        <VBtn color="error" variant="outlined" @click="closeDelete">
          Cancel
        </VBtn>

        <VBtn color="success" variant="elevated" @click="deleteItemConfirm">
          OK
        </VBtn>

        <VSpacer />
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style lang="scss">
button.v-expansion-panel-title {
    background-color: rgb(var(--v-theme-yellow)) !important;
    color: #fff;
}

button.v-expansion-panel-title.bg-secondary {
    background-color: rgb(var(--v-theme-yellow)) !important;
    color: #fff;
}

button.v-expansion-panel-title {
    background-color: rgb(var(--v-theme-yellow)) !important;
    color: #fff;
}

span.v-expansion-panel-title__icon {
    margin-left: 0px !important;
}

span.v-expansion-panel-title__icon {
    color: #fff
}

.v-expansion-panel {
    background-color: #fff;
    border-radius: 16px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    margin-bottom: 10px;
    overflow: hidden;
    transition: box-shadow 0.3s ease;
}
</style>
