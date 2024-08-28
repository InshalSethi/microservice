<script setup>
import API from '@/api';
import { PRODUCT_LIST_API } from '@/constants';
import AddProduct from '@/pages/products/AddProduct.vue';
import EditProduct from '@/pages/products/EditProduct.vue';
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
const imageBase64 = ref(null)
    const excelBase64 = ref(null)
const currencies = ref([
  { code: 'USD', name: 'US Dollar', sign: '$' },
  { code: 'EUR', name: 'Euro', sign: '€' },
  { code: 'GBP', name: 'British Pound', sign: '£' },
  { code: 'JPY', name: 'Japanese Yen', sign: '¥' },
])
const setCurrency = (code, sign) => {
      currencySign.value = sign;
      // You can perform additional operations with the selected currency code if needed
    };
// status options
const selectedOptions = [
  {
    text: 'Current',
    value: 1,
  },
  {
    text: 'Professional',
    value: 2,
  },
  {
    text: 'Rejected',
    value: 3,
  },
  {
    text: 'Resigned',
    value: 4,
  },
  {
    text: 'Applied',
    value: 5,
  },
]

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
    title: 'Price',
    key: 'price',
  },
  {
    title: 'Quantity',
    key: 'quantity',
  },
  {
    title: 'Description',
    key: 'description',
  },
  {
    title: 'ACTIONS',
    key: 'actions',
    searchable:false
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
  
  await store.dispatch('productGetByID', {
    id: item.id,
  })
  
  editedItem.value = store.getters.getSingleProduct
  console.log(store.getters.getSingleProduct)
  //editDialog.value = true
}
const addItem = item => {
  addDialog.value = true
}
const deleteItem = item => {
  editedIndex.value = medicineList.value.indexOf(item)
  editedItem.value = { ...item }
  deleteDialog.value = true
}

