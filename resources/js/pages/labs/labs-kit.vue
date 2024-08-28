<script setup>



import { useStore } from 'vuex';
const store = useStore()
const editDialog = ref(false)
const deleteDialog = ref(false)
const search = ref('')
const defaultItem = ref({
  id: -1,
  name: '',
  amount: '',

})
const addDialog = ref(false)
const editedItem = ref(defaultItem.value)
const editedIndex = ref(-1)
const labsKitList = ref([])
const isLoading=ref(false)
// status options
const selectedOptions = [
  {
    text: 'Active',
    value: 1,
  },
  {
    text: 'InActive',
    value: 2,
  },
  
]

    const refVForm = ref(null)


const formatPhoneNumber = () => {
  console.log(editedItem.value)
  // Remove non-numeric characters from the input
  const numericValue = editedItem.value.contact_no.replace(/\D/g, '');

  // Apply formatting logic
  if (numericValue.length <= 10) {
    editedItem.value.contact_no = numericValue.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
  } else {
    // Limit the input to a maximum of 14 characters
    const truncatedValue = numericValue.slice(0, 10);
    editedItem.value.contact_no = truncatedValue.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
  }
};
// headers
const headers = [
{
    title: 'ID',
    key: 'id',
  },
  {
    title: 'NAME',
    key: 'name',
  },
  {
    title: 'Amount',
    key: 'amount',
  },

 
  // {
  //   title: 'ACTIONS',
  //   key: 'actions',
  // },
]

const resolveStatusVariant = status => {
  if (status === 1)
    return {
      color: 'primary',
      text: 'Current',
    }
  else if (status === 2)
    return {
      color: 'success',
      text: 'Professional',
    }
  else if (status === 3)
    return {
      color: 'error',
      text: 'Rejected',
    }
  else if (status === 4)
    return {
      color: 'warning',
      text: 'Resigned',
    }
  else
    return {
      color: 'info',
      text: 'Applied',
    }
}

const editItem = item => {
  editedIndex.value = labsKitList.value.indexOf(item)
  editedItem.value = { ...item }
  editDialog.value = true
}

const deleteItem = item => {
  editedIndex.value = labsKitList.value.indexOf(item)
  editedItem.value = { ...item }
  deleteDialog.value = true
}

const close = () => {
  editDialog.value = false
  editedIndex.value = -1
  editedItem.value = { ...defaultItem.value }
}
const closeAdd = () => {
  addDialog.value = false
}
const closeDelete = () => {
  deleteDialog.value = false
  editedIndex.value = -1
  editedItem.value = { ...defaultItem.value }
}

const update = async () => {
  const { valid } = await refVForm.value.validate()
  console.log(valid)
  if (valid) {
    if (editedIndex.value > -1){
      await store.dispatch('labKitUpdate',{
        id: editedItem.value.id,
        name: editedItem.value.name,
        amount: editedItem.value.amount,
      })
      Object.assign(labsKitList.value[editedIndex.value], editedItem.value)
        close()
    } else {
      await store.dispatch('labKitAdd',{
        name: defaultItem.value.name,
        amount: defaultItem.value.amount,
      })
      
      defaultItem.value.name = null
      defaultItem.value.amount=null
      closeAdd()
    }
    await store.dispatch('labsKitList')
      console.log('getLabsList',store.getters.getLabKitList)
        let list = store.getters.getLabKitList
        
        labsKitList.value = list
  }

  
}

const deleteItemConfirm = async() => {
  console.log('editedIndex.value',editedIndex.value,editedItem.value.id)
  await store.dispatch('labKitDelete',{
    id: editedItem.value.id
  })
  labsKitList.value.splice(editedIndex.value, 1)
  closeDelete()
}
const getlabsKitList = computed(async () => {
  store.dispatch('updateIsLoading', true)
  await store.dispatch('labsKitList')
  console.log('getLabsList',store.getters.getLabKitList)
    let list = store.getters.getLabKitList
    store.dispatch('updateIsLoading', false)
  labsKitList.value = list
    return labsKitList.value
});

onMounted(() => {
 

})
</script>

