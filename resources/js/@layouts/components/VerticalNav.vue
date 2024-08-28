<script setup>
import { useAbility } from '@casl/vue'
import { layoutConfig } from '@layouts'
import {
VerticalNavGroup,
VerticalNavLink,
VerticalNavSectionTitle,
} from '@layouts/components'
import VerticalNavDropdown from '@layouts/components/VerticalNavDropdown.vue'
import { useLayoutConfigStore } from '@layouts/stores/config'
import { injectionKeyIsVerticalNavHovered } from '@layouts/symbols'

import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { useStore } from 'vuex'
import { VNodeRenderer } from './VNodeRenderer'
const store = useStore();
const router = useRouter()
const ability = useAbility()
const props = defineProps({
  tag: {
    type: null,
    required: false,
    default: 'aside',
  },
  navItems: {
    type: null,
    required: true,
  },
  isOverlayNavActive: {
    type: Boolean,
    required: true,
  },
  toggleIsOverlayNavActive: {
    type: Function,
    required: true,
  },
})

const refNav = ref()
const isHovered = useElementHover(refNav)
const permissions = ref()
provide(injectionKeyIsVerticalNavHovered, isHovered)

const configStore = useLayoutConfigStore()

const resolveNavItemComponent = item => {
  if ('heading' in item)
    return VerticalNavSectionTitle
  if ('children' in item && item.isDropdownButton)
    return VerticalNavDropdown
  if ('children' in item)
    return VerticalNavGroup
  return VerticalNavLink
  
}

/*ℹ️ Close overlay side when route is changed
Close overlay vertical nav when link is clicked
*/
const route = useRoute()
   
watch(() => route.name, async () => {
 
 
    await store.dispatch('checkLogin')
    const isLoggedIn = await store.getters.getCheckLoginExpire
    console.log('check login', isLoggedIn)
    permissions.value = store.getters.getPermissionUser

  const userAbilities = transformPermissions(store.getters.getPermissionUser);
   console.log('userAbilityRules cookie', userAbilities);
         localStorage.setItem('userAbilityRules',JSON.stringify(userAbilities))
          ability.update(userAbilities);
        console.log('userAbilityRules cookie', useCookie('userAbilityRules').value);
   
  if (isLoggedIn) {
     await store.dispatch('updateCheckToken',false)
      
      // Redirect to login page or perform any other action
      useCookie('accessToken').value = null
       
      localStorage.removeItem('admin_access_token');
      useCookie('userAbilityRules').value = null
        ability.update([])
      router.push({ name: 'login' })
    }
  
  props.toggleIsOverlayNavActive(false)
})
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

onMounted(async () => {
  await store.dispatch('checkLogin')
    const isLoggedIn = await store.getters.getCheckLoginExpire
    console.log('check login', isLoggedIn)
    permissions.value = store.getters.getPermissionUser
  const userAbilities = transformPermissions(store.getters.getPermissionUser);
   console.log('userAbilityRules cookie', userAbilities);
   localStorage.setItem('userAbilityRules',JSON.stringify(userAbilities))
  ability.update(userAbilities);
  console.log('ability', ability);
  console.log('userAbilityRules cookie', useCookie('userAbilityRules').value);
   
})
const isVerticalNavScrolled = ref(false)
const updateIsVerticalNavScrolled = val => isVerticalNavScrolled.value = val

const handleNavScroll = evt => {
  isVerticalNavScrolled.value = evt.target.scrollTop > 0
}

const hideTitleAndIcon = configStore.isVerticalNavMini(isHovered)
</script>