const selectfile = (data) => {
  if (data == 'dropdown') {
    selectDropdown.value = true
  } else {
    selectDropdown.value = false
  }
  
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
const convertImageToBase64 = (event) => {
      const file = event.target.files[0]
      const reader = new FileReader()
      reader.readAsDataURL(file)
      reader.onload = () => {
        imageBase64.value = reader.result.split(',')[1]
      }
    }

    const convertExcelToBase64 = (event) => {
      const file = event.target.files[0]
      const reader = new FileReader()
      reader.readAsDataURL(file)
      reader.onload = () => {
        excelBase64.value = reader.result.split(',')[1]
      }
    }


const deleteItemConfirm = async () => {
  
  await store.dispatch('productDelete',{
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
const generateSlug = () => {
  console.log(editedItem.title)
      if (editedItem.value.title) {
        editedItem.value.slug = editedItem.value.title
          .toString()
          .trim()
          .toLowerCase()
          .replace(/\s+/g, '-')
          .replace(/[^\w-]+/g, '')
          .replace(/--+/g, '-')
          .replace(/^-+/, '')
          .replace(/-+$/, '')
      } else if (defaultItem.value.title) { 
        defaultItem.value.slug = defaultItem.value.title 
          .toString()
          .trim()
          .toLowerCase()
          .replace(/\s+/g, '-')
          .replace(/[^\w-]+/g, '')
          .replace(/--+/g, '-')
          .replace(/^-+/, '')
          .replace(/-+$/, '')
      } else {
        editedItem.slug = ''
      }
    }
const serverItems = ref([]);
const loading = ref(true);
const totalItems = ref(0);

// Method to load items
const loadItems = debounce( async ( { page,  itemsPerPage, sortBy }) =>  {
  const payload = {
    page,
    itemsPerPage,
    sortBy,
    filters:{

    },
    search:search.value,
  }
  console.log("records",page, itemsPerPage, sortBy);
  loading.value = true;
  const data = await API.getDataTableRecord(PRODUCT_LIST_API, payload, headers);
  serverItems.value   = data.items;
  totalItems.value = data.total;
  loading.value = false;  
},500);
const itemsPerPage = ref(30);
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
    <VCard title="Products">

      <VCardText >
        <VRow>
       
          <VCol cols="12" md="8" class="d-flex align-center">
          <VBtn color="primary" prepend-icon="ri-add-line" @click="isAddCustomerDrawerOpen = !isAddCustomerDrawerOpen"  v-if="$can('read', 'Product Add')">
            New Product
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
        :item-value="name"
        @update:options="loadItems"
      >
        <!-- full name -->
        <template #item.title="{ item }">
          <div class="d-flex align-center">
            <!-- avatar -->
            <VAvatar
              size="32"
              :color="item.image_url ? '' : 'primary'"
              :class="item.image_url ? '' : 'v-avatar-light-bg primary--text'"
              :variant="!item.image_url ? 'tonal' : undefined"
            >
              <VImg
                v-if="item.image_url"
                :src="item.image_url"
              />
              <span
                v-else
                class="text-sm"
              >{{ avatarText(item.name) }}</span>
            </VAvatar>

            <div class="d-flex flex-column ms-3">
              <span class="d-block font-weight-medium text-high-emphasis text-truncate">{{ item.title }}</span>
          
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
               v-if="$can('read', 'Product Edit')"
            >
              <VIcon icon="ri-pencil-line" />
            </IconBtn>
            <IconBtn
              size="small"
              @click="deleteItem(item)"
              v-if="$can('read', 'Product Delete')"
            >
              <VIcon icon="ri-delete-bin-line" />
            </IconBtn>
          </div>
        </template>
        </v-data-table-server>
      <!-- </VDataTable> -->
    </VCard>
    </v-col>

    <AddProduct v-model:is-drawer-open="isAddCustomerDrawerOpen"     @addedMessage="handleParentAdded" />
   <EditProduct v-model:is-drawer-open="isEditCustomerDrawerOpen"  :user-data="store.getters.getSingleProduct"    @addedMessage="handleParentAdded" />
  </v-row>
  <!-- 👉 Edit Dialog  -->
  <VDialog
    v-model="editDialog"
    max-width="600px"
  >
     <VForm ref="refVForm" >
      <VCard>
        <VCardTitle>
          <span class="headline">Edit Medicine</span>
        </VCardTitle>

        <VCardText>
          <VContainer >
          
            <VRow>
              <!-- fullName -->
              <VCol cols="12" md="12">
                <VTextField
                  v-model="editedItem.title"
                  label="Title"
                  :rules="[requiredValidator]"
                  @input="generateSlug"
                />
              </VCol>
              <VCol cols="12" md="12">
                <VTextField
                  v-model="editedItem.slug"
                  label="Slug"
                  :rules="[requiredValidator]"
                />
              </VCol>

              <VCol cols="12" sm="6" md="12">
              <VTextField
                v-model="editedItem.price"
                label="Price"
                :rules="[requiredValidator]"
                prepend-inner-icon
              >
                <template v-slot:prepend-inner>
                  <VMenu
                    offset-y
                    transition="slide-y-transition"
                    :close-on-content-click="false"
                  >
                    <template v-slot:activator="{ props }">
                      <span v-bind="props">{{ currencySign  || '$money' }}</span>
                    </template>
                    <VList>
                      <VListItem
                        v-for="currency in currencies"
                        :key="currency.code"
                        @click.prevent="setCurrency(currency.code, currency.sign)"
                      >
                        <VListItemTitle>{{ currency.name }}</VListItemTitle>
                      </VListItem>
                    </VList>
                  </VMenu>
                </template>
              </VTextField>
            </VCol>

              <VCol cols="12" sm="6" md="12">
                <VTextField
                  v-model="editedItem.list_one_title"
                  label="Short Detail"
                  :rules="[requiredValidator]"
                />
              </VCol>
              <VCol cols="12" sm="6" md="12">
                <VTextField
                  v-model="editedItem.list_sub_title"
                  label="Short Description"
                  :rules="[requiredValidator]"
                />
              </VCol>
            
              <VCol cols="12" sm="6" md="12">
              <VTextarea
                v-model="editedItem.list_two_title"
                label="Full Description"
                :rules="[requiredValidator]"
              />
            </VCol>

            <VCol cols="12" md="8">
                <div v-if="selectDropdown==false">
                
                  <VFileInput
                    v-model="excelFile"
                    accept=".xlsx,.xls"
                    show-size
                    counter
                    label="Select an Excel file to upload"
                    @change="convertExcelToBase64"

                  />
                </div>
                <div v-if="selectDropdown==true">
                <VSelect
                    v-model="selectdataList"
                    :items="store.getters.getQuestioneriesList"
                    item-title="product_file_path"
                    item-value="product_file_path"
                    label="Select File"
                    prepend-icon="ri-file-line"
                    variant="outlined"
                  />
                </div>
            </VCol>
            <VCol cols="12" md="4">
              <VBtn color="success" variant="outlined" @click="selectfile('dropdown')"     class="custom-button" v-if="selectDropdown==false">Select File</VBtn>
              <VBtn color="success" variant="outlined" @click="selectfile('upload')"      class="custom-button" v-if="selectDropdown==true">Upload File</VBtn>
            </VCol>
            <VCol cols="12" md="12">
              <div>
            
                <VFileInput
                  v-model="imageFile"
                  accept="image/*"
                  show-size
                  counter
                  label="Select an image to upload"
                  @change="convertImageToBase64"
                />
              </div>
            </VCol> 
          
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
    <VForm ref="refVFormAdd" >
      <VCard>
        <VCardTitle>
          <span class="headline">Add Medicine</span>
        </VCardTitle>

        <VCardText>
          <VContainer >
          
            <VRow>
              <!-- fullName -->
              <VCol cols="12" md="12">
                <VTextField
                  v-model="defaultItem.title"
                  label="Title"
                  :rules="[requiredValidator]"
                  @input="generateSlug"
                />
              </VCol>
              <VCol cols="12" md="12">
                <VTextField
                  v-model="defaultItem.slug"
                  label="Slug"
                  :rules="[requiredValidator]"
                />
              </VCol>

              <VCol cols="12" sm="6" md="12">
              <VTextField
                v-model="defaultItem.price"
                label="Price"
                :rules="[requiredValidator]"
                prepend-inner-icon
              >
                <template v-slot:prepend-inner>
                  <VMenu
                    offset-y
                    transition="slide-y-transition"
                    :close-on-content-click="false"
                  >
                    <template v-slot:activator="{ props }">
                      <span v-bind="props">{{ currencySign  || '$money' }}</span>
                    </template>
                    <VList>
                      <VListItem
                        v-for="currency in currencies"
                        :key="currency.code"
                        @click.prevent="setCurrency(currency.code, currency.sign)"
                      >
                        <VListItemTitle>{{ currency.name }}</VListItemTitle>
                      </VListItem>
                    </VList>
                  </VMenu>
                </template>
              </VTextField>
            </VCol>

              <VCol cols="12" sm="6" md="12">
                <VTextField
                  v-model="defaultItem.list_one_title"
                  label="Short Detail"
                  :rules="[requiredValidator]"
                />
              </VCol>
              <VCol cols="12" sm="6" md="12">
                <VTextField
                  v-model="defaultItem.list_sub_title"
                  label="Short Description"
                  :rules="[requiredValidator]"
                />
              </VCol>
            
              <VCol cols="12" sm="6" md="12">
              <VTextarea
                v-model="defaultItem.list_two_title"
                label="Full Description"
                :rules="[requiredValidator]"
              />
            </VCol>

            <VCol cols="12" md="8">
                <div v-if="selectDropdown==false">
                
                  <VFileInput
                    v-model="excelFile"
                    accept=".xlsx,.xls"
                    :rules="[requiredExcelValidator]"
                    show-size
                    counter
                    label="Select an Excel file to upload"
                    @change="convertExcelToBase64"

                  />
                </div>
                <div v-if="selectDropdown==true">
                <VSelect
                    v-model="selectdataList"
                    :items="store.getters.getQuestioneriesList"
                    item-title="product_file_path"
                    item-value="product_file_path"
                    label="Select File"
                    prepend-icon="ri-file-line"
                    variant="outlined"
                  />
                </div>
            </VCol>
            <VCol cols="12" md="4">
              <VBtn color="success" variant="outlined" @click="selectfile('dropdown')"     class="custom-button" v-if="selectDropdown==false">Select File</VBtn>
              <VBtn color="success" variant="outlined" @click="selectfile('upload')"      class="custom-button" v-if="selectDropdown==true">Upload File</VBtn>
            </VCol>
            <VCol cols="12" md="12">
              <div>
            
                <VFileInput
                  v-model="imageFile"
                  accept="image/*"
                  :rules="[requiredImageValidator]"
                  show-size
                  counter
                  label="Select an image to upload"
                  @change="convertImageToBase64"
                />
              </div>
            </VCol> 
          
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
            @click="save"
          >
            Save
          </VBtn>
          
        </VCardActions>
      </VCard>
    </VForm>
  </VDialog>
</template>
<style scoped>
.custom-button {
  width: 100%;
  height: 48px; /* This value should match the height of your input fields */
}
</style>
