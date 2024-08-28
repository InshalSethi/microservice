<script setup>
import avatar1 from '@images/avatars/avatar-1.png';
import {
emailValidator,
requiredEmail,
requiredName,
requiredPhone,
validUSAPhone
} from '@validators';
import { useStore } from 'vuex';

const store = useStore();
let imageBlob = null;
const profileData = ref([]);
const errors = ref({
  name: undefined,
  email: undefined,
  phone_no:undefined,
})
const accountData = {
  avatarImg: avatar1,
  name: '',
  last_name: '',
  email: '',
  phone_no: '',
}

const refVForm = ref()
const refInputEl = ref()
const isConfirmDialogOpen = ref(false)
const accountDataLocal = ref(structuredClone(accountData))
const isAccountDeactivated = ref(false)
const validateAccountDeactivation = [v => !!v || 'Please confirm account deactivation']
const getIsTonalSnackbarVisible = ref(false);
const ImageBase64 = ref();
const resetForm = () => {
  accountDataLocal.value = structuredClone(accountData)
}

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
onMounted(async () => {
  await store.dispatch('adminDetial');
  let list  = await store.getters.getAdminDetail
  accountDataLocal.value.email = list.email
  accountDataLocal.value.name = list.name
  accountDataLocal.value.last_name = list.last_name;
  accountDataLocal.value.phone_no = list.phone_no
  if(!list.image_path){
    accountDataLocal.value.avatarImg = avatar1;
  }else{
    accountDataLocal.value.avatarImg = list.image_path
  } 
  
});
// reset avatar image
const resetAvatar = () => {
  accountDataLocal.value.avatarImg = accountData.avatarImg
}

const timezones = [
  '(GMT-11:00) International Date Line West',
  '(GMT-11:00) Midway Island',
  '(GMT-10:00) Hawaii',
  '(GMT-09:00) Alaska',
  '(GMT-08:00) Pacific Time (US & Canada)',
  '(GMT-08:00) Tijuana',
  '(GMT-07:00) Arizona',
  '(GMT-07:00) Chihuahua',
  '(GMT-07:00) La Paz',
  '(GMT-07:00) Mazatlan',
  '(GMT-07:00) Mountain Time (US & Canada)',
  '(GMT-06:00) Central America',
  '(GMT-06:00) Central Time (US & Canada)',
  '(GMT-06:00) Guadalajara',
  '(GMT-06:00) Mexico City',
  '(GMT-06:00) Monterrey',
  '(GMT-06:00) Saskatchewan',
  '(GMT-05:00) Bogota',
  '(GMT-05:00) Eastern Time (US & Canada)',
  '(GMT-05:00) Indiana (East)',
  '(GMT-05:00) Lima',
  '(GMT-05:00) Quito',
  '(GMT-04:00) Atlantic Time (Canada)',
  '(GMT-04:00) Caracas',
  '(GMT-04:00) La Paz',
  '(GMT-04:00) Santiago',
  '(GMT-03:30) Newfoundland',
  '(GMT-03:00) Brasilia',
  '(GMT-03:00) Buenos Aires',
  '(GMT-03:00) Georgetown',
  '(GMT-03:00) Greenland',
  '(GMT-02:00) Mid-Atlantic',
  '(GMT-01:00) Azores',
  '(GMT-01:00) Cape Verde Is.',
  '(GMT+00:00) Casablanca',
  '(GMT+00:00) Dublin',
  '(GMT+00:00) Edinburgh',
  '(GMT+00:00) Lisbon',
  '(GMT+00:00) London',
]

const currencies = [
  'USD',
  'EUR',
  'GBP',
  'AUD',
  'BRL',
  'CAD',
  'CNY',
  'CZK',
  'DKK',
  'HKD',
  'HUF',
  'INR',
]

 
const onSubmit = async () => {
  const { valid } = await refVForm.value.validate()
  console.log(valid)
  if (valid) {
    try {
          console.log(ImageBase64.value);
            await store.dispatch('profileUpdate',{
              name: accountDataLocal.value.name,
              last_name:  accountDataLocal.value.last_name,
              phone_no: accountDataLocal.value.phone_no,
              image: ImageBase64.value, //ecelData,
            })
        } catch (error) {
          console.error(error)
        }
  await store.dispatch('adminDetial');
    let list  = await store.getters.getAdminDetail
console.log('list',list)
  accountDataLocal.value.avatarImg = list.image_path
  accountDataLocal.value.name = list.name
  accountDataLocal.value.last_name = list.last_name
  accountDataLocal.value.phone_no = list.phone_no
  }
}  



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
</script>

<template>

  <VRow>
  
    <VCol cols="12">
      <VCard>
        <VCardText>
          <div class="d-flex mb-10">
            <VSnackbar v-model="getIsTonalSnackbarVisible" :timeout="5000" location="top end" variant="flat"
              color="success">
              <VIcon
                    class="ri-success-line success-icon"
                  /> Profile Update Successfully
            </VSnackbar>
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

          <!-- 👉 Form -->
          <VForm ref="refVForm">
            <VRow>
              <!-- 👉 First Name -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="accountDataLocal.name"
                label="Name"
                :rules="[requiredName]"
                :error-messages="errors.name"
                />
              </VCol>

              <!-- 👉 Last Name -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="accountDataLocal.last_name"
                  placeholder="Doe"
                  label="Last Name"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField readonly
                  v-model="accountDataLocal.email"
                  label="E-mail"
                  placeholder="johndoe@gmail.com"
                  type="email"
                  :rules="[requiredEmail, emailValidator]"
                  :error-messages="errors.email"
                />
              </VCol>

              <!-- 👉 Organization -->
           

              <!-- 👉 Phone -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="accountDataLocal.phone_no"
                  label="Phone Number"
                  placeholder="+1 (917) 543-9876"
                  :rules="[requiredPhone, validUSAPhone]"
                  :error-messages="errors.phone"
                  @input="formatPhoneNumber"
                  max="14"
                  pattern="^\(\d{3}\) \d{3}-\d{4}$"
                />
              </VCol>



              
            

              



              <!-- 👉 Form Actions -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn  @click.prevent="onSubmit">Save changes</VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>

  </VRow>

  <!-- Confirm Dialog -->
  <ConfirmDialog
    v-model:isDialogVisible="isConfirmDialogOpen"
    confirmation-question="Are you sure you want to deactivate your account?"
    confirm-title="Deactivated!"
    confirm-msg="Your account has been deactivated successfully."
    cancel-title="Cancelled"
    cancel-msg="Account Deactivation Cancelled!"
  />
</template>
