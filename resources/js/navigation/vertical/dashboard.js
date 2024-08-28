
export default [
  {
    title: 'Dashboard',
    icon: { icon: 'ri-home-smile-line' },
    to: 'admin-dashboard',
    action: 'read',
    subject: "Dashboard Data",
    
  },
  
  
  
  {
    title: 'Products',
    icon: { icon: 'ri-medicine-bottle-line' },
    to: 'admin-products',
    action: 'read',
    subject: "Product View",
  },


  
 {
    title: 'Settings',
    icon: { icon: 'ri-settings-4-line' },
    //class: 'bottom-end',
    //isDropdownButton: true,
    children: [
      {
        title: 'Users',
        to: 'admin-users',
        icon: {
        icon: 'ri-user-line',
        },
         action: 'read',
        subject: "Admin View",
      },
      {
        title: 'Roles',
        to: 'admin-roles',
     
        icon: {
        icon: 'ri-shield-user-line',
        },
         action: 'read',
        subject: "Role View",
      },
    ]  
    
}, 

]


