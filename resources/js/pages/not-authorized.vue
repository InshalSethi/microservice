<script setup>
import { useAbility } from '@casl/vue'
import miscMaskDark from '@images/misc/misc-mask-dark.png'
import miscMaskLight from '@images/misc/misc-mask-light.png'
import tree1 from '@images/misc/tree1.png'
import tree3 from '@images/misc/tree3.png'
import pages401 from '@images/pages/401.png'
import { useStore } from 'vuex'
const ability = useAbility()
const store = useStore();
const authThemeMask = useGenerateImageVariant(miscMaskLight, miscMaskDark)
const router = useRouter()
definePage({
  alias: '/pages/misc/not-authorized',
  meta: {
    layout: 'blank',
    public: true,
  },
})
const permissions = ref()
onMounted(async () => {
  await store.dispatch('checkLogin')
    const isLoggedIn = await store.getters.getCheckLoginExpire
    console.log('check login', isLoggedIn)
    permissions.value = store.getters.getPermissionUser

  const userAbilities = transformPermissions(store.getters.getPermissionUser);
  localStorage.setItem('userAbilityRules',JSON.stringify(userAbilities))
  ability.update(userAbilities);

console.log('ability', ability);
console.log('userAbilityRules cookie', useCookie('userAbilityRules').value);
   
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
</script>

<template>
  <div class="misc-wrapper">
    <ErrorHeader
      status-code="401"
      title="You are not authorized! 🔐"
      description="You don't have permission to access this page. Go Dashboard!"
      class="mb-10"
    />

    <!-- 👉 Image -->
    <div class="misc-avatar w-100 text-center">
      <VImg
        :src="pages401"
        alt="Coming Soon"
        :max-width="785"
        :height="500"
        class="mx-auto"
      />
      <VBtn
        to="/admin/dashboard"
        class="mt-10"
        style="z-index: 1;"
      >
        Back to Dashboard
      </VBtn>

      <div class="d-md-flex gap-x-2 misc-footer-tree d-none">
        <img
          :src="tree3"
          alt="tree"
          height="120"
          width="68"
        >
        <img
          :src="tree3"
          alt="tree"
          height="70"
          width="40"
          class="align-self-end"
        >
      </div>

      <img
        height="210"
        :src="tree1"
        class="misc-footer-tree-1 d-none d-md-block"
      >
      <img
        cover
        :src="authThemeMask"
        height="172"
        class="misc-footer-img d-none d-md-block flip-in-rtl"
      >
    </div>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/misc.scss";
</style>