<template>
  <Component
    :is="props.tag"
    ref="refNav"
    class="layout-vertical-nav"
    :class="[
      {
        'overlay-nav': configStore.isLessThanOverlayNavBreakpoint,
        'hovered': isHovered,
        'visible': isOverlayNavActive,
        'scrolled': isVerticalNavScrolled,
      },
    ]"
  >
    <!-- 👉 Header -->
    <div class="nav-header">
      <slot name="nav-header">
        <RouterLink
          to="/"
          class="app-logo app-title-wrapper"
        >
          <VNodeRenderer :nodes="layoutConfig.app.logo" />

          <Transition name="vertical-nav-app-title">
            <h1
              v-show="!hideTitleAndIcon"
              class="app-logo-title leading-normal"
            >
              {{ layoutConfig.app.title }}
            </h1>
          </Transition>
        </RouterLink>
        <!-- 👉 Vertical nav actions -->
        <!-- Show toggle collapsible in >md and close button in <md -->
        <Component
          :is="layoutConfig.app.iconRenderer || 'div'"
          v-show="configStore.isVerticalNavCollapsed"
          class="header-action d-none nav-unpin"
          :class="configStore.isVerticalNavCollapsed && 'd-lg-block'"
          v-bind="layoutConfig.icons.verticalNavUnPinned"
          @click="configStore.isVerticalNavCollapsed = !configStore.isVerticalNavCollapsed"
        />
        <Component
          :is="layoutConfig.app.iconRenderer || 'div'"
          v-show="!configStore.isVerticalNavCollapsed"
          class="header-action d-none nav-pin"
          :class="!configStore.isVerticalNavCollapsed && 'd-lg-block'"
          v-bind="layoutConfig.icons.verticalNavPinned"
          @click="configStore.isVerticalNavCollapsed = !configStore.isVerticalNavCollapsed"
        />
        <Component
          :is="layoutConfig.app.iconRenderer || 'div'"
          class="header-action d-lg-none"
          v-bind="layoutConfig.icons.close"
          @click="toggleIsOverlayNavActive(false)"
        />
      </slot>
    </div>
    <slot name="before-nav-items">
      <div class="vertical-nav-items-shadow" />
    </slot>
    <slot
      name="nav-items"
      :update-is-vertical-nav-scrolled="updateIsVerticalNavScrolled"
    >
      <PerfectScrollbar
        :key="configStore.isAppRTL"
        tag="ul"
        class="nav-items"
        :options="{ wheelPropagation: false }"
        @ps-scroll-y="handleNavScroll"
      >
        <Component
          :is="resolveNavItemComponent(item)"
          v-for="(item, index) in navItems"
          :key="index"
          :item="item"
        />
      </PerfectScrollbar>
    </slot>

    <slot name="after-nav-items" />
  </Component>
    
</template>

<style lang="scss" scoped>
.app-logo {
  display: flex;
  align-items: center;
  column-gap: 0.75rem;

  .app-logo-title {
    font-size: 1.25rem;
    font-weight: 600;
    line-height: 1.75rem;
    text-transform: uppercase;
  }
}
</style>

<style lang="scss">
@use "@configured-variables" as variables;
@use "@layouts/styles/mixins";

// 👉 Vertical Nav
.layout-vertical-nav {
  position: fixed;
  z-index: variables.$layout-vertical-nav-z-index;
  display: flex;
  flex-direction: column;
  block-size: 100%;
  inline-size: variables.$layout-vertical-nav-width;
  inset-block-start: 0;
  inset-inline-start: 0;
  transition: inline-size 0.25s ease-in-out, box-shadow 0.25s ease-in-out;
  will-change: transform, inline-size;

  .nav-header {
    display: flex;
    align-items: center;

    .header-action {
      cursor: pointer;

      @at-root {
        #{variables.$selector-vertical-nav-mini} .nav-header .header-action {
          &.nav-pin,
          &.nav-unpin {
            display: none !important;
          }
        }
      }
    }
  }

  .app-title-wrapper {
    margin-inline-end: auto;
  }

  .nav-items {
    block-size: 100%;

    // ℹ️ We no loner needs this overflow styles as perfect scrollbar applies it
    // overflow-x: hidden;

    // // ℹ️ We used `overflow-y` instead of `overflow` to mitigate overflow x. Revert back if any issue found.
    // overflow-y: auto;
  }

  .nav-item-title {
    overflow: hidden;
    margin-inline-end: auto;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  // 👉 Collapsed
  .layout-vertical-nav-collapsed & {
    &:not(.hovered) {
      inline-size: variables.$layout-vertical-nav-collapsed-width;
    }
  }
}

// Small screen vertical nav transition
@media (max-width:1279px) {
  .layout-vertical-nav {
    &:not(.visible) {
      transform: translateX(-#{variables.$layout-vertical-nav-width});

      @include mixins.rtl {
        transform: translateX(variables.$layout-vertical-nav-width);
      }
    }

    transition: transform 0.25s ease-in-out;
  }
}
.v-icon {
  margin-right: 8px; /* Adds space between the icon and the text */
}
.list-item-reset a {
    color: rgb(46,38,61) !important;
}
</style>
