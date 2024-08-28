<script setup>
import AddOrderCart from '@/views/apps/invoice/AddOrderCart.vue';
import {
requiredValidator
} from '@validators';
import moment from 'moment-timezone';
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { VForm } from 'vuetify/components/VForm';
import { useStore } from 'vuex';
const store = useStore();
const router = useRouter()
const route = useRoute()
const isTonalSnackbarVisible = ref(false)
const isLoadingVisible = ref(false)
const numberedSteps = [
  { title: 'Cart', subtitle: 'Add products' },
  { title: 'Appointment', subtitle: 'Meeting date & time' },
  { title: 'Checkout', subtitle: 'Process payment' },
];
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
  { name: 'Arkansas', abbreviation: 'AR' },
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
const isAppiontment =ref(false);
const isMobile = ref(window.innerWidth <= 768);
const currentStep = ref(0);
const isCurrentStepValid = ref(true);
const refCartForm = ref();
const refCheckoutForm = ref();
const refAppointmentForm = ref();
const patientID = ref(null);
const productArray = ref(null);
const productData = ref([]);
const productIds = ref(null)
const invoiceData = ref({
  invoice: {
    total: 0,
    total_shippping: 0,
    balance: 0,
  },
  purchasedProducts: [{
    id: null,
    title: null,
    subscription: null,
    // onetime: null,
    price: 0,
    qty: 0,
    shipping_cost: 0,
    is_prescription_required: null,
  }],
  checkout: {
    address1: null,
    address2: null,
    city: null,
    state: null,
    zip_code: null,
    cardNumber: null,
    expiry: null,
    cvv: null,

  }
});
const sortedStates = computed(() => {
  return states.value.slice().sort((a, b) => {
    return a.name.localeCompare(b.name);
  });
});
const errors = ref({
  address: undefined,
  city: undefined,
  state: undefined,
  zipcode: undefined,
  country: undefined,

});
const patients = ref([]);
const search = ref('');
const grandTotal = ref(0);
const shipplingTotal = ref(0);

const updateGrandTotal = (total) => {
  grandTotal.value = total;
  invoiceData.value.invoice.total = total;
};

const updateShippingTotal = (total) => {
  shipplingTotal.value = total;
  invoiceData.value.invoice.total_shippping = total;
};

const addProduct = (value) => {
  console.log('updatedd ==== ', value)
  productArray.value = value

  //invoiceData.value.purchasedProducts.push(value);
};

const removeProduct = (id) => {
  invoiceData.value.purchasedProducts.splice(id, 1);
};

const validateCurrentStep = () => {
  console.log('currentStep.value',productArray.value)
  switch (currentStep.value) {
    case 0:
      validateCartForm();
      break;
    case 1:
      validateAppointmentForm();
      break;
    case 2:
      validateCheckoutForm();
      break;
    default:
      console.log('Final step reached');
  }
};

const validateCartForm = async () => {
  refCartForm.value?.validate().then(async ({ valid }) => {
    if (valid) {
      console.log('invoiceData', productArray.value)
      await store.dispatch('getAllProductsList');
      productData.value = store.getters.getProductsList
      const finalArray = productArray.value.map(cartItem => {
        const matchedProduct = productData.value.find(plan => plan.id === cartItem.id);
        if (matchedProduct) {
          return {
            ...matchedProduct,
            qty: cartItem.qty,
            subscription: cartItem.subscription === 'Yes' ? true : false,
            onetime: cartItem.subscription === 'No' ? true : false
          };
        }
        return null;
      }).filter(item => item !== null);
      const finalArrayJson = JSON.stringify(finalArray);
      console.log('finalArrayJson', finalArrayJson);
      productIds.value = JSON.parse(finalArrayJson).map(product => ({ plans_id: product.id, quantity: product.qty, subscription: product.subscription, onetime: product.onetime }));
      console.log('Products', productIds.value);
      let hasPrescriptionRequired = productArray.value.some(item => item.is_prescription_required === 1);
      console.log("hasPrescriptionRequired",hasPrescriptionRequired);
      hasPrescriptionRequired ? isAppiontment.value = true :  isAppiontment.value = false;
      if(isAppiontment.value == true){
        currentStep.value += 1;
      }else{
        currentStep.value += 2;
      }
      isCurrentStepValid.value = true;
    } else {
      isCurrentStepValid.value = false;
    }
  });
};

