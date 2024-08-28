<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar';
import { VForm } from 'vuetify/components/VForm';
import { useStore } from 'vuex';
const store = useStore()
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

const editDialog = ref(false)
const refVForm = ref(null)
const defaultItem = ref({
  id: -1,
  name: '',
  quantity: '',
  description: '',
  price: '',
  currency:""
})
const itemId = ref()
const editedItem = ref(defaultItem.value)
const editedIndex = ref(-1)
const currencySign = ref('$');
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

const close = () => {
  editDialog.value = false
  editedIndex.value = -1
  editedItem.value = { ...defaultItem.value }
}


const getSingleProduct = computed(async () => {
  console.log(props.userData)
   console.log(editedItem.value.name)
      itemId.value=props.userData.id
      editedItem.value.id= props.userData.id
      editedItem.value.name= props.userData.name
      editedItem.value.quantity= props.userData.quantity
      editedItem.value.description= props.userData.description
      editedItem.value.price =props.userData.price
      editedItem.value.currency = props.userData.currency
     
   

        
});
const update = async () => {
  const { valid } = await refVForm.value.validate()
  console.log(valid)
  if (valid) {
    try {
          
            await store.dispatch('productUpdate', {
              id:editedItem.value.id,
              name: editedItem.value.name,
              quantity: editedItem.value.quantity,
              description: editedItem.value.description,
              price: editedItem.value.price,
              currency: currencySign.value,
            })
           
          
        if (!store.getters.getErrorMsg) {
          emit('addedMessage', 'success')
            editedItem.value.id= null
            editedItem.value.name= null
            editedItem.value.price= null
            editedItem.value.quantity= null
            editedItem.value.description= null
            editedItem.value.currency=null
            emit('update:isDrawerOpen', false)
        }
        


        } catch (error) {
          console.error(error)
        }
    
    
    close()
  }
}
const emit = defineEmits(['update:isDrawerOpen','addedMessage'])

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

  

const resetForm = () => {
  refVForm.value?.reset()
  emit('update:isDrawerOpen', false)
}
</script>

<template>
  <VNavigationDrawer
    :model-value="props.isDrawerOpen"
    temporary
    location="end"
    width="800"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Edit Product"
      @cancel="$emit('update:isDrawerOpen', false)"
    />
    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
      <VCardText style="block-size: calc(100vh - 5rem);" v-if="getSingleProduct">
          <VForm
            ref="refVForm"
            @submit.prevent=""
          >
            
            
           
                 <VRow>
              <!-- fullName -->
              <VCol cols="12" md="12">
                <VTextField
                  v-model="editedItem.name"
                  label="Name"
                  :rules="[requiredValidator]"
                  @input="generateSlug"
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
                  v-model="editedItem.quantity"
                  label="Short Detail"
                  :rules="[requiredValidator]"
                />
              </VCol>
            
              <VCol cols="12" sm="6" md="12">
              <VTextarea
                v-model="editedItem.description"
                label="Description"
                :rules="[requiredValidator]"
              />
            </VCol>

              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    color="primary"
                    class="me-4"
                    @click="update"
                  >
                    Save
                  </VBtn>
                  <VBtn
                    color="error"
                    variant="outlined"
                    @click="resetForm"
                  >
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
