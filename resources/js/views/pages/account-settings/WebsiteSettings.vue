<script setup>
import { useStore } from 'vuex';
const store = useStore();
const accountData = {
  favicon:'',
  domain_name: '',
  footer_text: '',
  header_title: '',
  logo: '',
  plan_description: '',
  plan_main_title:'',
 id:'',
}
const refVForm = ref(null)
const refInputEl = ref()
const refInputElFavicon = ref()
const isConfirmDialogOpen = ref(false)
const accountDataLocal = ref(structuredClone(accountData))
const isAccountDeactivated = ref(false)
const validateAccountDeactivation = [v => !!v || 'Please confirm account deactivation']
const imageBase64 = ref(null)
 const faviconBase64 = ref(null)
const resetForm = () => {
  accountDataLocal.value = structuredClone(accountData)
}
const resetFormFavicon = () => {
  accountDataLocal.value = structuredClone(accountData)
}
const changeAvatar = file => {
  const fileReader = new FileReader()
  const { files } = file.target
  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string') {
            accountDataLocal.value.logo = fileReader.result
      }
    
          imageBase64.value = fileReader.result.split(',')[1]
    }
  }
}
const changeAvatarFavicon = file => {
  const fileReader = new FileReader()
  const { files } = file.target
  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string') {
         accountDataLocal.value.favicon = fileReader.result
      }
       faviconBase64.value = fileReader.result.split(',')[1]
    }
  }
}

