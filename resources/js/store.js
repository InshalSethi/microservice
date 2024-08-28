import axios from 'axios';
import { createStore } from 'vuex';
import {
  ADMIN_ADMIN_SAVE_API,
  ADMIN_DELETE_ROLES_BY_ID_API,
  ADMIN_GET_PERMISSION_ROLE_API,
  ADMIN_GET_PRODUCTS_LIST_API,
  ADMIN_GET_ROLES_API,
  ADMIN_GET_ROLES_BY_ID_API,
  ADMIN_GET_ROLES_SAVE_API,
  ADMIN_GET_SITE_SETTING,
  ADMIN_LOGIN_DETAIL,
  ADMIN_ROLES_LIST_API,
  ADMIN_SINGLE_API,
  ADMIN_UPDATE_API,
  ADMIN_UPDATE_PASSWORD,
  ADMIN_UPDATE_PERMISSION_API,
  ADMIN_UPDATE_ROLES_BY_ID_API,
  ADMIN_UPDATE_SITE_SETTING,
  ADMIN_USER_LIST_API,
  PRODUCT_DELETE_API,
  PRODUCT_LIST_API,
  PRODUCT_SAVE_API,
  PRODUCT_SINGLE_PRODUCT_API,
  PRODUCT_UPDATE_API,
  PROFILE_UPDATE_API,
} from './constants.js';