const validateCheckoutForm = () => {
  refCheckoutForm.value?.validate().then(async({ valid }) => {
    console.log('currentStep.value', currentStep.value)
    if (valid) {
      store.dispatch('updateIsLoading', true)
        await saveOrder();
        await processPayment();
        if(isAppiontment.value == true){
            await bookAppointment();
        }
        store.dispatch('updateIsLoading', false)
        router.replace(route.query.to && route.query.to != '/admin/add-order' ? String(route.query.to) : '/admin/orders')
      
      isCurrentStepValid.value = true;
    } else {
      isCurrentStepValid.value = false;
    }
  });
};

const validateAppointmentForm = () => {
  refAppointmentForm.value?.validate().then(async ({ valid }) => {
    if (valid) {
      if (!showCalendar.value) {
        
      }
      currentStep.value++;
      isCurrentStepValid.value = true;
      console.log('Order completed:', invoiceData.value);
    } else {
      isCurrentStepValid.value = false;
    }
  });
};


const saveOrder = async () => {
  await store.dispatch('saveShippingInformation', {
    first_name: store.getters.getSinglePatient.first_name,
    last_name: store.getters.getSinglePatient.last_name,
    email: store.getters.getSinglePatient.email,
    phone: store.getters.getSinglePatient.phone_no,
    patient_id: patientID.value,
    shipping_address1: invoiceData.value.checkout.address1,
    shipping_address2: invoiceData.value.checkout.address2,
    shipping_city: invoiceData.value.checkout.city,
    shipping_state: invoiceData.value.checkout.state,
    shipping_zipcode: invoiceData.value.checkout.zip_code,
    shipping_country: 'United States',
    billing_address1: null,
    billing_address2: null,
    billing_city: null,
    billing_state: null,
    billing_zipcode: null,
    billing_country: null,
    shipping_amount: shipplingTotal.value,
    total_amount: grandTotal.value,
    items: productIds.value
  })
};

const processPayment = async () => {
  await store.dispatch('processPayment')
}

const goToPreviousStep = () => {
  if (currentStep.value > 0) {
      if(isAppiontment.value == true){
        currentStep.value--;
      }else{
        currentStep.value = 0;
      }
  }
};

const fetchInitialPatients = async () => {
  console.log("hellocurrentStep",currentStep.value);
  try {
    await store.dispatch('getInitialPatients');
    patients.value = store.getters.getOrderFilters;
  } catch (e) {
    error.value = 'Failed to fetch initial patients';
  } finally {
  }
};

const onSearch = async (val) => {
  if (val && val.length > 0) {
    try {
      await store.dispatch('searchPatients', val);
      patients.value = store.getters.getOrderFilters;
    } catch (e) {
      error.value = 'Failed to search patients';
    } finally {
      
    }
  }
};
onMounted(fetchInitialPatients);