// reset avatar image
const resetAvatar = () => {
  accountDataLocal.value.logo = accountData.logo
}
const resetAvatarFavicon = () => {
  accountDataLocal.value.favicon = accountData.favicon
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
onMounted(async () => {
  await store.dispatch('siteSetting');
  let list = await store.getters.getSiteSetting 
console.log('list',list)
  accountDataLocal.value.logo = list.logo
  accountDataLocal.value.favicon = list.favicon
  accountDataLocal.value.plan_main_title = list.plan_main_title
  accountDataLocal.value.plan_description = list.plan_description
  accountDataLocal.value.header_title = list.header_title
  accountDataLocal.value.footer_text = list.footer_text
  accountDataLocal.value.domain_name = list.domain_name
  accountDataLocal.value.id = list.id
});
const convertImageToBase64 = (event) => {
      const file = event.target.files[0]
      const reader = new FileReader()
      reader.readAsDataURL(file)
      reader.onload = () => {
        imageBase64.value = reader.result.split(',')[1]
      }
    }
const save = async () => {
  const { valid } = await refVForm.value.validate()
  console.log(valid)
  if (valid) {
    try {
          console.log(imageBase64.value)
            await store.dispatch('siteSettingUpdate',{
              plan_main_title: accountDataLocal.value.plan_main_title,
              plan_description:  accountDataLocal.value.plan_description,
              header_title: accountDataLocal.value.header_title,
              footer_text: accountDataLocal.value.footer_text,
              domain_name: accountDataLocal.value.domain_name,
              id: accountDataLocal.value.id,
              logo:imageBase64.value, //ecelData,
              favicon:faviconBase64.value//imageBase64.value
            })
           
        
        } catch (error) {
          console.error(error)
        }
   await store.dispatch('siteSetting');
  let list = await store.getters.getSiteSetting 
console.log('list',list)
  accountDataLocal.value.logo = list.logo
  accountDataLocal.value.favicon = list.favicon
  accountDataLocal.value.plan_main_title = list.plan_main_title
  accountDataLocal.value.plan_description = list.plan_description
  accountDataLocal.value.header_title = list.header_title
  accountDataLocal.value.footer_text = list.footer_text
  accountDataLocal.value.domain_name = list.domain_name
  }
}
</script>

<template>
  <VRow>

      <VCol cols="12">
        <VCard>
          <VCardText>
            <div class="d-flex mb-10">
              <!-- 👉 Avatar -->
              <VAvatar
                rounded
                size="70"
                class="me-6"
                :image="accountDataLocal.logo"
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
                    accept=".jpeg,.png,.webp,.jpg,GIF"
                    hidden
                    @input="changeAvatar"
                  >
                  <VBtn
                    type="reset"
                    color="error"
                    variant="outlined"
                    @click="resetAvatar"
                  >
                    <span class="d-none d-sm-block">Reset</span>
                    <VIcon
                      icon="ri-refresh-line"
                      class="d-sm-none"
                    />
                  </VBtn>
                </div>
                <p class="text-body-1 mb-0">
                  Allowed JPG, GIF ,webp or PNG. Max size of 800K
                </p>
              </form>
            </div>
            
            <div class="d-flex mb-10">
                <!-- 👉 Avatar -->
                <VAvatar
                  rounded
                  size="70"
                  class="me-6"
                  :image="accountDataLocal.favicon"
                />

                <!-- 👉 Upload Photo -->
                <form class="d-flex flex-column justify-center gap-4">
                  <div class="d-flex flex-wrap gap-4">
                    <VBtn
                      color="primary"
                      @click="refInputElFavicon?.click()"
                    >
                      <VIcon
                        icon="ri-upload-cloud-line"
                        class="d-sm-none"
                      />
                      <span class="d-none d-sm-block">Upload Favicon</span>
                    </VBtn>
                    <input
                      ref="refInputElFavicon"
                      type="file"
                      name="file"
                    accept=".jpeg,.png,.webp,.jpg,GIF"
                      hidden
                      @input="changeAvatarFavicon"
                    >
                    <VBtn
                      type="reset"
                      color="error"
                      variant="outlined"
                      @click="resetAvatarFavicon"
                    >
                      <span class="d-none d-sm-block">Reset</span>
                      <VIcon
                        icon="ri-refresh-line"
                        class="d-sm-none"
                      />
                    </VBtn>
                  </div>
                  <p class="text-body-1 mb-0">
                    Allowed JPG,webp, GIF or PNG. Max size of 800K
                  </p>
                </form>
            </div>
            <!-- 👉 Form -->
                <VForm ref="refVForm" >
              <VRow>
                <!-- 👉 First Name -->
                <VCol
                  md="6"
                  cols="12"
                >
                  <VTextField
                    v-model="accountDataLocal.plan_main_title"
                    placeholder="Plan Main Page Title"
                    label="Plan Page Main Title"
                    :rules="[requiredValidator]"
                  />
                </VCol>

                <!-- 👉 Last Name -->
                <VCol
                  md="6"
                  cols="12"
                >
                  <VTextField
                    v-model="accountDataLocal.plan_description"
                    placeholder="Doe"
                    label="Plan Description"
                    :rules="[requiredValidator]"
                  />
                </VCol>
          <!-- 👉 Header Title -->
                        <VCol
                          cols="12"
                          md="6"
                          
                        >
                          <VTextField
                            v-model="accountDataLocal.header_title"
                            label="Header Title"
                            placeholder="Header Title"
                            :rules="[requiredValidator]"
                          
                          />
                        </VCol>
                <VCol
                  cols="12"
                  md="6"
                >
                  <VTextField
                    v-model="accountDataLocal.domain_name"
                    label="Domain Name"
                    placeholder="Domain Name"
                    :rules="[requiredValidator]"
                  />
                </VCol>
                <!-- 👉 Email -->
                <VCol
                  cols="12"
                  md="6"
                >
                  <VTextarea
                    v-model="accountDataLocal.footer_text"
                    label="Footer Text"
                    placeholder="Footer Text"
                    :rules="[requiredValidator]"
                  
                  />
                </VCol>

                

                <!-- 👉 Phone -->
                

                <!-- 👉 Address -->
                <VCol
                  cols="12"
                  md="6"
                    style="display: none;"
                >
                  <VTextField
                    v-model="accountDataLocal.address"
                    label="Address"
                    placeholder="123 Main St, New York, NY 10001"
                  />
                </VCol>

                <!-- 👉 State -->
                <VCol
                  cols="12"
                  md="6"
                    style="display: none;"
                >
                  <VTextField
                    v-model="accountDataLocal.state"
                    label="State"
                    placeholder="New York"
                  />
                </VCol>

                <!-- 👉 Zip Code -->
                <VCol
                  cols="12"
                  md="6"
                    style="display: none;"
                >
                  <VTextField
                    v-model="accountDataLocal.zip"
                    label="Zip Code"
                    placeholder="10001"
                  />
                </VCol>

                <!-- 👉 Country -->
                <VCol
                  cols="12"
                  md="6"
                    style="display: none;"
                >
                  <VSelect
                    v-model="accountDataLocal.country"
                    multiple
                    chips
                    closable-chips
                    label="Country"
                    :items="['USA', 'Canada', 'UK', 'India', 'Australia']"
                    placeholder="Select Country"
                  />
                </VCol>

                <!-- 👉 Language -->
                <VCol
                  cols="12"
                  md="6"
                    style="display: none;"
                >
                  <VSelect
                    v-model="accountDataLocal.language"
                    label="Language"
                    multiple
                    chips
                    closable-chips
                    placeholder="Select Language"
                    :items="['English', 'Spanish', 'Arabic', 'Hindi', 'Urdu']"
                  />
                </VCol>

                <!-- 👉 Timezone -->
                <VCol
                  cols="12"
                  md="6"
                    style="display: none;"
                >
                  <VSelect
                    v-model="accountDataLocal.timezone"
                    label="Timezone"
                    placeholder="Select Timezone"
                    :items="timezones"
                    :menu-props="{ maxHeight: 200 }"
                  />
                </VCol>

                <!-- 👉 Currency -->
                <VCol
                  cols="12"
                  md="6"
                    style="display: none;"
                >
                  <VSelect
                    v-model="accountDataLocal.currency"
                    label="Currency"
                    placeholder="Select Currency"
                    :items="currencies"
                    :menu-props="{ maxHeight: 200 }"
                  />
                </VCol>

                <!-- 👉 Form Actions -->
                <VCol
                  cols="12"
                  class="d-flex flex-wrap gap-4"
                >
                  <VBtn @click="save">Save changes</VBtn>

                  
                </VCol>
              </VRow>
            </VForm>
          </VCardText>
        </VCard>
      </VCol>
  
    <VCol cols="12"   style="display: none;">
      <!-- 👉 Delete Account -->
      <VCard title="Delete Account">
        <VCardText>
          <!-- 👉 Checkbox and Button  -->
          <div>
            <VCheckbox
              v-model="isAccountDeactivated"
              :rules="validateAccountDeactivation"
              label="I confirm my account deactivation"
            />
          </div>

          <VBtn
            :disabled="!isAccountDeactivated"
            color="error"
            class="mt-3"
            @click="isConfirmDialogOpen = true"
          >
            Deactivate Account
          </VBtn>
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
