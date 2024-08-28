<script setup>
import { passwordValidator } from '@/@core/utils/validators';
import avatar1 from '@images/avatars/avatar-1.png';
import { PerfectScrollbar } from 'vue3-perfect-scrollbar';
import { VForm } from 'vuetify/components/VForm';
import { useStore } from 'vuex';
const store = useStore()
const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
})
const accountData = {
  avatarImg: avatar1,
  name: '',
  first_name: '',
  last_name: '',
  email: '',
  phone_no: '',
  password: '',
  status:''
}
const accountDataLocal = ref(structuredClone(accountData))
const refInputEl = ref()
const ImageBase64 = ref();
const statusList = ref([
  { name: 'Active', abbreviation: '1' },
  { name: 'De-Active', abbreviation: '0' },
 
]);
const sortedStates = computed(() => {
    return states.value.slice().sort((a, b) => {
        return a.name.localeCompare(b.name);
    });
});
const isPasswordVisible = ref(false)
const emit = defineEmits(['update:isDrawerOpen','addedMessage'])
const formatPhoneNumber = () => {
  // Remove non-numeric characters from the input
  const numericValue = accountDataLocal.value.phone_no.replace(/\D/g, '');

  // Apply formatting logic
  if (numericValue.length <= 10) {
    accountDataLocal.value.phone_no = numericValue.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
  } else {
    // Limit the input to a maximum of 14 characters
    const truncatedValue = numericValue.slice(0, 10);
    accountDataLocal.value.phone_no = truncatedValue.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
  }
};
const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}
const genders = ref([
  { name: 'Male', abbreviation: 'Male' },
  { name: 'Female', abbreviation: 'Female' },
  { name: 'Other', abbreviation: 'Other' },
]);
const refVForm = ref()
const first_name = ref()
const last_name = ref()
const email = ref()
const status = ref()
const password = ref()
const phone_no = ref()
const changeAvatar = file => {
  const fileReader = new FileReader()
  const { files } = file.target
  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string') {
         accountDataLocal.value.avatarImg = fileReader.result
      }
       ImageBase64.value = fileReader.result.split(',')[1];
       
    }
  }
}

const onSubmit = async () => {
  const { valid } = await refVForm.value.validate()
  if (valid) {

      await store.dispatch('adminUserSave', {
          name: accountDataLocal.value.first_name,
          email:  accountDataLocal.value.email,
          last_name:  accountDataLocal.value.last_name,
          phone_no: accountDataLocal.value.phone_no,
          password: accountDataLocal.value.password,
          profile_pic: ImageBase64.value, //ecelData,
       
      })
  
    if (!store.getters.getErrorMsg) {
      emit('addedMessage', 'success')
          accountDataLocal.value.email = null
          accountDataLocal.value.name = null
           accountDataLocal.value.first_name = null
          accountDataLocal.value.phone_no = null
          accountDataLocal.value.last_name = null
          accountDataLocal.value.password = null
          ImageBase64.value = null
           accountDataLocal.value.avatarImg=avatar1
          emit('update:isDrawerOpen', false)
    }
  
  }
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
      title="Add User"
      @cancel="$emit('update:isDrawerOpen', false)"
    />
    <VDivider />

    <VCard flat>
      <PerfectScrollbar
        :options="{ wheelPropagation: false }"
        class="h-100"
      >
        <VCardText style="block-size: calc(100vh - 5rem);">
          <VForm
            ref="refVForm"
            @submit.prevent=""
          >
            <VRow>
             
        
             <div class="d-flex mb-10">
            
            <!-- 👉 Avatar -->
            <VAvatar
              rounded
              size="100"
              class="me-6"
              :image="accountDataLocal.avatarImg"
            />

            <!-- 👉 Upload Photo -->
            <form class="d-flex flex-column justify-center gap-4">
              <div class="d-flex flex-wrap gap-4">
                <VBtn
                  color="primary"
                  @click="refInputEl?.click()"
                >
                  <VIcon
                    icon="ri-upload-cloud-line"
                    class="d-sm-none"
                  />
                  <span class="d-none d-sm-block">Upload Logo</span>
                </VBtn>
                <input
                  ref="refInputEl"
                  type="file"
                  name="file"
                  accept=".jpeg,.png,.jpg,GIF"
                  hidden
                  @input="changeAvatar"
                >
              </div>
              <p class="text-body-1 mb-0">
                Allowed JPG, GIF or PNG. Max size of 800K
              </p>
            </form>
          </div>
              <VCol cols="12" md="6">
                <VTextField
                  v-model="accountDataLocal.first_name"
                  label="First Name"
                  :rules="[requiredValidator]"
                  placeholder="First Name"
                />
              </VCol>
              <VCol cols="12" md="6">
                    <VTextField
                      v-model="accountDataLocal.last_name"
                      label="Last Name"
                      :rules="[requiredValidator]"
                      placeholder="Last Name"
                    />
                  </VCol>
              <VCol cols="12" md="6">
                <VTextField
                  v-model="accountDataLocal.email"
                  label="Email Address"
                  :rules="[requiredValidator, emailValidator]"
                  placeholder="johndoe@email.com"
                />
              </VCol>
              <VCol
                  cols="12"
                  md="6"
                >
                  <VTextField
                    v-model="accountDataLocal.password"
                    label="Password"
                    placeholder="············"
                    :type="isPasswordVisible ? 'text' : 'password'"
                    :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
                    @click:append-inner="isPasswordVisible = !isPasswordVisible"
                    :rules="[requiredValidator, passwordValidator]"
                  />
                </VCol>
                <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="accountDataLocal.phone_no"
                  label="Phone Number"
                  placeholder="+1 (917) 543-9876"
                  :rules="[requiredPhone, validUSAPhone]"
                 
                  @input="formatPhoneNumber"
                  max="14"
                  pattern="^\(\d{3}\) \d{3}-\d{4}$"
                />
              </VCol>
             <VCol cols="12" md="6">
            
             
              </VCol>


             



             

              <VCol cols="12">
                <div class="d-flex justify-start">
                  <VBtn
                    type="submit"
                    color="primary"
                    class="me-4"
                    @click="onSubmit"
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
