<script setup>
import API from '@/api';
import { ADMIN_USER_LIST_API } from '@/constants';
import AddUser from '@/pages/admin-users/AddUser.vue';
import EditUser from '@/pages/admin-users/EditUser.vue';
import debounce from 'lodash.debounce';
import { useStore } from 'vuex';
const isAddCustomerDrawerOpen = ref(false)
const isEditCustomerDrawerOpen = ref(false)
const store = useStore()
const editDialog = ref(false)
const addDialog = ref(false)
const deleteDialog = ref(false)
const search = ref('')
const refVForm = ref(null)
const refVFormAdd = ref(null)
const selectDropdown = ref(false)
const selectdataList = ref(null)
const defaultItem = ref({
  id: -1,
  title: '',
  slug: '',
  list_one_title: '',
  list_sub_title: '',
  list_two_title: '',
  price: '',
  currency:""
})
const requiredImageValidator = (value) => !!value || 'Please select an image file.'
    const requiredExcelValidator = (value) => !!value || 'Please select an Excel file.'
const imageFile = ref(null)
    const excelFile = ref(null)
const editedItem = ref(defaultItem.value)
const editedIndex = ref(-1)
const medicineList = ref([])
const isLoading = ref(false)
const currencySign = ref('$');




// headers
const headers = [
{
    title: 'ID',
    key: 'id',
  },
  {
    title: 'Name',
    key: 'name',
  },
 
  {
    title: 'email',
    key: 'email',
  },
 
  {
    title: 'ACTIONS',
    key: 'actions',
  },
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


const editItem = async (item) => {
  isEditCustomerDrawerOpen.value = true
  
  await store.dispatch('singleUserEdit', {
    id: item.id,
  })
  
  editedItem.value = store.getters.getSingleUser
  console.log(store.getters.getSingleUser)
 // editDialog.value = true
}
const addItem = item => {
  addDialog.value = true
}
const deleteItem = item => {
  editedIndex.value = medicineList.value.indexOf(item)
  editedItem.value = { ...item }
  deleteDialog.value = true
}




const closeDelete = () => {
  deleteDialog.value = false
  editedIndex.value = -1
  editedItem.value = { ...defaultItem.value }
}





const deleteItemConfirm = async () => {
  
  await store.dispatch('medicineDelete',{
    id: editedItem.value.id
  })
  
  closeDelete()
 loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] });
  defaultItem.value.id= null
  defaultItem.value.title= null
  defaultItem.value.slug= null
  defaultItem.value.list_one_title= null
  defaultItem.value.list_sub_title= null
  defaultItem.value.list_two_title= null
 defaultItem.value.price =null
  defaultItem.value.currency=null

}

const serverItems = ref([]);
const loading = ref(true);
const totalItems = ref(0);
const itemsPerPage = ref(5);

const loadItems = debounce( async ( { page,  itemsPerPage, sortBy }) =>  {
  const payload = {
    page,
    itemsPerPage,
    sortBy,
    filters:{
    },
    search:search.value,
  }
  console.log("records",page, itemsPerPage, sortBy , ADMIN_USER_LIST_API);
  loading.value = true;
  const data = await API.getDataTableRecord(ADMIN_USER_LIST_API, payload, headers);
  console.log('patientData',data);
  serverItems.value   = data.items;
  totalItems.value = data.total;
  loading.value = false;  
  
},500);
onMounted(() => {
  
})
const handleParentAdded = async (msg) => {
  if (msg == 'success') {
      store.dispatch('updateIsLoading', true)
    loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] });
         store.dispatch('updateIsLoading', false)
  }
  // You can also trigger a toast or snackbar here to show the message
  // For example, if using Vuetify:
  // showSnackbar(msg)
}
</script>

<template>
  <v-row>
    <v-col cols="12" md="12">
    <VCard title="Users">

      <VCardText >
        <VRow>
       
          <VCol cols="12" md="8" class="d-flex align-center">
          <VBtn color="primary" prepend-icon="ri-add-line" @click="isAddCustomerDrawerOpen = !isAddCustomerDrawerOpen" v-if="$can('read', 'Admin Add')">
            New User
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
      
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        :headers="headers"
        :items="serverItems"
        :items-length="totalItems"
        :loading="loading"
        :search="search"
        @update:options="loadItems"
      >
        <!-- full name -->
        <template #item.name="{ item }">
          <div class="d-flex align-center">
            <!-- avatar -->
            <VAvatar
              size="32"
              :color="item.image_path ? '' : 'primary'"
              :class="item.image_path ? '' : 'v-avatar-light-bg primary--text'"
              :variant="!item.image_path ? 'tonal' : undefined"
            >
              <VImg
                v-if="item.image_path"
                :src="item.image_path"
              />
              <span
                v-else
                class="text-sm"
              >{{ avatarText(item.name) }}</span>
            </VAvatar>

            <div class="d-flex flex-column ms-3">
              <span class="d-block font-weight-medium text-high-emphasis text-truncate">{{ item.name}}</span>
          
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
               @click="editItem(item  )"
                v-if="$can('read', 'Admin Edit')"
            >
              <VIcon icon="ri-pencil-line" />
            </IconBtn>
            <IconBtn
              size="small"
              @click="deleteItem(item)"
              style="display: none;"
            >
              <VIcon icon="ri-delete-bin-line" />
            </IconBtn>
          </div>
        </template>
        </v-data-table-server>
      <!-- </VDataTable> -->
    </VCard>
    </v-col>

    <AddUser v-model:is-drawer-open="isAddCustomerDrawerOpen"     @addedMessage="handleParentAdded" />
   <EditUser v-model:is-drawer-open="isEditCustomerDrawerOpen"  :user-data="store.getters.getSingleUser"    @addedMessage="handleParentAdded" />
  </v-row>
 
 

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




</template>
<style scoped>
.custom-button {
  width: 100%;
  height: 48px; /* This value should match the height of your input fields */
}
</style>
