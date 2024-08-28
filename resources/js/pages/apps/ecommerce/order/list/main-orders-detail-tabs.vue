<script setup>
import { computed, onMounted, ref } from "vue";

import Notes from "@/pages/apps/ecommerce/order/list//OrderDetailNotes.vue";
import OrderDetail from "@/pages/apps/ecommerce/order/list//orders-detail.vue";
import Prescription from "@/pages/apps/ecommerce/order/list/OrderDetailPrecrption.vue";
const route = useRoute();
const isConfirmDialogVisible = ref(false);
const isUserInfoEditDialogVisible = ref(false);
const isEditAddressDialogVisible = ref(false);
const currentTab = ref("tab-1");

import { useStore } from "vuex";
const store = useStore();
const orderData = ref(null);
const pateintDetail = ref({});
const productItems = ref([]);
const userTab = ref(null);

const tabs = [
    {
        icon: "mdi-clipboard-text-outline",
        title: "Order Detail",
        action: 'read',
        subject: "Order Detail Tab",
    },
    {
        icon: "mdi-note-text-outline",
        title: "Notes",
        action: 'read',
        subject: "Notes Tab",
    },
    {
        icon: "mdi-prescription",
        title: "Prescriptions",
        action: 'read',
        subject: "Prescription Tab",
    },
];
const filteredOrders = computed(() => {
    let filtered = store.getters.getPatientOrderDetail;

    return filtered;
});
onMounted(async () => {
    store.dispatch("updateIsLoading", true);
    await store.dispatch("orderDetailAgent", {
        id: route.params.id,
    });
    orderData.value = store.getters.getPatientOrderDetail;
    console.log(orderData.value);
     store.dispatch("updateIsLoading", false);
});
</script>
<template>
    <VTabs v-model="userTab" grow>
          <VTab
          v-for="tab in tabs"
          :key="tab.icon"
        >
          <VIcon
            start
            :icon="tab.icon"
            v-if="$can(tab.action, tab.subject)"
          />
          <span v-if="$can(tab.action, tab.subject)">{{ tab.title }}</span>
          
        </VTab>
    </VTabs>
    <VWindow
        v-model="userTab"
        class="mt-6 disable-tab-transition"
        :touch="false"
    >
        <VWindowItem v-if="$can('read', 'Order Detail Tab')">
            <OrderDetail />
        </VWindowItem>

        <VWindowItem v-if="$can('read', 'Notes Tab')">
            <Notes :order-data="orderData" />
        </VWindowItem>

        <VWindowItem v-if="$can('read', 'Prescription Tab')">
            <Prescription :order-data="orderData" />
        </VWindowItem>
    </VWindow>
</template>
<style scoped>

</style>
