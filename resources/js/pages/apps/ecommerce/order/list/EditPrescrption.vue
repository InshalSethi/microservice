<script setup>
import { useRoute } from 'vue-router';
import { PerfectScrollbar } from 'vue3-perfect-scrollbar';
import { VForm } from 'vuetify/components/VForm';
import { useStore } from 'vuex';
const route = useRoute();
const isMobile = ref(window.innerWidth <= 768);
const store = useStore()
const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  userData: {
    type: Object,
    required: true,
  },
  patientId: {
    type: Number,
    required: true,
  }
})
const states = ref([
    { name: 'Alabama', abbreviation: 'AL' },
    { name: 'Alaska', abbreviation: 'AK' },
    { name: 'Arizona', abbreviation: 'AZ' },
    { name: 'Arkansas', abbreviation: 'AR' },
    { name: 'Howland Island', abbreviation: 'UM-84' },
    { name: 'Delaware', abbreviation: 'DE' },
    { name: 'Maryland', abbreviation: 'MD' },
    { name: 'Baker Island', abbreviation: 'UM-81' },
    { name: 'Kingman Reef', abbreviation: 'UM-89' },
    { name: 'New Hampshire', abbreviation: 'NH' },
    { name: 'Wake Island', abbreviation: 'UM-79' },
    { name: 'Kansas', abbreviation: 'KS' },
    { name: 'Texas', abbreviation: 'TX' },
    { name: 'Nebraska', abbreviation: 'NE' },
    { name: 'Vermont', abbreviation: 'VT' },
    { name: 'Jarvis Island', abbreviation: 'UM-86' },
    { name: 'Hawaii', abbreviation: 'HI' },
    { name: 'Guam', abbreviation: 'GU' },
    { name: 'United States Virgin Islands', abbreviation: 'VI' },
    { name: 'Utah', abbreviation: 'UT' },
    { name: 'Oregon', abbreviation: 'OR' },
    { name: 'California', abbreviation: 'CA' },
    { name: 'New Jersey', abbreviation: 'NJ' },
    { name: 'North Dakota', abbreviation: 'ND' },
    { name: 'Kentucky', abbreviation: 'KY' },
    { name: 'Minnesota', abbreviation: 'MN' },
    { name: 'Oklahoma', abbreviation: 'OK' },
    { name: 'Pennsylvania', abbreviation: 'PA' },
    { name: 'New Mexico', abbreviation: 'NM' },
    { name: 'American Samoa', abbreviation: 'AS' },
    { name: 'Illinois', abbreviation: 'IL' },
    { name: 'Michigan', abbreviation: 'MI' },
    { name: 'Virginia', abbreviation: 'VA' },
    { name: 'Johnston Atoll', abbreviation: 'UM-67' },
    { name: 'West Virginia', abbreviation: 'WV' },
    { name: 'Mississippi', abbreviation: 'MS' },
    { name: 'Northern Mariana Islands', abbreviation: 'MP' },
    { name: 'United States Minor Outlying Islands', abbreviation: 'UM' },
    { name: 'Massachusetts', abbreviation: 'MA' },
    { name: 'Connecticut', abbreviation: 'CT' },
    { name: 'Florida', abbreviation: 'FL' },
    { name: 'District of Columbia', abbreviation: 'DC' },
    { name: 'Midway Atoll', abbreviation: 'UM-71' },
    { name: 'Navassa Island', abbreviation: 'UM-76' },
    { name: 'Indiana', abbreviation: 'IN' },
    { name: 'Wisconsin', abbreviation: 'WI' },
    { name: 'Wyoming', abbreviation: 'WY' },
    { name: 'South Carolina', abbreviation: 'SC' },
    { name: 'South Dakota', abbreviation: 'SD' },
    { name: 'Montana', abbreviation: 'MT' },
    { name: 'North Carolina', abbreviation: 'NC' },
    { name: 'Palmyra Atoll', abbreviation: 'UM-95' },
    { name: 'Puerto Rico', abbreviation: 'PR' },
    { name: 'Colorado', abbreviation: 'CO' },
    { name: 'Missouri', abbreviation: 'MO' },
    { name: 'New York', abbreviation: 'NY' },
    { name: 'Maine', abbreviation: 'ME' },
    { name: 'Tennessee', abbreviation: 'TN' },
    { name: 'Georgia', abbreviation: 'GA' },
    { name: 'Louisiana', abbreviation: 'LA' },
    { name: 'Nevada', abbreviation: 'NV' },
    { name: 'Iowa', abbreviation: 'IA' },
    { name: 'Idaho', abbreviation: 'ID' },
    { name: 'Rhode Island', abbreviation: 'RI' },
    { name: 'Washington', abbreviation: 'WA' },
    { name: 'Ohio', abbreviation: 'OH' },
    // ... (add the rest of the states)
]);
const checkMobile = () => {
    isMobile.value = window.innerWidth <= 768;
};
const prescription_id = ref();
const sortedStates = computed(() => {
    return states.value.slice().sort((a, b) => {
        return a.name.localeCompare(b.name);
    });
});
const valid = ref(false);
const form = ref(null);
const getFieldRules = (fieldName, errorMessage) => {
    if (fieldName) {
        return [
            (v) => !!v || `${errorMessage}`,
            // Add more validation rules as needed
        ];
    }
};
const headers = [

    { key: 'name', title: 'Name' },
    { key: 'brand', title: 'Brand' },
    { key: 'from', title: 'From' },
    { key: 'direction_quantity', title: 'Direction Quantity' },
    { key: 'dosage', title: 'Dosage' },
    { key: 'quantity', title: 'Quantity' },
    { key: 'refill_quantity', title: 'Refill Quantity' },
    { key: 'actions', title: 'Action' },
];
const openDialog = (user, type) => {
    console.log("userId", user);
 
  
    if (type == "Prescription") {
        console.log("enter ne value");
        prescriptionModel.value = true;
    }
    if (type == "Prescription Form") {
        console.log("enter ne value");
        prescriptionModelForm.value = true;
    }
 
};
const itemId = ref()
const prescriptionForm = async () => {
    console.log("toggelUserValue.value.", prescription_id.value);
    // isLoadingVisible.value = true
    if (form.value.validate()) {
  
        console.log(prescription_id.value,route.params.id)
      await store.dispatch("updatePercriptionOrderDetail", {
          id:itemId.value,
            medicines: medicines.value,
        prescription_id: prescription_id.value,
            patient_id:props.patientId,
            order_id:route.params.id,
            brand: brand.value,
            from: from.value,
            dosage: dosage.value,
            quantity: quantity.value,
            direction_quantity: direction_quantity.value,
            direction_one: direction_one.value,
            direction_two: direction_two.value,
            refill_quantity: refil_quantity.value,
            dont_substitute: dont_substitute.value,
            comments: comments.value,
         });
        if (!store.getters.getErrorMsg) {
      emit('patientAdded', 'success')
               prescriptionModel.value = false;
                medicines.value = null;
                prescription_id.value = null;
             
                brand.value = null;
                from.value = null;
                dosage.value = null;
                quantity.value = null;
                direction_quantity.value = null;
                direction_one.value = null;
                direction_two.value = null;
                refil_quantity.value = null;
                dont_substitute.value = null;
                comments.value = null;
                emit('update:isDrawerOpen', false)
    }
   
      
      
       
       
    }
};