export default createStore({
  state: {
    isLoading: false,
    isTonalSnackbarVisible: false,
    isErrorMsg: false,
    isSuccessMsg: false,
    singleProduct: null,
    permissionUser:null,
    singleUser: null,
    adminUserList: null,
    permissionsRole:null,
    medicineList: [],
    adminDetail: null,
    siteSetting: null,
    showMessage: null,
    timeout: null,
    checkLoginExpire: false,
    productsList:[],
    permissions: {},
    singleRole: null,
    rolesList: null,
    
  },
  mutations: {
    setLoading(state, payload) {
      console.log('payload');
      state.isLoading = payload
    },
    setIsTonalSnackbarVisible(state, payload) {
      state.isTonalSnackbarVisible = payload;
    },
    setCheckLoginExpire(state, payload) {
      console.log('payload');
        state.checkLoginExpire = payload
    },
     setPermissionUser(state, payload) {
      console.log('payload');
        state.permissionUser = payload
    },
    setErrorMsg(state, payload) {
      console.log('payload');
       clearTimeout(state.timeout)
      state.isErrorMsg = payload
       state.timeout = setTimeout(() => {
      state.isErrorMsg = false
    }, 5000)
    },
    setSuccessMsg(state, payload) {
      console.log('payload');
        clearTimeout(state.timeout)
      state.isSuccessMsg = payload
       state.timeout = setTimeout(() => {
      state.isSuccessMsg = false
    }, 5000)
    },
     setShowMsg(state, payload) {
      console.log('payload');
       state.showMessage = payload
       
    },
     setMedicineList(state, payload) {
      state.medicineList = payload
    },
     setAdminUserList(state, payload) {
      state.adminUserList = payload
    },
    setAdminDetail(state, payload) {
      state.adminDetail = payload
    },
    setSiteSetting(state, payload) {
      state.siteSetting = payload
    },
    setSingleProduct(state, payload) {
            state.singleProduct = payload;
    },
    setSingleUser(state, payload) {
            state.singleUser = payload;
    },
    
    setDashboardData(state, payload) {
            state.dashboardData = payload;
    },
    setProductsList(state, payload) {
            state.productsList = payload;
    },
    setPermissions(state, payload) {
            state.permissions = payload;
    },
    setSingleRole(state, payload) {
            state.singleRole = payload;
    },
    setPermissionsRole(state, payload) {
            state.permissionsRole = payload;
    },
    setRolesList(state, payload) {
            state.rolesList = payload;
    },
    
  },
  actions: {
    async updateErrorMessage({ commit }, payload) {

            commit('setErrorMsg', true)
    
              commit('setShowMsg', payload)
    },
    
    async SaveCurrentDate({ commit }, payload) {
        commit('setCurrentDate', payload)
    },
    async updateIsLoading({ commit }, payload) {
        commit('setLoading', payload)
    },
    async updateIsTonalSnackbar({ commit }, payload) {
      commit("setIsTonalSnackbarVisible", payload);
    },
    async updateCheckToken({ commit }, payload) {
        commit('setCheckLoginExpire', payload)
    },
     async productGetByID({ commit }, payload) {
      commit('setLoading', true)
      await axios.post(PRODUCT_SINGLE_PRODUCT_API+'/'+payload.id, {}, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
        console.log('Response:', response.data);
         commit('setSingleProduct', response.data.data)
  
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
     async productsList({ commit }, payload) {
      commit('setLoading', true)
      console.log(localStorage.getItem('admin_access_token'))
      await axios.post(PRODUCT_LIST_API, {
        start: payload.startL,
        length:payload.endL,
        // search:payload.search,
      }, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
              commit('setLoading', false)
            console.log('Response:', response.data);
            commit('setMedicineList',response.data)
  
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
    async productUpdate({ commit }, payload) {
      commit('setLoading', true)
      await axios.put(PRODUCT_UPDATE_API+'/'+payload.id, {
        name: payload.name,
        slug: payload.slug,
        quantity: payload.quantity,
        description: payload.description,
        price: payload.price,
        currency: payload.currency,
      }, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data);
            if (response.data.message=='success' && response.status === 200) {
              commit('setSuccessMsg', true)
              console.log('Response:', response.data.message);
              commit('setShowMsg', 'Successfully Updated')
            }
         
          })
          .catch(error => {
            let errorString = null
            commit('setLoading', false)
            if ( error.response.data.status === 500) {
              errorString =  'Somthing wrong!'
            }else if(error.response.data.errors){
              let errors = error.response.data.errors
              let errorMessages = [];

              // Loop through the object properties
              for (const key in errors) {
                  if (errors.hasOwnProperty(key)) {
                      errorMessages = errorMessages.concat(errors[key]);
                  }
              }

              errorString = errorMessages.join(' ');
            }else{
              errorString = 'Somthing wrong!'
            }
            commit('setErrorMsg', true)
            commit('setShowMsg', errorString)
          });
    },
    async productDelete({ commit }, payload) {
      commit('setLoading', true);
      console.log(localStorage.getItem('admin_access_token'));
   
      await axios.delete(`${PRODUCT_DELETE_API}/${payload.id}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })
      .then(response => {
        commit('setLoading', false);
        console.log('Response:', response.data);
        if (response.data.message && response.status === 200) {
          commit('setSuccessMsg', true);
          console.log('Response:', response.data.message);
          commit('setShowMsg', 'Successfully Deleted');
        }
      })
      .catch(error => {
        commit('setLoading', false);
        console.error('Error:', error);
      });
    },
    async productAdd({ commit }, payload) {
      commit('setLoading', true)
      await axios.post(PRODUCT_SAVE_API, {
        name: payload.name,
        price: payload.price,
        quantity: payload.quantity,
        description: payload.description,
        currency: payload.currency,
      }, {
        headers: {
          'Content-Type': 'multipart/form-data',
          'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data);
            if (response.data.message=='success' && response.status === 200) {
              commit('setSuccessMsg', true)
              console.log('Response:', response.data.message);
              commit('setShowMsg', 'Successfully Added')
            }
          })
          .catch(error => {
            let errorString = null
            commit('setLoading', false)
            if ( error.response.data.status === 500) {
              errorString =  'Somthing wrong!'
            }else if(error.response.data.errors){
              let errors = error.response.data.errors
              let errorMessages = [];

              // Loop through the object properties
              for (const key in errors) {
                  if (errors.hasOwnProperty(key)) {
                      errorMessages = errorMessages.concat(errors[key]);
                  }
              }

              errorString = errorMessages.join(' ');
            }else{
              errorString = 'Somthing wrong!'
            }
            commit('setErrorMsg', true)
            commit('setShowMsg', errorString)
            console.error('Error:', error);
          });
    },
    async adminDetial({ commit }, payload) {
      commit('setLoading', true)
     
      await axios.post(ADMIN_LOGIN_DETAIL, {}, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
        console.log('Response:', response.data);
           commit('setAdminDetail',response.data.admin_details)
  
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
    async siteSetting({ commit }, payload) {
      commit('setLoading', true)
     
      await axios.post(ADMIN_GET_SITE_SETTING, {}, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
        console.log('Response:', response.data.settings_data);
           commit('setSiteSetting',response.data.settings_data)
  
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
     async siteSettingUpdate({ commit }, payload) {
      commit('setLoading', true)
     
      await axios.post(ADMIN_UPDATE_SITE_SETTING+payload.id, {
              plan_main_title: payload.plan_main_title,
              plan_description:  payload.plan_description,
              header_title: payload.header_title,
              footer_text: payload.footer_text,
              domain_name: payload.domain_name,
              logo:payload.logo,
              favicon:payload.favicon//imageBase64.value
      }, {
        headers: {
          'Content-Type': 'multipart/form-data',
          'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data);
            if (response.data.msg && response.status === 200) {
              commit('setSuccessMsg', true)
              console.log('Response:', response.data.message);
              commit('setShowMsg', response.data.msg)
            }
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
    async adminPasswordUpadate({ commit }, payload) {
      commit('setLoading', true)
     
      await axios.post(ADMIN_UPDATE_PASSWORD, {
              password: payload.password,
              new_password :  payload.new_password ,
              confirm_password :  payload.confirm_password ,
      }, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data);
      if (response.data.status == 'error') {
          commit('setErrorMsg', true)
           console.log('Response:', response.data.msg);
           commit('setShowMsg', response.data.msg)
         
          }
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
    async profileUpdate({ commit }, payload) {
      commit('setLoading', true)
      await axios.post(PROFILE_UPDATE_API, {
        first_name: payload.name,
        last_name:payload.last_name,
        phone_no: payload.phone_no,
        image:payload.image
      }, {
        headers: {
           'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data);
  
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
    async checkLogin({ commit }, payload) {
      //commit('setLoading', true)
     
      await axios.post(ADMIN_LOGIN_DETAIL, {}, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
           commit('setLoading', false)
        console.log('Response:', response.data);
      
          commit('setPermissionUser', response.data.permissions)
          })
          .catch(error => {
           //commit('setLoading', false)
            console.error('Error:', error);
            if (error.response && error.response.status === 401 && error.response.data.error === 'Token has expired') {
                 commit('setCheckLoginExpire', true) 
            }
          });
    },
     async admiUserList({ commit }, payload) {
      commit('setLoading', true)
      console.log(localStorage.getItem('admin_access_token'))
      await axios.post(ADMIN_USER_LIST_API, {
        start: payload.startL,
        length:payload.endL,
        // search:payload.search,
      }, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
              commit('setLoading', false)
            console.log('Response:', response.data);
         
            commit('setAdminUserList',response.data)
  
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
      async adminUserSave({ commit }, payload) {
      commit('setLoading', true)
      await axios.post(ADMIN_ADMIN_SAVE_API, {
        name: payload.name,
        email: payload.email,
        password:payload.password,
        role_id: payload.role_id,
        profile_pic:payload.profile_pic
      }, {
        headers: {
           'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data);
        if (response.data.success) {
                commit('setSuccessMsg', true)
                console.log('Response:','successfully saved!');
                commit('setShowMsg', 'successfully saved!')
              
                }
          })
          .catch(error => {
            commit('setLoading', false)
            commit('setErrorMsg', true)
            const isDuplicateEntryError = /1062 Duplicate entry/.test(error.response.data.message);
            if (isDuplicateEntryError) {
                
             
              commit('setShowMsg', 'Email already taken!')
            } else {
                     commit('setShowMsg', error.response.data.message)
            }
            
            console.error('Error:', error);
          });
    },
    async adminUpdateUser({ commit }, payload) {
      commit('setLoading', true)
      await axios.post(ADMIN_UPDATE_API+'/'+payload.id, {
        name: payload.name,
        email: payload.email,
        password:payload.password,
        role_id: payload.role_id,
        profile_pic:payload.profile_pic
      }, {
        headers: {
           'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data);
        if (response.data.success) {
                commit('setSuccessMsg', true)
                console.log('Response:','successfully saved!');
                commit('setShowMsg', 'successfully saved!')
              
                }
          })
          .catch(error => {
            commit('setLoading', false)
            commit('setErrorMsg', true)
            const isDuplicateEntryError = /1062 Duplicate entry/.test(error.response.data.message);
            if (isDuplicateEntryError) {
                
             
              commit('setShowMsg', 'Email already taken!')
            } else {
                     commit('setShowMsg', error.response.data.message)
            }
            
            console.error('Error:', error);
          });
    },
      async singleUserEdit({ commit }, payload) {
      commit('setLoading', true)
      await axios.post(ADMIN_SINGLE_API+'/'+payload.id, {}, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
        console.log('Response:', response.data);
         commit('setSingleUser', response.data.data)
  
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
    async getAllProductsList ({commit,state},payload){
      commit('setLoading', true)
      await axios.post(ADMIN_GET_PRODUCTS_LIST_API, {}, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response Products:', response.data);
            commit('setProductsList',response.data.data);
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
    async getAdminRoles({ commit }, payload) {
        commit("setLoading", true);

        await axios.post(
                ADMIN_GET_ROLES_API,
                {},
                {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "admin_access_token"
                        )}`,
                    },
                }
            )
            .then((response) => {
                commit("setLoading", false);
                console.log("Response Permissions:", response.data);
                commit("setPermissions", response.data.data);
                //SET_NOTIFICATION_COUNT

            })
            .catch((error) => {
                commit("setLoading", false);
                console.error("Error:", error);
            });
    },
    async saveRole({ commit }, payload) {
      commit('setLoading', true)
      await axios.post(ADMIN_GET_ROLES_SAVE_API, {
        role_name: payload.name,
        role_guard: payload.guard,
      }, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data);
            commit('setSuccessMsg', true)
            commit('setShowMsg', 'Successfully Added')
            
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
    async getRoleByID({ commit }, payload) {
      commit('setLoading', true)
      await axios.post(ADMIN_GET_ROLES_BY_ID_API+'/'+payload.id, {}, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data);
            commit('setSingleRole', response.data.data)
  
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
    async updateRole({ commit }, payload) {
      commit('setLoading', true)
      await axios.post(ADMIN_UPDATE_ROLES_BY_ID_API+'/'+payload.id, {
        role_name: payload.name,
        role_guard: payload.guard,
      }, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data);
              commit('setSuccessMsg', true)
              commit('setShowMsg', 'Successfully Updated')
            
         
          })
          .catch(error => {
            commit('setLoading', false)
            if ( error.response.data.status === 500) {
               commit('setErrorMsg', true)
  
              commit('setShowMsg', 'somthing wrong!')
            }
            console.error('Error:', error);
          });
    },
    async deleteRole({ commit }, payload) {
      commit('setLoading', true)
      console.log(localStorage.getItem('admin_access_token'))
      await axios.post(ADMIN_DELETE_ROLES_BY_ID_API+'/'+payload.id, {}, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data);
            commit('setSuccessMsg', true)
            commit('setShowMsg', 'Successfully Deleted')
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
    async permissionsRole({ commit }, payload) {
      commit('setLoading', true)
      console.log(localStorage.getItem('admin_access_token'))
      await axios.post(ADMIN_GET_PERMISSION_ROLE_API+'/'+payload.id, {}, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data.data);
      
            commit('setPermissionsRole', response.data.data)
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
      async permissionsUpdate({ commit }, payload) {
      commit('setLoading', true)
      console.log(localStorage.getItem('admin_access_token'))
      await axios.post(ADMIN_UPDATE_PERMISSION_API+'/'+payload.id, {permisssions:payload.permisssions}, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response:', response.data.success);
              if (response.data.success) {
                    commit("setIsTonalSnackbarVisible", true);
                    commit('setSuccessMsg', true)
                    commit("setShowMsg",  response.data.success);
                }
           // commit('setPermissionsRole', response.data.data)
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
    async getAllRolesList ({commit,state},payload){
      commit('setLoading', true)
      await axios.post(ADMIN_ROLES_LIST_API, {}, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('admin_access_token')}`,
        }
      })  .then(response => {
            commit('setLoading', false)
            console.log('Response Roles List:', response.data);
            commit('setRolesList',response.data.data);
          })
          .catch(error => {
            commit('setLoading', false)
            console.error('Error:', error);
          });
    },
  },
  getters: {
    getCurrentFilterDate(state){
      return state.filter_date;
    },
    getIsTonalSnackbarVisible(state) {
      return state.isTonalSnackbarVisible;
    },
    getRolesList(state){
      return state.rolesList
    },
    getSingleRole(state){
      return state.singleRole
    },
    
    getPermissions(state){
      return state.permissions
    },
    getProductsList(state){
      return state.productsList
    },
    getDashboardData(state){
      return state.dashboardData
    },
    
    getIsLoading(state){
      return state.isLoading
    },
     getErrorMsg(state) {
        return state.isErrorMsg
    },
    getSuccessMsg(state) {
      console.log('payload');
        return state.isSuccessMsg
    },
     getShowMsg(state) {
      console.log('payload');
        return state.showMessage 
    },
    
    
    getMedcineList(state){
      return state.medicineList
    },
     getAdminDetail(state){
      return state.adminDetail
    },
    getSiteSetting(state){
      return state.siteSetting
    },
    getCheckLoginExpire(state){
      return state.checkLoginExpire
    },
    getSingleProduct(state, payload) {
      return state.singleProduct;
    },
     getAdminUserList(state, payload) {
      return state.adminUserList;
    },
    getSingleUser(state, payload) {
      return state.singleUser;
    },
    getPermissionsRole(state, payload) {
         return state.permissionsRole 
    },
       getPermissionUser(state, payload) {
           return state.permissionUser
    },
}
})
