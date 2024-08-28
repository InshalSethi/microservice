<script setup>
import ScrollToTop from '@core/components/ScrollToTop.vue'
import initCore from '@core/initCore'
import {
initConfigStore,
useConfigStore,
} from '@core/stores/config'
import { hexToRgb } from '@layouts/utils'
import { useTheme } from 'vuetify'
import { useStore } from 'vuex'
const store = useStore()
const { global } = useTheme()

// ℹ️ Sync current theme with initial loader theme
initCore()
initConfigStore()

const configStore = useConfigStore()
</script>

<template>
  <VOverlay
          v-model="store.getters.getIsLoading"
          contained
          persistent
          scroll-strategy="none"
          class="align-center justify-center"
        >
          <VProgressCircular indeterminate />
        </VOverlay>
        <VSnackbar v-model="store.getters.getSuccessMsg" :timeout="5000" location="top end" variant="flat"
              color="success">
              <VIcon
                    class="ri-checkbox-circle-line"
                  /> {{ store.getters.getShowMsg }}
            </VSnackbar>
            <VSnackbar v-model="store.getters.getErrorMsg" :timeout="5000" location="top end" variant="flat"
              color="error">
              <VIcon
                    class="ri-spam-2-line"
                  /> {{ store.getters.getShowMsg }}
            </VSnackbar>
  <VLocaleProvider :rtl="configStore.isAppRTL">
    <!-- ℹ️ This is required to set the background color of active nav link based on currently active global theme's primary -->
    <VApp :style="`--v-global-theme-primary: ${hexToRgb(global.current.value.colors.primary)}`">
      <RouterView />
      <ScrollToTop />
    </VApp>
  </VLocaleProvider>
</template>
