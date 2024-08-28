<script setup>
import { computed, ref } from 'vue';
import CartProducts from './CartProducts.vue';

const props = defineProps({
  page: {
    type: String,
    required: true,
  },
  data: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits([
  'push',
  'remove',
  'updateGrandTotal',
  'updateShippingTotal',
]);

const invoice = ref(props.data.invoice);
const salesperson = ref(props.data.salesperson);
const thanksNote = ref(props.data.thanksNote);
const note = ref(props.data.note);

const productTotals = ref({});
const productShippingTotals = ref({});


const removeProduct = (id) => {
  emit('remove', id);
  delete productTotals.value[id];
  delete productShippingTotals.value[id];
  calculateGrandTotal();
  calculateShippingTotal();
};
const getData =  computed(() => {
  console.log(">>>>>Asid",props.data.purchasedProducts);
});
const updateProductTotal = ({ id, data, total, totalShipping }) => {
  console.log('Updating totals for product:', data,id, data, total, totalShipping);
  emit('push', data);
  productTotals.value[id] = total;
  productShippingTotals.value[id] = totalShipping;
  calculateGrandTotal();
  calculateShippingTotal();
};

const calculateGrandTotal = () => {
  const grandTotal = Object.values(productTotals.value).reduce((sum, total) => sum + total, 0);
  emit('updateGrandTotal', grandTotal);
};

const calculateShippingTotal = () => {
  const shippingTotal = Object.values(productShippingTotals.value).reduce((sum, totalShipping) => sum + totalShipping, 0);
  emit('updateShippingTotal', shippingTotal);
};

const grandTotal = computed(() => {
  return Object.values(productTotals.value).reduce((sum, total) => sum + total, 0);
});

const shippingTotal = computed(() => {
  return Object.values(productShippingTotals.value).reduce((sum, totalShipping) => sum + totalShipping, 0);
});
</script>

<template>
  <VDivider class="my-6 border-dashed" />
  <div v-if="getData"></div>
  <!-- 👉 Add purchased products -->
  <div class="add-products-form">
    
    <div v-for="(product, index) in props.data.purchasedProducts" :key="product.id || index" class="mb-4">
      <CartProducts :id="product.id" :data="product" @remove-product="removeProduct"
        @update-total="updateProductTotal" :page="page"/>
    </div>

  </div>

  <VDivider class="my-6 border-dashed" />

  <!-- 👉 Total Amount -->
  <div class="d-flex justify-space-between flex-wrap flex-column flex-sm-row">
    <div class="mb-6 mb-sm-0">
    </div>

    <div>
      <table class="w-100">
        <tbody>
          <tr>
            <td class="pe-16">
              Subtotal:
            </td>
            <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">
              <h6 class="text-h6">
                ${{ grandTotal.toFixed(2) }}
              </h6>
            </td>
          </tr>
          <tr>
            <td class="pe-16">
              Shipping Total:
            </td>
            <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">
              <h6 class="text-h6">
                ${{ shippingTotal.toFixed(2) }}
              </h6>
            </td>
          </tr>
        </tbody>
      </table>

      <VDivider class="mt-4 mb-3" />

      <table class="w-100">
        <tbody>
          <tr>
            <td class="pe-16">
              Total:
            </td>
            <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">
              <h6 class="text-h6">
                ${{ (grandTotal + shippingTotal).toFixed(2) }}
              </h6>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