<template>
  <v-row>
    <v-col cols="12" md="12" v-if="getlabsKitList">
    <VCard title="Lab Kit">
      <VCardText >
          <VRow>
       
          <VCol cols="12" md="8" class="d-flex align-center" >
          <VBtn color="primary" prepend-icon="ri-add-line" @click="addDialog = true" style="display: none;">
            New Lab Kit
          </VBtn>
          </VCol>
        <VCol cols="12" md="4" class="d-flex justify-end">
          <VTextField
            v-model="search"
            label="Search"
            placeholder="Search ..."
            append-inner-icon="ri-search-line"
            single-line
            hide-details
            dense
            outlined
          />
        </VCol>
       </VRow>
        </VCardText>
      <VDataTable
        :headers="headers"
        :items="labsKitList"
        :search="search"
        :items-per-page="5"
        class="text-no-wrap"
      >
        <!-- full name -->
        <template #item.name="{ item }">
          <div class="d-flex align-center">
            <!-- avatar -->
            <VAvatar
              size="32"
              :color="item.avatar ? '' : 'primary'"
              :class="item.avatar ? '' : 'v-avatar-light-bg primary--text'"
              :variant="!item.avatar ? 'tonal' : undefined"
            >
              <VImg
                v-if="item.avatar"
                :src="item.avatar"
              />
              <span
                v-else
                class="text-sm"
              >{{ avatarText(item.name) }}</span>
            </VAvatar>

            <div class="d-flex flex-column ms-3">
              <span class="d-block font-weight-medium text-high-emphasis text-truncate">{{ item.name }}</span>
              <small>{{ item.post }}</small>
            </div>
          </div>
        </template>

        <!-- status -->
        <template #item.status="{ item }">
          <VChip
            :color="resolveStatusVariant(item.status).color"
            density="comfortable"
          >
            {{ resolveStatusVariant(item.status).text }}
          </VChip>
        </template>

        <!-- Actions -->
        <template #item.actions="{ item }">
          <div class="d-flex gap-1">
            <IconBtn
              size="small"
              @click="editItem(item)"
            >
              <VIcon icon="ri-pencil-line" />
            </IconBtn>
            <IconBtn
              size="small"
              @click="deleteItem(item)"
            >
              <VIcon icon="ri-delete-bin-line" />
            </IconBtn>
          </div>
        </template>
      </VDataTable>
    </VCard>

    </v-col>
  </v-row>
  <!-- 👉 Edit Dialog  -->
  <VDialog
    v-model="editDialog"
    max-width="600px"
  >
  <VForm ref="refVForm" >
    <VCard>
      <VCardTitle>
        <span class="headline">Edit Lab Kit</span>
      </VCardTitle>

      <VCardText>
        <VContainer >
         
          <VRow>
            <!-- fullName -->
            <VCol cols="12" md="12">
              <VTextField
                v-model="editedItem.name"
                label="Name"
                :rules="[requiredValidator]"
              />
            </VCol>

            <!-- email -->
            <VCol cols="12" sm="12" md="12">
              <VTextField
                v-model="editedItem.amount"
                label="Amount"
                :rules="[requiredValidator]"
              />
            </VCol>

         
           
            

      
            <!-- status -->
            
          </VRow>
        </VContainer>
      </VCardText>

      <VCardActions>
        <VSpacer />

        <VBtn
          color="error"
          variant="outlined"
          @click="close"
        >
          Cancel
        </VBtn>

        <VBtn
          color="success"
          variant="elevated"
          @click="update"
        >
          Save
        </VBtn>
        
      </VCardActions>
    </VCard>
  </VForm>
  </VDialog>

  <!-- 👉 Delete Dialog  -->
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

    <!-- 👉 Add Dialog  -->
  <VDialog
    v-model="addDialog"
    max-width="600px"
  >
    <VForm ref="refVForm" >
    <VCard>
      <VCardTitle>
        <span class="headline">Add Lab Kit</span>
      </VCardTitle>

      <VCardText>
        <VContainer >
         
          <VRow>
            <!-- fullName -->
            <VCol cols="12" md="12">
              <VTextField
                v-model="defaultItem.name"
                label="Name"
                :rules="[requiredValidator]"
              />
            </VCol>

            <!-- email -->
            <VCol cols="12" sm="12" md="12">
              <VTextField
                v-model="defaultItem.amount"
                label="Amount"
                :rules="[requiredValidator,requiredAmountFloat]"
              />
            </VCol>

         
           
            

      
            <!-- status -->
            
          </VRow>
        </VContainer>
      </VCardText>

      <VCardActions>
        <VSpacer />

        <VBtn
          color="error"
          variant="outlined"
          @click="closeAdd"
        >
          Cancel
        </VBtn>

        <VBtn
          color="success"
          variant="elevated"
          @click="update"
        >
          Save
        </VBtn>
        
      </VCardActions>
    </VCard>
  </VForm>
  </VDialog>
</template>
