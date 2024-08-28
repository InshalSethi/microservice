<script setup>
import { useRoute, useRouter } from 'vue-router';
import { PerfectScrollbar } from 'vue3-perfect-scrollbar';
import { VForm } from 'vuetify/components/VForm';
import { useStore } from 'vuex';
const store = useStore()
const router = useRouter();
const route = useRoute()
const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  userData: {
    type: Object,
    required: true,
  }
})
const order_note = ref()
const getSingleNote = computed(async () => {
    order_note.value = '';
    console.log("props",props.userData);
    if(props.userData.note)
        order_note.value = props.userData.note.note;
    // itemId.value=props.userData.id
    // editedItem.value.id= props.userData.id
      
});

const emit = defineEmits(['update:isDrawerOpen', 'addedMessage'])
const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}
const refVForm = ref()
const onSubmit = async () => {
  const { valid } = await refVForm.value.validate()
  if (valid) {

    await store.dispatch('UpdateNoteByID', {
      note: order_note.value,
      id: props.userData.note.id
    })


    if (!store.getters.getErrorMsg) {
      emit('addedMessage', 'success')
      order_note.value = null
      
      emit('update:isDrawerOpen', false)
    }

    
    await store.dispatch("orderDetailAgent", {
        id: route.params.id,
    });

    emit('notes', store.getters.getPatientOrderDetail.appointment_notes)



  }
}


const resetForm = () => {
  refVForm.value?.reset()
  emit('update:isDrawerOpen', false)
}


// const roleData = ref([]);
// const useSortedRole = () => {
//   const isLoading = ref(false);
//   const error = ref(null);

//   const sortedRole = computed(() => {
//     const allOption = { id: '', role: 'Select Any' };
//     const sortedData = roleData.value.slice().sort((a, b) => {
//       return a.role.localeCompare(b.role);
//     });
//     return [allOption, ...sortedData];
//   });

//   const fetchRoleData = async () => {
//     isLoading.value = true;
//     error.value = null;
//     try {
//       await store.dispatch('getAllRolesList');
//       roleData.value = store.getters.getRolesList || [];
//       console.log('Fetched Role data:', roleData.value);
//     } catch (e) {
//       console.error('Error fetching Role data:', e);
//       error.value = 'Failed to fetch Role data';
//     } finally {
//       isLoading.value = false;
//     }
//   };

//   onBeforeMount(fetchRoleData);

//   return { sortedRole, isLoading, error, fetchRoleData };
// };
// const { sortedRole, isLoading, error, fetchRoleData } = useSortedRole();
</script>

<template>
  <VNavigationDrawer :model-value="props.isDrawerOpen" temporary location="end" width="800"
    @update:model-value="handleDrawerModelValueUpdate">
    <!-- 👉 Header -->
    <AppDrawerHeaderSection title="Edit Note" @cancel="$emit('update:isDrawerOpen', false)" />
    <VDivider />

    <VCard flat>
      <PerfectScrollbar :options="{ wheelPropagation: false }" class="h-100">
        <VCardText style="block-size: calc(100vh - 5rem);" v-if="getSingleNote">
          <VForm ref="refVForm" @submit.prevent="">
            <VRow>
                
                <VCol cols="12">
                <VTextarea v-model="order_note" label="Order Note" :rules="[requiredValidator]"
                  placeholder="Note" />
              </VCol>
              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn type="submit" color="primary" class="me-4" @click="onSubmit">
                    Update
                  </VBtn>
                  <VBtn color="error" variant="outlined" @click="resetForm">
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
</template>

<style lang="scss">
.v-navigation-drawer__content {
  overflow-y: hidden !important;
}
</style>
