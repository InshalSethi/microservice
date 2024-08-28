<script setup>
import addOrderNote from '@/pages/apps/ecommerce/order/list/addOrderNote.vue';
import EditOrderNote from '@/pages/apps/ecommerce/order/list/EditOrderNote.vue';
import store from '@/store';
import { useRoute, useRouter } from 'vue-router';
const router = useRouter()
const route = useRoute()
const isAddCustomerDrawerOpen = ref(false)
const isEditCustomerDrawerOpen = ref(false)
const deleteDialog = ref(false)
const props = defineProps({
    orderData: {
        type: Object,
        required: true,
    },
})

const handleParentAdded = async (msg) => {
  if (msg == 'success') {
    //   store.dispatch('updateIsLoading', true)
    // loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] });
        //  store.dispatch('updateIsLoading', false)
  }
  // You can also trigger a toast or snackbar here to show the message
  // For example, if using Vuetify:
  // showSnackbar(msg)
}
const notes = ref([]);
const historyNotes = computed(async () => {

    let notesData = props.orderData.appointment_notes;
    console.log("notesData", notesData);
    for (let data of notesData) {
        if (data.note_type == 'Notes') {
            let dataObject = {}
            dataObject.note = data.note
            dataObject.doctor = props.orderData.appointment_details.provider_name;
            dataObject.date = formatDateDate(data.created_at)
            dataObject.id = data.id
            //notes.value.push(dataObject)
        }
    }
    notes.value.sort((a, b) => {
        return b.id - a.id;
    });
    console.log("getNotes", notes.value);
    store.dispatch('updateIsLoading', false)
    return notes.value
});
const formatDateDate = (date) => {
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
onMounted(async () => {
    let notesData = props.orderData.appointment_notes;
    console.log("notesData", notesData);
    for (let data of notesData) {
        if (data.note_type == 'Notes') {
            let dataObject = {}
            dataObject.note = data.note
            dataObject.doctor = props.orderData.appointment_details.provider_name;
            dataObject.date = formatDateDate(data.created_at)
            dataObject.id = data.id
             dataObject.created_by = data.created_by
            notes.value.push(dataObject)
        }
    }
    notes.value.sort((a, b) => {
        return b.id - a.id;
    });
    console.log("getNotes", notes.value);
});
const addNewOrder = () => {
  store.dispatch("updateIsLoading", true);
  router.replace(route.query.to && route.query.to != '/admin/orders' ? String(route.query.to) : '/admin/add-order')
  store.dispatch("updateIsLoading", false);
};

const getNotes = () =>{
    
    let notesData = store.getters.getPatientOrderDetail.appointment_notes;
    notes.value = [];
    for (let data of notesData) {
        if (data.note_type == 'Notes') {
            let dataObject = {}
            dataObject.note = data.note
            dataObject.doctor = props.orderData.appointment_details.provider_name;
            dataObject.date = formatDateDate(data.created_at)
            dataObject.id = data.id
            dataObject.created_by = data.created_by
            notes.value.push(dataObject)
        }
    }
    notes.value.sort((a, b) => {
        return b.id - a.id;
    });
    console.log(">>Notes",notes.value);
}
const editedItem = ref([]);
const editedIndex = ref([]);
const editItem = async(item) => {
    isEditCustomerDrawerOpen.value = true;
     await store.dispatch('GetNoteByID', {
    id: item.id,
  })
  
   editedItem.value = store.getters.getSingleOrderNote
     console.log(editedItem.value);
}
const deleteItem = item => {
  editedIndex.value = notes.value.indexOf(item)
  editedItem.value = { ...item }
  deleteDialog.value = true
}

const closeDelete = () => {
  deleteDialog.value = false
//   editedIndex.value = -1
//   editedItem.value = { ...defaultItem.value }
}

const deleteItemConfirm = async () => {
  
  await store.dispatch('DeleteSingleNote',{
    id: editedItem.value.id
  })
  
  closeDelete()
    await store.dispatch("orderDetailAgent", {
        id: route.params.id,
    });

    getNotes();

}
</script>

<template>
    <VRow>
          <VCol cols="12" md="8" class="d-flex align-center mb-3">
          <VBtn color="primary" prepend-icon="ri-add-line" @click="isAddCustomerDrawerOpen = !isAddCustomerDrawerOpen" v-if="$can('read', 'Notes Add')">
            New Note
          </VBtn>
          </VCol>
    </VRow>
    <VCard title="Notes" v-if="notes.length > 0">
        <VCardText>
            <VTimeline truncate-line="both" align="start" side="end" line-inset="10" line-color="primary"
                density="compact" class="v-timeline-density-compact">

                <template v-if="historyNotes">
                    <VTimelineItem dot-color="primary" size="x-small" v-for="(p_note, index) of notes" :key="index">
                        <div class="d-flex justify-space-between align-center mb-3">
                            <span class="app-timeline-title">{{ p_note.note }}</span>
                            <span class="app-timeline-meta">{{ p_note.date }}</span>
                        </div>
                        <div class="d-flex justify-space-between align-center mb-3">
                            <span class="app-timeline-title">{{ p_note.created_by?p_note.created_by:p_note.doctor }}</span>
                            <span class="app-timeline-meta"> <div class="d-flex gap-1">
                                <IconBtn
                                size="small"
                                @click="editItem(p_note)"
                                v-if="$can('read', 'Notes Edit')"
                                >
                                <VIcon icon="ri-pencil-line" />
                                </IconBtn>
                                <IconBtn
                                size="small"
                                @click="deleteItem(p_note)"
                                v-if="$can('read', 'Notes Delete')"
                                >
                                <VIcon icon="ri-delete-bin-line" />
                                </IconBtn>
                            </div>
                        </span>
                        </div>
                        <!-- <p class="app-timeline-text mb-0">
                            {{ p_note.doctor }}
                        </p> -->
                    </VTimelineItem>
                </template>

            </VTimeline>
        </VCardText>

    </VCard>

    <VCard v-else>
        <VAlert border="start" color="primary" variant="tonal">
            <div class="text-center">No data found</div>
        </VAlert>
    </VCard>
    <addOrderNote  @notes="getNotes" v-model:is-drawer-open="isAddCustomerDrawerOpen"     @addedMessage="handleParentAdded" />
     <EditOrderNote @notes="getNotes" v-model:is-drawer-open="isEditCustomerDrawerOpen"  :user-data="store.getters.getSingleOrderNote"    @addedMessage="handleParentAdded" />
       <VDialog
      v-model="deleteDialog"
      max-width="500px"
    >
      <VCard>
        <VCardTitle>
          Are you sure you want to delete this item?
        </VCardTitle>

        <VCardActions>
          <VSpacer />

          <VBtn
            color="error"
            variant="outlined"
            @click="closeDelete"
          >
            Cancel
          </VBtn>

          <VBtn
            color="success"
            variant="elevated"
            @click="deleteItemConfirm"
          >
            OK
          </VBtn>

          <VSpacer />
        </VCardActions>
      </VCard>
    </VDialog>  
    <!-- <VList class="pb-0" lines="two" v-if="historyNotes">
        <template v-if="notes.length > 0" v-for="(p_note, index) of notes" :key="index">
            <VListItem class="pb-0" border>
                <VListItemTitle>
                    <span class="pb-0">{{ p_note.note }}</span>
                    <p class="text-start fs-5 mb-0 pb-0 text-grey">
                        <small> {{ p_note.doctor }}</small>
                    </p>
                    <p class="text-end fs-5 mb-0 pb-0 text-grey">
                        <small> {{ p_note.date }}</small>
                    </p>
                </VListItemTitle>
            </VListItem>
            <VDivider v-if="index !== notes.length - 1" />
        </template>
        <template v-else>
            <VCard>
                <VAlert border="start" color="rgb(var(--v-theme-yellow))" variant="tonal">
                    <div class="text-center">No data found</div>
                </VAlert>

            </VCard>
        </template>
    </VList> -->
</template>
