const emailRouteComponent = () => import('@/pages/apps/email/index.vue')

// 👉 Redirects
export const redirects = [
  // ℹ️ We are redirecting to different pages based on role.
  // NOTE: Role is just for UI purposes. ACL is based on abilities.
  {
    path: '/admin',
    // name: 'index',
    redirect: to => {
      // TODO: Get type from backend
      const userData = useCookie('userData')
      const userRole = userData.value?.role
      console.log(userRole)
      if (userRole)
        return { name: 'admin-dashboard' }
      if (userRole === 'admin')
        return { name: 'admin-dashboard' }
      if (userRole === 'client')
        return { name: 'access-control' }
      
      return { name: 'login', query: to.query }
    },
  },
  {
    path: '/',
    // name: 'index',
    redirect: to => {
      // TODO: Get type from backend
      const userData = useCookie('userData')
      const userRole = userData.value?.role
       if (userRole)
        return { name: 'admin-dashboard' }
      if (userRole === 'admin')
        return { name: 'admin-dashboard' }
      if (userRole === 'user')
        return { name: 'admin-dashboard' }
      if (userRole === 'client')
        return { name: 'access-control' }
      
      return { name: 'login', query: to.query }
    },
  },
  // {
  //   path: '/pages/user-profile',
  //   name: 'pages-user-profile',
  //   redirect: () => ({ name: 'pages-user-profile-tab', params: { tab: 'profile' } }),
  // },
  // {
  //   path: '/pages/account-settings',
  //   name: 'pages-account-settings',
  //   redirect: () => ({ name: 'pages-account-settings-tab', params: { tab: 'account' } }),
  //    meta: {action: 'read', subject: 'Profile Update'},
  // },
  
]
export const routes = [
  // Email filter
  {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: () => import('@/pages/dashboards/analytics.vue'),
    meta: {action: 'read', subject: 'Dashboard Data'},
  },
  {
    path: '/admin/products',
    name: 'admin-products',
    component: () => import('@/pages/products/product.vue'),
    meta: {action: 'read', subject: 'Product View'},
  },
  //  {
  //   path: '/admin/profile',
  //   name: 'admin-profile',
  //    component: () => import('@/views/pages/account-settings/AccountSettingsAccount.vue'),
  //    meta: {action: 'read', subject: 'Profile Update'},
  // },
  
  //  {
  //   path: '/admin/site-setting',
  //   name: 'admin-site-setting',
  //    component: () => import('@/views/pages/account-settings/WebsiteSettings.vue'),
  //   meta: {action: 'read', subject: 'Site Settings'},
  // },
  
  {
    path: '/admin/account-settings',
    name: 'admin-account-settings',
     //redirect: () => ({ name: 'admin-account-settings', params: { tab: 'account' } }),
     component: () => import('@/views/pages/account-settings/main-tab.vue'),
      props: route => ({ tab: route.params.tab || 'account' }), 
       meta: {action: 'read', subject: 'Profile Update'},
  },
  
  
 
  
  {
    path: '/admin/users',
    name: 'admin-users',
    component: () => import('@/pages/admin-users/admin-list.vue'),
    meta: {action: 'read', subject: 'Admin View'},
  },
    {
    path: '/admin/role-permission/:id',
    name: 'admin-role-permission',
      component: () => import('@/pages/user-permission/assign-permission.vue'),
    meta: {action: 'read', subject: 'Role Permissions'},
  },
   
 
 
  
  {
    path: '/admin/roles/roles',
    name: 'admin-roles',
    component: () => import('@/pages/roles/roles.vue'),
      meta: {action: 'read', subject: 'Role View'},
  },
  
 
   

  
  
  
  
  
]
