<script setup>
import { onMounted, ref } from 'vue';
import { useStore } from 'vuex';
const store = useStore();
const OrdersData = ref([]);
onMounted(() => {
    OrdersData.value = store.getters.getProductsAnalytics.orders;
    console.log("OrdersData onMounted:", OrdersData.value);
});

const headers = [
    { title: '#Product', key: 'product_id' },
    { title: 'Name', key: 'product_name' },
    { title: 'Amount', key: 'total_amount' },
    { title: 'Orders', key: 'total_orders' },
    { title: 'Item sold', key: 'total_item_sold' },
];


const formatAmount = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};
</script>

<template>
    <VCard>
        <VDataTable :headers="headers" :items="store.getters.getProductsAnalytics.orders" :items-per-page="5"
            class="text-no-wrap">
            <template #item.product_id="{ item }">
                <span class="text-h6">{{ item.product_id }}</span>
            </template>
            <template #item.total_amount="{ item }">
                <span class="text-h6">{{ formatAmount(item.total_amount) }}</span>
            </template>
        </VDataTable>
    </VCard>
</template>