const useSortedPatient = () => {
  const patientData = ref([]);
  const isLoading = ref(false);
  const error = ref(null);

  const sortedPatient = computed(() => {
    const allOption = { id: '', patient_name: 'Select Any' };
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

const { sortedPatient, isLoading, error } = useSortedPatient();

const onPatientChange = async () => {

  await store.dispatch('patientGETBYID', {
    id: patientID.value,
  })

};
const cardNumberFormat = () => {
  invoiceData.value.checkout.cardNumber = invoiceData.value.checkout.cardNumber.replace(/\D/g, '').substring(0, 16);

};
const formatExpiry = () => {
  // Automatically format the input to MM/YY format
  invoiceData.value.checkout.expiry = invoiceData.value.checkout.expiry.replace(/\D/g, '').slice(0, 4).replace(/(\d{2})(\d{2})/, '$1/$2');
};
const handleCVVInput = () => {
  // Remove non-digit characters from input
  invoiceData.value.checkout.cvv = invoiceData.value.checkout.cvv.replace(/\D/g, '');
};

//Book Appointment starts here
const selectTimeZone = ref(null)
const isTimeSlot = ref()
const timeSlotString = ref()
const timezone = ref([]);
const filterDate = ref([]);
const calanderFormatedDate = ref();
const calanderSelectedDate = ref();
const scheduleDate = ref();
const selectTimeSlot = ref();
const chooseDate = ref([]);
const isAMPM = ref(false);
const additionalButtonsShown = ref(new Array(5).fill(false));
const orderDate = ref();
const errorMessage = ref()
const drawer = ref()
const timeZoneChanged = ref(false)
const timezonePopup = ref(false)
const showCalendar = ref(true)
const access_token = localStorage.getItem('access_token');
const slotTime = ref(null)
const appointmentDetails = JSON.parse(localStorage.getItem('patient_appointment_details'))
const storedTimeZone = ref(null)
const storedAppointmentDate = ref(null)
const storedAppointmentTime = ref(null)
const storedSlotTime = ref(null)
const seetingPlanLogo = ref();
const timezoneMap = {
  'EST': 'America/New_York',
  'PST': 'America/Los_Angeles',
  'CST': 'America/Chicago',
  'MST': 'America/Denver'
};
onBeforeMount(async () => {
  if (selectTimeZone.value || calanderFormatedDate.value || isTimeSlot.value || slotTime.value) {

    if (calanderFormatedDate.value)
      await getAvailableSlots(calanderFormatedDate.value)
    if (slotTime.value) {
      const entries = Object.entries(chooseDate.value)
      for (const [index, [key, tempSlot]] of entries.entries()) {
        if (tempSlot == slotTime.value)
          timeSlot(index, tempSlot)
      }
    }
  }
  if (!selectTimeZone.value) {
    // selectTimeZone.value = moment.tz.guess();
    // List of allowed IANA timezones
    const allowedTimezones = Object.values(timezoneMap);

    // Get the guessed timezone
    let guessedTimezone = moment.tz.guess();

    // Check if the guessed timezone is in the allowed list, fallback to EST if not
    if (!allowedTimezones.includes(guessedTimezone)) {
      guessedTimezone = 'America/New_York'; // Fallback to EST
    }
    const selectedAbbreviation = Object.keys(timezoneMap).find(key => timezoneMap[key] === guessedTimezone) || 'EST';
    // Set the initial selected timezone
    selectTimeZone.value = selectedAbbreviation;

    console.log('Current Timezone:', selectTimeZone.value);
  }
});
onMounted(async () => {


  window.addEventListener('resize', checkIfMobile);

  const timezones = reactive(
    Object.entries(timezoneMap).map(([abbreviation, timezone]) => {
      const offset = moment.tz(timezone).utcOffset();
      const offsetHours = Math.abs(Math.floor(offset / 60));
      const offsetMinutes = Math.abs(offset % 60);
      const offsetSign = offset < 0 ? '-' : '+';
      const formattedOffset = `${offsetSign}${offsetHours.toString().padStart(2, '0')}:${offsetMinutes.toString().padStart(2, '0')}`;
      const now = moment().tz(timezone);
      const nowformattedOffset = now.format('Z');
      const nowformattedTimezone = now.format('z');
      const formattedTime = now.format('hh:mm A');

      if (isAMPM.value == false) {
        return {
          name: abbreviation,
          abbreviation: abbreviation,
        };
      } else {
        return {
          name: `${abbreviation} (${nowformattedOffset})`,
          abbreviation: abbreviation,
        };
      }
    })
  );

  timezone.value = timezones;
})
// Detach event listener on component unmount
onUnmounted(() => {
  window.removeEventListener('resize', checkIfMobile);
});
const changeTimeZone = () => {
  timeZoneChanged.value = true
  timezonePopup.value = true
}
const changeDate = () => {
  showCalendar.value = true
}
const checkIfMobile = () => {
  isMobile.value = window.innerWidth <= 768;
};
const handleDateInput = async () => {
  console.log('handleDateInput')
  if (!calanderSelectedDate.value) {
    isTonalSnackbarVisible.value = true
    errorMessage.value = 'Please select any date from calander';
    return
  }
  timeZoneChanged.value = false
  timezonePopup.value = false
  chooseDate.value = [];
  isLoadingVisible.value = true;
  drawer.value = !drawer.value
  // scheduleDate.value = '';
  console.log("calanderDate", calanderSelectedDate.value);
  const selectedDate = new Date(calanderSelectedDate.value);
  const year = selectedDate.getFullYear();
  const month = (selectedDate.getMonth() + 1).toString().padStart(2, '0'); // Adding 1 because months are zero-indexed
  const day = selectedDate.getDate().toString().padStart(2, '0');
  calanderFormatedDate.value = `${year}-${month}-${day}`;
  orderDate.value = `${month}-${day}-${year}`;
  const formattedDate = new Intl.DateTimeFormat('en-US', {
    weekday: 'long',
    month: 'long',
    day: 'numeric',
    year: 'numeric',
  }).format(calanderSelectedDate.value);
  scheduleDate.value = formattedDate;
  console.log("formattedDate", calanderFormatedDate.value, formattedDate);
  if (calanderFormatedDate.value) {
    await getAvailableSlots(calanderFormatedDate.value)
  } else {
    chooseDate.value = [];
  }
};
const getAvailableSlots = async (date) => {
  await store.dispatch('updateSelectedTimezone', selectTimeZone.value)
  await store.dispatch('getAvailableSlotsData', {
    date: date,
  })
  console.log(store.getters.getSelectedTimezone)

  let scheduleTime = store.getters.getSelectedTimezone
  console.log(store.getters.getAvailableSlots)

  chooseDate.value = getTimeSlotsForDate(store.getters.getAvailableSlots, date, scheduleTime);
  console.log('>>>Slots--', chooseDate.value)
  // getTimeSlots(chooseDate.value, scheduleTime);
  console.log(filteredSlots.value)
  if (filteredSlots.value) {
    showCalendar.value = false
  } else {
    isTonalSnackbarVisible.value = true
    errorMessage.value = 'No Slots Availabe'
  }

}
const getTimeSlotsForDate = (timeSlots, date, timezone = 'EST') => {
  // Define the start and end time in the specified timezone
  const startTime = moment.tz(`${date} 08:00:00`, 'YYYY-MM-DD HH:mm:ss', timezone);
  const endTime = moment.tz(`${date} 17:00:00`, 'YYYY-MM-DD HH:mm:ss', timezone);
  const formattedSlots = {};

  for (const [key, value] of Object.entries(timeSlots)) {
    const slotTime = moment.tz(value, 'YYYY-MM-DD HH:mm:ss', timezone);

    if (slotTime.isSameOrAfter(startTime) && slotTime.isSameOrBefore(endTime)) {
      formattedSlots[key] = slotTime.format('hh:mm A');
    }
  }

  return formattedSlots;
};
const timeSlot = (index, selectedTime) => {
  console.log('selectedTime', index, selectedTime)
  selectTimeSlot.value = selectedTime
  // Reset visibility of the last shown additional buttons
  const lastShownIndex = additionalButtonsShown.value.findIndex((shown) => shown);
  if (lastShownIndex !== -1) {
    additionalButtonsShown.value[lastShownIndex] = false;
  }

  // Toggle the visibility of the additional buttons for the clicked time slot
  additionalButtonsShown.value[index] = true;
};
const convertTimeBasedOnContext = (time12hr, time24hr) => {
  // Extracting the AM/PM part from the 12-hour format
  const period = time12hr.slice(-2); // 'AM' or 'PM'

  // Extracting the hour part from both time strings
  let hour12 = parseInt(time12hr.slice(0, 2), 10);
  let hour24 = parseInt(time24hr.slice(0, 2), 10);

  // Adjusting the 24-hour time based on the 12-hour format's AM/PM
  if (period === "PM" && hour12 !== 12) {
    // If it's PM and not 12 PM, add 12 to match 24-hour format
    hour24 = (hour24 % 12) + 12;
  } else if (period === "AM" && hour12 === 12) {
    // If it's 12 AM, it should be 00 in 24-hour format
    hour24 = 0;
  }

  // Construct new 24-hour time string
  let newTime24hr = hour24.toString().padStart(2, "0") + time24hr.slice(2);

  return newTime24hr;
};
const bookAppointment = async () => {
  await scheduleEvent()
};
const saveAppointment = async (date, time, timeSlotString, timezone) => {
  await store.dispatch("savePatientAppointment", {
    patient_id: patientID.value,
    patient_name: store.getters.getSinglePatient.first_name + ' ' + store.getters.getSinglePatient.last_name,
    patient_email: store.getters.getSinglePatient.email,
    appointment_date: date,
    appointment_time: convertTimeBasedOnContext(
      timeSlotString,
      time
    ),
    timezone: timezone,
    timeSlotString: timeSlotString,
  });
};
const scheduleEvent = async () => {
  isTimeSlot.value = '';
  // console.log("sdsadsd", selectTimeZone.value);
  isLoadingVisible.value = true;
  // console.log("isAMPM", isAMPM.value);
  if (isAMPM.value) { //24 hours
    isTimeSlot.value = selectTimeSlot.value + ":00"
    // console.log("timeSlot", isTimeSlot.value);
  } else { //Am pm
    let timeString = selectTimeSlot.value;
    timeSlotString.value = timeString
    console.log("timeSlot", timeString);
    // Parse the time string into a Moment.js object
    let timeMoment = moment(timeString, 'hh:mm A');
    isTimeSlot.value = timeMoment.format('hh:mm:ss');
  }

  console.log('', calanderFormatedDate.value, isTimeSlot.value)
  if (!selectTimeSlot.value || isTimeSlot.value == 'Invalid date') {
    isLoadingVisible.value = false;
    isTonalSnackbarVisible.value = true;
    errorMessage.value = "Please select a time slot"
    return

  }
  if (storedTimeZone.value == selectTimeZone.value
    && storedAppointmentDate.value == calanderFormatedDate.value
    && storedAppointmentTime.value == isTimeSlot.value) {
    isLoadingVisible.value = true;


  } else {
    let apptData = {
      patient_id: patientID.value,
      patient_name: store.getters.getSinglePatient.first_name + ' ' + store.getters.getSinglePatient.last_name,
      patient_email: store.getters.getSinglePatient.email,
      appointment_date: calanderFormatedDate.value,
      appointment_time: isTimeSlot.value,
      timezone: selectTimeZone.value,
      timeSlotString: timeSlotString.value
    }
    saveAppointment(calanderFormatedDate.value, isTimeSlot.value, timeSlotString.value, selectTimeZone.value)
    localStorage.setItem('patient_appointment_details', JSON.stringify(apptData))
    console.log(JSON.parse(localStorage.getItem('patient_appointment_details')))


  }



}

const minDate = computed(() => {

  let tz = timezoneMap[selectTimeZone.value] || 'America/New_York';

  const today = moment().tz(tz);
  console.log('today EST >>>', today.format(), tz, selectTimeZone.value);

  const tomorrow = today.add(1, 'day');
  console.log('tomorrow EST >>>', tomorrow.format());

  return tomorrow.format('YYYY-MM-DD'); // Returns date in YYYY-MM-DD format

});


const maxDate = computed(() => {
  const threeMonthsLater = new Date();
  threeMonthsLater.setMonth(threeMonthsLater.getMonth() + 3);
  return new Date(
    threeMonthsLater.getFullYear(),
    threeMonthsLater.getMonth(),
    threeMonthsLater.getDate()
  );
});
const filteredSlots = computed(() => {
  filterDate.value = []
  console.log('here', calanderSelectedDate.value)
  const today = new Date();
  const todayDateString = today.toISOString().substr(0, 10); // Get today's date in YYYY-MM-DD format
  // console.log("todayDateString", todayDateString);

  if (!storedAppointmentDate.value || calanderSelectedDate.value) {
    const formattedDate = new Intl.DateTimeFormat('en-US', {
      weekday: 'long',
      month: 'long',
      day: 'numeric',
      year: 'numeric',
    }).format(calanderSelectedDate.value);
    scheduleDate.value = formattedDate;

  } else {
    const date = moment(storedAppointmentDate.value);
    scheduleDate.value = date.format('dddd, MMMM D, YYYY');
  }
  let isTodaySlotAvailable = false;
  const entries = Object.entries(chooseDate.value);
  console.log("entries", entries);
  for (const [_, slotValue] of entries) {
    if (calanderFormatedDate.value === todayDateString) {
      console.log('if');
      filterDate.value.push(slotValue);
    } else {
      console.log('else');
      showCalendar.value = true;
      return chooseDate.value;
    }
  }
  return filterDate.value

});
</script>

<template>
  <VCard>
    <VCardText>
      <AppStepper v-model:current-step="currentStep" :items="numberedSteps"
        :is-active-step-valid="isCurrentStepValid" />
    </VCardText>

    <VDivider />

    <VCardText>
      <VWindow v-model="currentStep" class="disable-tab-transition">
        <!-- Cart Step -->
        <VWindowItem>
          <VForm ref="refCartForm" @submit.prevent="validateCurrentStep">
            <VRow>
              <VCol cols="12">
                <h6 class="text-sm font-weight-medium">
                  Cart Details
                </h6>
                <p class="text-xs mb-0">
                  Select products to complete your order
                </p>
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12" md="4">
                <VAutocomplete
                  v-model="patientID"
                  label="Patient"
                  placeholder="Patient"
                  density="comfortable"
                  :items="patients"
                  item-title="patient_name"
                  item-value="id"
                  :error-messages="error"
                  :rules="[requiredValidator]"
                  @update:model-value="onPatientChange"
                  @update:search="onSearch"
                  :search-input.sync="search"
                  :menu-props="{ maxHeight: 400 }"
                />
              </VCol>
            </VRow>
            <VRow>
              <VCol cols="12" md="12">
                <AddOrderCart :data="invoiceData" @push="addProduct" @remove="removeProduct"
                  @update-grand-total="updateGrandTotal"  @update-shipping-total="updateShippingTotal" page="add"/>
              </VCol>

              <VCol cols="12">
                <div class="d-flex flex-wrap gap-4 justify-sm-space-between justify-center mt-8">
                  <VBtn color="secondary" variant="outlined" disabled>
                    <VIcon icon="ri-arrow-left-line" start class="flip-in-rtl" />
                    Previous
                  </VBtn>

                  <VBtn type="submit">
                    Next
                    <VIcon icon="ri-arrow-right-line" end class="flip-in-rtl" />
                  </VBtn>
                </div>
              </VCol>
            </VRow>
          </VForm>
        </VWindowItem>

        <!-- Book Appointment Step -->
        <VWindowItem>
          <VForm ref="refAppointmentForm" @submit.prevent="validateCurrentStep">
            <VCol cols="12" md="6">
              <h6 class="text-sm font-weight-medium">
                Book Appointment
              </h6>
              <p class="text-xs mb-0">
                Select any date & Time
              </p>
              <VCol cols="12" md="12">
                <div class="rounded-top">

                  <VRow class="">
                    <VCol cols="12" md="12">
                      <div class="auth- align-center justify-center" :class="isMobile ? '' : 'pa-4'">
                        <VCard style="background: no-repeat;box-shadow: none;">

                          <VRow class="">
                            <VCol cols="12" md="12">
                              <div>
                                <strong>Timezone: </strong> <span>{{ selectTimeZone }}</span>
                              </div>
                              <div class="text-right pt-2 pb-2">
                                <a class="mb-2" @click="changeTimeZone"
                                  style="cursor: pointer;text-decoration: underline;">Change
                                  Timezone</a>
                              </div>
                              <VRow>
                                <v-col cols="12" md="12" v-if="!showCalendar">
                                  <v-btn class="mb-2 " @click="changeDate" v-if="!showCalendar" block>Change
                                    Date</v-btn>
                                </v-col>
                              </VRow>
                            </VCol>
                          </VRow>
                          <VRow :class="isMobile ? '' : 'px-2'" v-if="showCalendar">
                            <VCol cols="12" md="12">
                              <v-flex>
                                <v-date-picker color="rgb(var(--v-theme-yellow-theme-button))"
                                  v-model="calanderSelectedDate" @update:modelValue="handleDateInput" :min="minDate"
                                  :max="maxDate" width="100%" hide-header></v-date-picker>


                              </v-flex>


                            </VCol>

                          </VRow>
                          <VRow :class="isMobile ? '' : 'px-2'" v-if="!showCalendar">
                            <v-col cols="12" md="12" class="text-center tslot-date pl-4">
                              <p class="text-center mb-3">{{ scheduleDate }}</p>
                              <div class="text-center">
                                <div v-if="!filteredSlots">
                                  <h4 class="text-center text-danger"> No slot available </h4>
                                </div>
                                <span v-else class=" mr-2" v-for="(   choosetimeframes, index   ) in filteredSlots   "
                                  :key="index">
                                  <v-btn @click="timeSlot(index, choosetimeframes)" class="mb-2 px-3 py-2"
                                    variant="outlined" v-if="!additionalButtonsShown[index]"
                                    color="rgb(var(--v-theme-yellow-theme-button))">
                                    {{ choosetimeframes }}
                                  </v-btn>

                                  <span v-if="additionalButtonsShown[index]">
                                    <v-btn class="mb-2 px-3 py-2">{{ choosetimeframes
                                      }}</v-btn>
                                  </span>
                                </span>

                              </div>
                            </v-col>

                          </VRow>
                        </VCard>
                      </div>
                    </VCol>

                    <VCol cols="12" md="12">
                      <VDialog v-model="timezonePopup" refs="myDialog" persistent width="500">
                        <v-card title="Change Time Zone">
                          <v-card-text v-if="timeZoneChanged">
                            <VRow>
                              <v-col cols="12" md="12">
                                <v-autocomplete label="Time Zone" v-model="selectTimeZone" style="column-gap: 0px;"
                                  :items="timezone" item-title="name" item-value="abbreviation"
                                  @update:modelValue="handleDateInput"></v-autocomplete>
                              </v-col>
                            </VRow>

                          </v-card-text>

                          <v-card-actions>
                            <v-spacer></v-spacer>

                            <v-btn text="Close" @click="timezonePopup = false"></v-btn>
                          </v-card-actions>
                        </v-card>
                      </VDialog>
                    </VCol>

                  </VRow>
                </div>
              </VCol>

            </VCol>
            <VCol cols="12">
              <div class="d-flex flex-wrap gap-4 justify-sm-space-between justify-center mt-8">
                <VBtn color="secondary" variant="tonal" @click="goToPreviousStep">
                  <VIcon icon="ri-arrow-left-line" start class="flip-in-rtl" />
                  Previous
                </VBtn>

                <VBtn type="submit">
                    Next
                    <VIcon icon="ri-arrow-right-line" end class="flip-in-rtl" />
                  </VBtn>
              </div>
            </VCol>
          </VForm>
        </VWindowItem>

        <!-- Checkout Step -->
        <VWindowItem>
          <VForm ref="refCheckoutForm" @submit.prevent="validateCurrentStep">
            <VRow>
              <VCol cols="12" md="6">
                <div class="mb-3">
                  <h4 class=" mt-0">Shipping Information</h4>
                  <small>Please provide your shipping details below so that we can
                    promptly send you
                    the
                    product:</small>
                </div>
                <VRow>
                  <VCol cols="12" md="12">
                    <VTextField v-model="invoiceData.checkout.address1" label="Address" :rules="[requiredValidator]"
                      density="comfortable" />
                  </VCol>
                  <VCol cols="12" md="12">
                    <VTextField v-model="invoiceData.checkout.address2" label="APT/Suite #" density="comfortable" />
                  </VCol>
                </VRow>
                <VRow>
                  <VCol cols="12" md="4">
                    <VTextField v-model="invoiceData.checkout.city" label="City" :rules="[requiredValidator]"
                      density="comfortable" />
                  </VCol>
                  <VCol cols="12" md="5">
                    <v-autocomplete clearable v-model="invoiceData.checkout.state" label="Select State"
                      :items="sortedStates" item-title="name" item-value="abbreviation" :rules="[requiredValidator]"
                      :error-messages="errors.state" density="comfortable">
                    </v-autocomplete>
                  </VCol>
                  <VCol cols="12" md="3">
                    <VTextField type="number" v-model="invoiceData.checkout.zip_code" :rules="[requiredValidator]"
                      label="ZipCode" density="comfortable" />
                  </VCol>
                </VRow>

              </VCol>
              <VCol cols="12" md="6">
                <div class="mb-3">
                  <h4 class="mb-0">
                    Card Information &nbsp;<VIcon>ri-bank-card-fill</VIcon>
                  </h4>
                  <small>Fill your card information carefully.</small>
                </div>
                <VRow>
                  <VCol cols="12" lg="12" md="12">
                    <VTextField v-model="invoiceData.checkout.cardNumber" label="Credit Card Number*"
                      :rules="[requiredValidator, cardNumberValidator]" placeholder="xxxxxxxxxxxxxxxx"
                      @input="cardNumberFormat" density="comfortable" />
                  </VCol>
                  <VCol cols="12" lg="6" md="6">
                    <VTextField v-model="invoiceData.checkout.expiry" label="Expiration Date*"
                      :rules="[requiredValidator, expiryValidator]" placeholder="MM/YY" @input="formatExpiry"
                      density="comfortable" />
                  </VCol>
                  <VCol cols="12" lg="6" md="6">
                    <VTextField v-model="invoiceData.checkout.cvv" :rules="[requiredValidator, cvvValidator]"
                      label="CVV*" maxlength="3" @input="handleCVVInput" density="comfortable" />
                  </VCol>
                </VRow>
              </VCol>


              <!-- Add checkout form fields here -->
              <VCol cols="12">
                <div class="d-flex flex-wrap gap-4 justify-sm-space-between justify-center mt-8">
                  <VBtn color="secondary" variant="tonal" @click="goToPreviousStep">
                    <VIcon icon="ri-arrow-left-line" start class="flip-in-rtl" />
                    Previous
                  </VBtn>

                  <VBtn color="success" type="submit">
                    Create
                  </VBtn>
                </div>
              </VCol>

            </VRow>
          </VForm>
        </VWindowItem>

        
      </VWindow>
    </VCardText>
  </VCard>
</template>
