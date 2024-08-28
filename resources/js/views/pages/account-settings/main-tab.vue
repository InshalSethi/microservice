<script setup>
import AccountSettingsAccount from '@/views/pages/account-settings/AccountSettingsAccount.vue'
import AccountSettingsBillingAndPlans from '@/views/pages/account-settings/AccountSettingsBillingAndPlans.vue'
import AccountSettingsConnections from '@/views/pages/account-settings/AccountSettingsConnections.vue'
import AccountSettingsNotification from '@/views/pages/account-settings/AccountSettingsNotification.vue'
import AccountSettingsSecurity from '@/views/pages/account-settings/AccountSettingsSecurity.vue'
import WebsiteSettings from '@/views/pages/account-settings/WebsiteSettings.vue'
const route = useRoute('admin-account-settings-tab')

const activeTab = computed({
  get: () => route.params.tab,
  set: () => route.params.tab,
})

// tabs
const tabs = [
  {
    title: 'Account',
    icon: 'ri-group-line',
    tab: 'account',
    action: 'read',
    subject: "Profile Update",
  },
  {
    title: 'Security',
    icon: 'ri-lock-line',
    tab: 'security',
    action: 'read',
    subject: "Security",
  },
   {
    title: 'Site Setting',
    icon: 'ri-link',
    tab: 'site-settings',
    action: 'read',
    subject: "Site Settings",
  },
 
  
]

definePage({ meta: { navActiveLink: 'admin-account-settings-tab' } })
</script>

<template>
  <div>
    <VTabs
      v-model="activeTab"
      class="v-tabs-pill"
    >
      <VTab
        v-for="item in tabs"
        :key="item.icon"
        :value="item.tab"
        :to="{ name: 'admin-account-settings-tab', params: { tab: item.tab } }"
      >
        <VIcon
          start
          :icon="item.icon"
          v-if="$can(item.action, item.subject)"
        />
        <span v-if="$can(item.action, item.subject)">{{ item.title }}</span>
      </VTab>
    </VTabs>

    <VWindow
      v-model="activeTab"
      class="mt-5 disable-tab-transition"
      :touch="false"
    >
      <!-- Account -->
      <VWindowItem value="account" v-if="$can('read', 'Profile Update')">
        <AccountSettingsAccount />
      </VWindowItem>

      <!-- Security -->
      <VWindowItem value="security" v-if="$can('read', 'Security')">
        <AccountSettingsSecurity />
      </VWindowItem>
      <!-- site setting -->
      <VWindowItem value="site-settings" v-if="$can('read', 'Site Settings')">
        <WebsiteSettings />
      </VWindowItem>
      <!-- Billing -->
      <VWindowItem value="billing-plans">
        <AccountSettingsBillingAndPlans />
      </VWindowItem>

      <!-- Notification -->
      <VWindowItem value="notification">
        <AccountSettingsNotification />
      </VWindowItem>

      <!-- Connections -->
      <VWindowItem value="connection">
        <AccountSettingsConnections />
      </VWindowItem>
    </VWindow>
  </div>
</template>
