<script setup>
import AuthProvider from '@/views/pages/authentication/AuthProvider.vue'
import authV2RegisterIllustrationDark from '@images/pages/auth-v2-register-illustration-dark.png'
import authV2RegisterIllustrationLight from '@images/pages/auth-v2-register-illustration-light.png'
import { themeConfig } from '@themeConfig'
import tree2 from '@images/misc/tree2.png'
import authV2RegisterIllustrationBorderedDark from '@images/pages/auth-v2-register-illustration-bordered-dark.png'
import authV2RegisterIllustrationBorderedLight from '@images/pages/auth-v2-register-illustration-bordered-light.png'
import authV2MaskDark from '@images/pages/mask-v2-dark.png'
import authV2MaskLight from '@images/pages/mask-v2-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import axios from 'axios';
import {
    emailValidator,
    requiredEmail,
    requiredPassword
} from '@validators';
import { onMounted, ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ADMIN_REGISTER_API } from '../constants';
import { useStore } from 'vuex';
const store = useStore()
const route = useRoute()
const router = useRouter()
const ability = useAbility()
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)
const authThemeImg = useGenerateImageVariant(authV2RegisterIllustrationLight, authV2RegisterIllustrationDark, authV2RegisterIllustrationBorderedLight, authV2RegisterIllustrationBorderedDark, true)
const errors = ref({
    password: undefined,
    email: undefined,
})
const errorMessage = ref(null)
definePage({
  meta: {
    layout: 'blank',
    unauthenticatedOnly: true,
  },
})

const form = ref({
  name: '',
  email: '',
  password: '',
  confirm_password: '',
  privacyPolicies: false,
})

const isPasswordVisible = ref(false)

// Custom validation rule for matching passwords
const matchPasswords = (value) => {
  return value === form.value.password || 'Passwords do not match';
};

const register = async () => {
  try {
    store.dispatch('updateIsLoading', true)
    const response = await axios.post(ADMIN_REGISTER_API, {
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.confirm_password,
    });

    console.log("Response", response.data);
    response.data.userData.avatar = "/images/avatars/avatar-1.png";
    console.log("Response 2", response.data);

    const { accessToken, userData, userAbilityRules, permissions } = response.data;
    const userAbilities = transformPermissions(permissions);
    console.log('userAbilities login', userAbilities)
     
    localStorage.setItem('userAbilityRules', JSON.stringify(userAbilities))
    ability.update(userAbilities);
    useCookie('userData').value = userData;
    useCookie('accessToken').value = accessToken;
    localStorage.setItem('admin_access_token', accessToken)
    
    await nextTick(() => {
      router.replace(route.query.to ? String(route.query.to) : '/');
    });
    store.dispatch('updateIsLoading', false)
  } catch (error) {
    store.dispatch('updateIsLoading', false)
    if (error.response && error.response.status === 422) {
      // Handle 422 error (Unprocessable Entity)
      errorMessage.value = error.response.data.error || 'Email is already taken.';
      errors.value.email = error.response.data.error || 'Email is already taken.'
    } else {
      // Handle other errors
      errorMessage.value = 'An error occurred. Please try again later.';
      errors.value.email = 'An error occurred. Please try again later.'
    }
    console.error(error);
  }
};
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
</script>

<template>
  <RouterLink to="/">
    <div class="auth-logo d-flex align-center gap-x-3">
      <VNodeRenderer :nodes="themeConfig.app.logo" />
      <h1 class="auth-title">
        {{ themeConfig.app.title }}
      </h1>
    </div>
  </RouterLink>

  <VRow no-gutters class="auth-wrapper">
    <VCol md="8" class="d-none d-md-flex position-relative">
      <div class="d-flex align-center justify-end w-100 h-100 pa-10 pe-0">
        <VImg max-width="797" :src="authThemeImg" class="auth-illustration" />
      </div>

      <img class="auth-footer-mask" height="360" :src="authThemeMask" />

      <div class="d-flex gap-x-2 auth-footer-tree">
        <img :src="tree2" alt="tree image" height="180" />
        <img :src="tree2" alt="tree image" height="120" class="align-self-end" />
      </div>
    </VCol>

    <VCol cols="12" md="4" class="auth-card-v2 d-flex align-center justify-center" style="background-color: rgb(var(--v-theme-surface));">
      <VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-4">
        <VCardText>
          <h4 class="text-h4 mb-1">Welcome!</h4>
          <p class="mb-0">Want to create an account? No problem, just fill the form.</p>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent="register">
            <VRow>
              <!-- Username -->
              <VCol cols="12">
                <VTextField
                  v-model="form.name"
                  autofocus
                  label="Name"
                  placeholder="Johndoe"
                  :rules="[requiredPassword]"
                />
              </VCol>

              <!-- email -->
              <VCol cols="12">
                <VTextField
                  v-model="form.email"
                  label="Email"
                  type="email"
                  placeholder="johndoe@email.com"
                  :rules="[requiredEmail, emailValidator]" 
                  :error-messages="errors.email"
                />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <VTextField
                  v-model="form.password"
                  label="Password"
                  placeholder="············"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                  :rules="[requiredPassword]"
                />
              </VCol>

              <!-- confirm password -->
              <VCol cols="12">
                <VTextField
                  v-model="form.confirm_password"
                  label="Confirm Password"
                  placeholder="············"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                  :rules="[requiredPassword, matchPasswords]"
                />
              </VCol>

              <!-- privacy policy -->
              <VCol cols="12">
                <div class="d-flex align-center my-5">
                  <VCheckbox id="privacy-policy" v-model="form.privacyPolicies" inline />
                  <VLabel for="privacy-policy" style="opacity: 1;">
                    <span class="me-1 text-high-emphasis">I agree to</span>
                    <a href="javascript:void(0)" class="text-primary">privacy policy & terms</a>
                  </VLabel>
                </div>

                <VBtn block type="submit">Sign up</VBtn>
              </VCol>

              <!-- create account -->
              <VCol cols="12">
                <div class="text-center text-base">
                  <span class="d-inline-block">Already have an account?</span>
                  <RouterLink class="text-primary d-inline-block" :to="{ name: 'login' }">Sign in instead</RouterLink>
                </div>
              </VCol>

              <VCol cols="12">
                <div class="d-flex align-center">
                  <VDivider />
                  <span class="mx-4 text-high-emphasis">or</span>
                  <VDivider />
                </div>
              </VCol>

              <!-- auth providers -->
              <VCol cols="12" class="text-center">
                <AuthProvider />
              </VCol>
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
