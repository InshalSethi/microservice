<script setup>
import { computed, onBeforeMount, ref, watch } from 'vue';
import { useStore } from 'vuex';

const store = useStore();
const props = defineProps({
  id: {
    type: Number,
    required: true,
  },
  page: {
    type: String,
    required: true,
  },
  data: {
    type: Object,
    required: true,
    default: () => ({
      id: '',
      title: '',
      subscription: null,
      // onetime: null,
      price: 0,
      qty: 0,
      shipping_cost: 0,
      is_prescription_required:'',
    }),
  },
});
const localProductData = ref([{
  id: Date.now(),
  title: '',
  subscription: null,
  // onetime: null,
  price: 0,
  qty: 1,
  shipping_cost: 0,
  is_prescription_required:null,
}]);
const getA = computed(() => {
  console.log("hello asif",props.id, props.data);
});
const subscriptions = [
  { title: 'Yes', value: "Yes" },
  { title: 'No', value: "No" },
];
const onetime = [
  { title: 'Yes', value: "Yes" },
  { title: 'No', value: "No" },
];


const addNewItem = () => {
  localProductData.value.push({
    id: Date.now(),
    title: '',
    subscription: null,
    // onetime: null,
    price: 0,
    qty: 1,
    shipping_cost: 0,
    is_prescription_required:null,
  });
};
const emit = defineEmits([
  'removeProduct',
  'updateTotal',
]);

const productData = ref([]);
const selectedItem = ref(null);


const useSortedProduct = () => {
  const isLoading = ref(false);
  const error = ref(null);
  const productData = ref([]);

  const sortedProduct = computed(() => {
    return productData.value
      .slice()
      .filter(product => product.title !== 'Select Any')
      .sort((a, b) => a.title.localeCompare(b.title));
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



const { sortedProduct, isLoading, error } = useSortedProduct();


watch(selectedItem, () => {
  const item = sortedProduct.value.find(obj => obj.title === selectedItem.value);
  if (item && item.id !== '') {
    const newProduct = {
      id: item.id || Date.now(),
      title: item.title,
      subscription: item.subscription === 'true' ? true : false,
      // onetime: item.onetime === 'true' ? true : false,
      price: item.price || 0,
      qty: 1,
      shipping_cost: item.shipping_cost || 0,
      is_prescription_required:item.is_prescription_required || 0,
    };
    localProductData.value.push(newProduct);
    updateTotal();
    selectedItem.value = null; // Reset selection after adding
  }
});

const removeProduct2 = () => {
  emit('removeProduct', props.id);
};
const removeProduct = (index) => {
  localProductData.value.splice(index, 1);
  //emit('removeProduct', props.id);
  updateTotal();
};
const totalPrice = computed(() =>
  localProductData.value.reduce((sum, product) =>
    sum + Number(product.price) * Number(product.qty), 0)
);

const totalShipping = computed(() =>
  localProductData.value.reduce((sum, product) => {
    const shippingCost = Number(product.shipping_cost);
    const qty = Number(product.qty);
    return sum + (shippingCost > 0 ? shippingCost * qty : 0);
  }, 0)
);

const updateTotal = () => {
  console.log('totalShipping.value',totalPrice.value,totalShipping.value)
  emit('updateTotal', {
    id: props.id,
    data: localProductData.value,
    total: totalPrice.value,
    totalShipping: totalShipping.value
  });
};

watch(() => localProductData.value.qty, updateTotal);
watch(() => localProductData.value.price, updateTotal);
const updateProductDetails = (index) => {
  const selectedProduct = sortedProduct.value.find(item => item.title === localProductData.value[index].title);
  if (selectedProduct) {
    localProductData.value[index] = {
      ...localProductData.value[index],
      id: selectedProduct.id || Date.now(),
      price: selectedProduct.price || 0,
      shipping_cost: selectedProduct.shipping_cost || 0,
      is_prescription_required:selectedProduct.is_prescription_required|| 0
    };
    updateTotal();
  }
};
// Emit initial total on component mount
onBeforeMount(() => {
  updateTotal();
});
watch(localProductData, () => {
  updateTotal();
}, { deep: true });
watch(() => store.getters.getPatientOrderDetail, (newVal) => {
  if (newVal && newVal.order_details && newVal.order_details.id) {
    setData(newVal);
  }
}, { immediate: true });

function setData(orderDetail) {
  if(props.page === 'edit'){
    const storedProducts = orderDetail.order_items.items
    console.log('storedProducts>>>>>>>',storedProducts);
    localProductData.value = storedProducts.map(item => ({
      id: item.id,
      title: item.title,
      subscription: item.subscription === true? 'Yes':'No', 
      // onetime: item.onetime === true? 'Yes':'No', 
      price: parseFloat(item.price), 
      qty: parseInt(item.quantity, 10), 
      shipping_cost: parseFloat(item.shipping_cost), 
      is_prescription_required:item.is_prescription_required,
    }));
    updateTotal();
  }
  
}
</script>

<template>
  <div v-if="getA"></div>
  <div class="add-products-header mb-2 d-none d-md-flex mb-4">
    <VRow class="me-10">
      <VCol cols="12" md="3">
        <h6 class="text-h6">Item</h6>
      </VCol>
      <VCol cols="12" md="3">
        <h6 class="text-h6">Subscription</h6>
      </VCol>
      <VCol cols="12" md="2">
        <h6 class="text-h6 ps-2">Price</h6>
      </VCol>
      <VCol cols="12" md="2">
        <h6 class="text-h6 ps-2">QTY</h6>
      </VCol>
      <VCol cols="12" md="2">
        <h6 class="text-h6">Total</h6>
      </VCol>
    </VRow>
  </div>
   
  <div v-for="(product, index) in localProductData" :key="product.id">
    <VCard flat border class="d-flex flex-sm-row flex-column-reverse mb-4">
      <div class="pa-5 flex-grow-1">
        <VRow>
          <VCol cols="12" md="3">
            <VAutocomplete v-model="product.title" :items="sortedProduct" item-title="title" item-value="title"
              label="Select Item" placeholder="Select Item" :loading="isLoading" :error-messages="error"
              :rules="[v => !!v || 'Item is required']" @update:modelValue="updateProductDetails(index)" />
          </VCol>
          <VCol cols="12" md="3">
            <VAutocomplete v-model="product.subscription" :items="subscriptions" item-title="title" item-value="value"
              label="Select Subscription" placeholder="Select Subscription" :error-messages="error"
              :rules="[v => !!v || 'Subscription is required']" />
          </VCol>
          <VCol cols="12" md="2">
            <VTextField v-model.number="product.price" type="number" label="Price" placeholder="100" disabled />
          </VCol>
          <VCol cols="12" md="2">
            <VTextField v-model.number="product.qty" type="number" label="Quantity" placeholder="1" :rules="[
              v => !!v || 'Quantity is required',
              v => v > 0 || 'Quantity must be greater than 0'
            ]" />
          </VCol>
          <VCol cols="12" md="2">
            <p class="text-h6 mt-4">
              ${{ (Number(product.price) * Number(product.qty)).toFixed(2) }}
            </p>
          </VCol>
        </VRow>
      </div>
      <div class="d-flex flex-column align-end item-actions pa-2"
        :class="$vuetify.display.smAndUp ? 'border-s' : 'border-b'">
        <VBtn icon @click="removeProduct(index)">
          <VIcon icon="ri-close-line" />
        </VBtn>
      </div>
    </VCard>
  </div>

  <!-- Add new item button -->
  <VBtn size="small" prepend-icon="ri-add-line" @click="addNewItem">
    Add Item
  </VBtn>

</template>
