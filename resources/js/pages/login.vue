<script setup>
import tree1 from '@images/misc/tree1.png';
import authV2LoginIllustrationBorderedDark from '@images/pages/auth-v2-login-illustration-bordered-dark.png';
import authV2LoginIllustrationBorderedLight from '@images/pages/auth-v2-login-illustration-bordered-light.png';
import authV2LoginIllustrationDark from '@images/pages/auth-v2-login-illustration-dark.png';
import authV2LoginIllustrationLight from '@images/pages/auth-v2-login-illustration-light.png';
import authV2MaskDark from '@images/pages/mask-v2-dark.png';
import authV2MaskLight from '@images/pages/mask-v2-light.png';
import { VNodeRenderer } from '@layouts/components/VNodeRenderer';
import { themeConfig } from '@themeConfig';
import axios from 'axios';
import { VForm } from 'vuetify/components/VForm';
import { useStore } from 'vuex';
import { ADMIN_LOGIN_API } from '../constants';
const store = useStore()

const authThemeImg = useGenerateImageVariant(authV2LoginIllustrationLight, authV2LoginIllustrationDark, authV2LoginIllustrationBorderedLight, authV2LoginIllustrationBorderedDark, true)
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)

definePage({
  meta: {
    layout: 'blank',
    unauthenticatedOnly: true,
  },
})

const isPasswordVisible = ref(false)
const route = useRoute()
const router = useRouter()
const ability = useAbility()

const errors = ref({
  email: undefined,
  password: undefined,
})

const refVForm = ref()

const credentials = ref({
  email: '',
  password: '',
})

const isLoading = ref(false)
const rememberMe = ref(false)

const login = async () => {
  
  try {
    store.dispatch('updateIsLoading', true)
    // isLoading.value = true
    const response = await axios.post(ADMIN_LOGIN_API, {
      email: credentials.value.email,
      password: credentials.value.password,
    });

    console.log("Response", response.data);
    response.data.userData.avatar = "/images/avatars/avatar-1.png";
    console.log("Response 2", response.data);

    const { accessToken, userData, userAbilityRules,permissions } = response.data;
    const userAbilities = transformPermissions(permissions);
    console.log('userAbilities login',userAbilities)
     
     localStorage.setItem('userAbilityRules',JSON.stringify(userAbilities))
    //useCookie('userAbilityRules').value = userAbilityRules;
    ability.update(userAbilities);
    useCookie('userData').value = userData;
    useCookie('accessToken').value = accessToken;
    localStorage.setItem('admin_access_token',accessToken)
    
    await nextTick(() => {
      router.replace(route.query.to ? String(route.query.to) : '/');
    });
    store.dispatch('updateIsLoading', false)
  } catch (error) {
    store.dispatch('updateIsLoading', false)
    errors.value.email = error.response.data.error
    console.error(error);
  }
}
const transformPermissions = (permissionsData) => {
  const transformedPermissions = [];

  const processPermissions = (permissions) => {
    for (const permission of permissions) {
      if (permission.ability === true) {
        transformedPermissions.push({
          action: 'read', // Adjust based on your permission model
          subject: permission.text,
        });
      }

      if (permission.children) {
        for (const child of permission.children) {
          if (child.ability === true) {
            transformedPermissions.push({
              action: 'read', // Adjust based on your permission model
              subject: child.text,
            });
          }
        }
      }
    }
  };

  for (const group of permissionsData) {
    processPermissions(group.permissions);
  }

  return transformedPermissions;
};
const onSubmit = () => {
  refVForm.value?.validate().then(({ valid: isValid }) => {
    if (isValid)
      login()
  })
}
</script>

<template>
  <VDialog v-model="isLoading" width="110" height="150" color="primary">
        <VCardText class="" style="color: white !important;">
          <div class="demo-space-x">
            <VProgressCircular :size="40" color="primary" indeterminate />
          </div>
        </VCardText>
      </VDialog>
  <RouterLink to="/">
    <div class="auth-logo d-flex align-center gap-x-3">
      <VNodeRenderer :nodes="themeConfig.app.logo" />
      <h1 class="auth-title">
        {{ themeConfig.app.title }}
      </h1>
    </div>
  </RouterLink>

  <VRow
    no-gutters
    class="auth-wrapper"
  >
    <VCol
      md="8"
      class="d-none d-md-flex position-relative"
    >
      <div class="d-flex align-center justify-end w-100 h-100 pa-10 pe-0">
        <VImg
          max-width="797"
          :src="authThemeImg"
          class="auth-illustration"
        />
      </div>

      <img
        class="auth-footer-mask"
        height="360"
        :src="authThemeMask"
      >

      <VImg
        :src="tree1"
        alt="tree image"
        height="190"
        width="90"
        class="auth-footer-tree"
      />
    </VCol>

    <VCol
      cols="12"
      md="4"
      class="auth-card-v2 d-flex align-center justify-center"
      style="background-color: rgb(var(--v-theme-surface));"
    >
      <VCard
        flat
        :max-width="500"
        class="mt-12 mt-sm-0 pa-4"
      >
        <VCardText>
          <h4 class="text-h4 mb-1">
            Welcome to <span class="text-capitalize">{{ themeConfig.app.title }}!</span> 
          </h4>
          <p class="mb-0">
            Please sign-in to your account and start the adventure
          </p>
        </VCardText>
        <!-- <VCardText>
          <VAlert
            color="primary"
            variant="tonal"
          >
            <p class="text-caption mb-2 text-primary">
              Admin Email: <strong>admin@demo.com</strong> / Pass: <strong>admin</strong>
            </p>
            <p class="text-caption mb-0 text-primary">
              Client Email: <strong>client@demo.com</strong> / Pass: <strong>client</strong>
            </p>
          </VAlert>
        </VCardText> -->

        <VCardText>
          <VForm
            ref="refVForm"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <VTextField
                  v-model="credentials.email"
                  label="Email"
                  placeholder="johndoe@email.com"
                  type="email"
                  autofocus
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="errors.email"
                />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <VTextField
                  v-model="credentials.password"
                  label="Password"
                  placeholder="············"
                  :rules="[requiredValidator]"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :error-messages="errors.password"
                  :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />

                <div class="d-flex align-center flex-wrap justify-space-between my-5 gap-2">
                  <VCheckbox
                    v-model="rememberMe"
                    label="Remember me"
                  />
                  <RouterLink
                    class="text-primary"
                    :to="{ name: 'forgot-password' }"
                  >
                    Forgot Password?
                  </RouterLink>
                </div>

                <VBtn
                  block
                  type="submit"
                  style="width: 100%;"
                >
                  Login
                </VBtn>
              </VCol>

              <!-- create account -->
              <VCol
                cols="12"
                class="text-center text-base"
              >
                <span>New on our platform?</span> <RouterLink
                  class="text-primary d-inline-block"
                  :to="{ name: 'register' }"
                >
                  Create an account
                </RouterLink>
              </VCol>
              <!-- <VCol
                cols="12"
                class="d-flex align-center"
              >
                <VDivider />
                <span class="mx-4">or</span>
                <VDivider />
              </VCol> -->

              <!-- auth providers -->
              <!-- <VCol
                cols="12"
                class="text-center"
              >
                <AuthProvider />
              </VCol> -->
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>