const emit = defineEmits(['update:isDrawerOpen','patientAdded'])

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}
const genders = ref([
  { name: 'Male', abbreviation: 'Male' },
  { name: 'Female', abbreviation: 'Female' },
  { name: 'Other', abbreviation: 'Other' },
]);
const prescriptionModel = ref(false);
const prescriptionModelForm = ref(false);
const selectedMedicines = ref([]);
const toggelUserValue = ref(null)
const medicines = ref("");
const brand = ref("");
const from = ref("");
const dosage = ref("");
const quantity = ref("");
const direction_quantity = ref("");
const direction_one = ref("");
const direction_two = ref("");
const refil_quantity = ref("");
const dont_substitute = ref("");
const comments = ref("");
const search = ref("");
const loading = ref(true);
const page = ref(1);
const itemsPerPage = ref(10);
const pageCount = ref(0);
const itemsPrescriptions = ref([]);




const selectedItem = async (item) => {
    console.log(item)
    medicines.value = item.name
    brand.value = item.brand
    dosage.value = item.dosage
    dosage.value = item.dosage
    from.value = item.from
    quantity.value = item.quantity
    direction_quantity.value = item.direction_quantity
    refil_quantity.value = item.refill_quantity
    prescription_id.value = item.id
    prescriptionModelForm.value = false

}
  onMounted(async () => {
    window.addEventListener("resize", checkMobile);
      await store.dispatch('orderPrecriptionList')
    itemsPrescriptions.value = store.getters.getOrderPrecriptionList
    console.log(itemsPrescriptions.value)
    loading.value=false
});
const resetForm = () => {
  refVForm.value?.reset()
  emit('update:isDrawerOpen', false)
}
const getPrescrption = computed(async () => {
  if (props.userData) {
      itemId.value=props.userData.id
    medicines.value = props.userData.prescription.name
       prescription_id.value=props.userData.prescription_id
            brand.value=props.userData.brand
            from.value= props.userData.from
            dosage.value= props.userData.dosage
            quantity.value= props.userData.quantity
            direction_quantity.value = props.userData.direction_quantity
             direction_one.value= props.userData.direction_one,
            direction_two.value= props.userData.direction_two,
            comments.value= props.userData.comments,
            refil_quantity.value= props.userData.refill_quantity
            dont_substitute.value= props.userData.dont_substitute
  }
        
});
</script>

<template>
  <VNavigationDrawer
    :model-value="props.isDrawerOpen"
    temporary
    location="end"
    width="800"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Edit Prescription"
      @cancel="$emit('update:isDrawerOpen', false)"
    />
    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);" v-if="getPrescrption">
          <VForm
            ref="refVForm"
            @submit.prevent=""
          >
      
             
           
            <v-form ref="form" v-model="valid" class="mt-6">
                <v-row>
                    <v-col cols="12" md="2" v-if="isMobile">
                        <v-btn
                            color="primary"
                            class="btn"
                            style="height: 54px"
                            @click.stop="
                                openDialog(toggelUserValue, 'Prescription Form')
                            "
                        >
                            Prescription
                        </v-btn>
                    </v-col>
                    <v-col cols="12" md="10">
                        <v-text-field
                            label="Medicine"
                            :rules="
                                getFieldRules(
                                    'Medicine',
                                    'Medicine is required'
                                )
                            "
                            v-model="medicines"
                            required
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="2" v-if="!isMobile">
                        <v-btn
                            color="primary"
                            class="btn"
                            style="height: 54px"
                            @click.stop="
                                openDialog(toggelUserValue, 'Prescription Form')
                            "
                        >
                            Prescription
                        </v-btn>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field
                            label="Brand"
                            :rules="getFieldRules('brand', 'Brand is required')"
                            v-model="brand"
                            required
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            label="From"
                            :rules="getFieldRules('from', 'From is required')"
                            v-model="from"
                            required
                        ></v-text-field>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field
                            label="Dosage"
                            :rules="
                                getFieldRules('dosage', 'Dosage is required')
                            "
                            v-model="dosage"
                            required
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            label="Quantity"
                            :rules="
                                getFieldRules(
                                    'quantity',
                                    'Quantity is required'
                                )
                            "
                            v-model="quantity"
                            required
                        ></v-text-field>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field
                            label="Direction Quantity"
                            :rules="
                                getFieldRules(
                                    'direction quantity',
                                    'Direction Quantity is required'
                                )
                            "
                            v-model="direction_quantity"
                            required
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            label="Refil Quantity"
                            :rules="
                                getFieldRules(
                                    'Refil Quantity',
                                    'Refil Quantity one is required'
                                )
                            "
                            v-model="refil_quantity"
                            required
                        ></v-text-field>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field
                            label="Direction one"
                            :rules="
                                getFieldRules('', 'Direction one is required')
                            "
                            v-model="direction_one"
                            required
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            label="Direction Two"
                            :rules="
                                getFieldRules('', 'Direction Two is required')
                            "
                            v-model="direction_two"
                            required
                        ></v-text-field>
                    </v-col>
                </v-row>

                <v-row style="margin-bottom: 5px">
                    <v-col cols="12" md="12">
                        <!-- <v-text-field label="Comments" :rules="getFieldRules('', 'Comments is required')" v-model="comments"
              required></v-text-field> -->
                        <v-textarea
                            label="Comments"
                            :rules="getFieldRules('', 'Comments is required')"
                            v-model="comments"
                            required
                        ></v-textarea>
                    </v-col>
                </v-row>
            </v-form>
           
    

             
      <VRow>
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    color="primary"
                    class="me-4"
                    @click="prescriptionForm"
                        :disabled="!valid"
                  >
                    Save
                  </VBtn>
                  <VBtn
                    color="error"
                    variant="outlined"
                    @click="resetForm"
                  >
                    Discard
                  </VBtn>
                </div>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </PerfectScrollbar>
    </VCard>
  </VNavigationDrawer>
     <v-dialog v-model="prescriptionModelForm" max-width="1200">
        <v-card class="pa-3">
            <v-row>
                <v-col cols="12" class="text-right cross">
                    <v-btn
                        icon
                        color="transparent"
                        small
                        @click="prescriptionModelForm = false"
                    >
                        <v-icon>rdi-close</v-icon>
                    </v-btn>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="12">
                    <v-data-table
                        :headers="headers"
                        :items="itemsPrescriptions"
                        :search="search"
                        :loading="loading"
                        :page.sync="page"
                        :items-per-page.sync="itemsPerPage"
                        @page-count="pageCount = $event"
                        class="elevation-1"
                    >
                        <template v-slot:top>
                            <v-toolbar flat :height="30">
                                <v-toolbar-title>Prescriptions</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>
                                <v-text-field
                                    v-model="search"
                                    label="Search"
                                    single-line
                                    hide-details
                                ></v-text-field>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-btn
                                color="primary"
                                small
                                @click="selectedItem(item)"
                                >Select</v-btn
                            >
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-card>
    </v-dialog>
</template>

<style lang="scss">
.v-navigation-drawer__content {
  overflow-y: hidden !important;
}
</style>
