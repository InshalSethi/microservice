<script setup>
import API from '@/api';
import { ADMIN_GET_ROLES_API } from '@/constants';
import AddRole from '@/pages/roles/AddRole.vue';
import EditRole from '@/pages/roles/EditRole.vue';
import { format } from 'date-fns';
import debounce from 'lodash.debounce';
import { useStore } from 'vuex';
const router = useRouter();
const isAddCustomerDrawerOpen = ref(false)
const isEditCustomerDrawerOpen = ref(false)
const store = useStore()
const editDialog = ref(false)
const addDialog = ref(false)
const deleteDialog = ref(false)
const search = ref('')
const refVForm = ref(null)
const refVFormAdd = ref(null)
const selectDropdown = ref(false)
const selectdataList = ref(null)
const defaultItem = ref({
    id: -1,
    title: '',
    slug: '',
    list_one_title: '',
    list_sub_title: '',
    list_two_title: '',
    price: '',
    currency: ""
})
const requiredImageValidator = (value) => !!value || 'Please select an image file.'
const requiredExcelValidator = (value) => !!value || 'Please select an Excel file.'
const imageFile = ref(null)
const excelFile = ref(null)
const editedItem = ref(defaultItem.value)
const editedIndex = ref(-1)
const medicineList = ref([])
const isLoading = ref(false)
const currencySign = ref('$');
const imageBase64 = ref(null)
const excelBase64 = ref(null)
const currencies = ref([
    { code: 'USD', name: 'US Dollar', sign: '$' },
    { code: 'EUR', name: 'Euro', sign: '€' },
    { code: 'GBP', name: 'British Pound', sign: '£' },
    { code: 'JPY', name: 'Japanese Yen', sign: '¥' },
])
const setCurrency = (code, sign) => {
    currencySign.value = sign;
    // You can perform additional operations with the selected currency code if needed
};
// status options
const selectedOptions = [
    {
        text: 'Current',
        value: 1,
    },
    {
        text: 'Professional',
        value: 2,
    },
    {
        text: 'Rejected',
        value: 3,
    },
    {
        text: 'Resigned',
        value: 4,
    },
    {
        text: 'Applied',
        value: 5,
    },
]

// headers
const headers = [
    {
        title: 'ID',
        key: 'id',


    },
    {
        title: 'Name',
        key: 'role_name',
    },
    // {
    //   title: 'Slug',
    //   key: 'slug',
    // },
    {
        title: 'Guard',
        key: 'role_guard',
    },
    {
        title: 'Created',
        key: 'created_at',
    },
    {
        title: 'Updated',
        key: 'updated_at',
    },

    {
        title: 'ACTIONS',
        key: 'actions',
        searchable: false
    },
]

const resolveStatusVariant = status => {
    if (status === 1)
        return {
            color: 'primary',
            text: 'Current',
        }
    else if (status === 2)
        return {
            color: 'success',
            text: 'Professional',
        }
    else if (status === 3)
        return {
            color: 'error',
            text: 'Rejected',
        }
    else if (status === 4)
        return {
            color: 'warning',
            text: 'Resigned',
        }
    else
        return {
            color: 'info',
            text: 'Applied',
        }
}

const formatDateTime = (date) => {
    return format(date, 'MM-dd-yyyy');
};

const editItem = async (item) => {
    isEditCustomerDrawerOpen.value = true

    await store.dispatch('getRoleByID', {
        id: item.id,
    })

    editedItem.value = store.getters.getSingleRole
    console.log('here', store.getters.getSingleRole)
    // editDialog.value = true
}
const addItem = item => {
    addDialog.value = true
}
const deleteItem = item => {
    editedIndex.value = medicineList.value.indexOf(item)
    editedItem.value = { ...item }
    deleteDialog.value = true
}
const permissionRole = item => {
    router.push({ name: "admin-role-permission", params: { id: item.id } });

}

const selectfile = (data) => {
    if (data == 'dropdown') {
        selectDropdown.value = true
    } else {
        selectDropdown.value = false
    }

}
const close = () => {
    editDialog.value = false
    editedIndex.value = -1
    editedItem.value = { ...defaultItem.value }
}
const closeAdd = () => {
    addDialog.value = false
}
const closeDelete = () => {
    deleteDialog.value = false
    editedIndex.value = -1
    editedItem.value = { ...defaultItem.value }
}
const convertImageToBase64 = (event) => {
    const file = event.target.files[0]
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = () => {
        imageBase64.value = reader.result.split(',')[1]
    }
}

const convertExcelToBase64 = (event) => {
    const file = event.target.files[0]
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = () => {
        excelBase64.value = reader.result.split(',')[1]
    }
}

const deleteItemConfirm = async () => {

    await store.dispatch('deleteRole', {
        id: editedItem.value.id
    })

    closeDelete()
    loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] });
    defaultItem.value.id = null
    defaultItem.value.name = null
    defaultItem.value.guard = null

}
const generateSlug = () => {
    console.log(editedItem.title)
    if (editedItem.value.title) {
        editedItem.value.slug = editedItem.value.title
            .toString()
            .trim()
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w-]+/g, '')
            .replace(/--+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '')
    } else if (defaultItem.value.title) {
        defaultItem.value.slug = defaultItem.value.title
            .toString()
            .trim()
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w-]+/g, '')
            .replace(/--+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '')
    } else {
        editedItem.slug = ''
    }
}
const serverItems = ref([]);
const loading = ref(true);
const totalItems = ref(0);

// Method to load items
const loadItems = debounce(async ({ page, itemsPerPage, sortBy }) => {
    const payload = {
        page,
        itemsPerPage,
        sortBy,
        filters: {

        },
        search: search.value,
    }
    console.log("records", page, itemsPerPage, sortBy);
    loading.value = true;
    const data = await API.getDataTableRecord(ADMIN_GET_ROLES_API, payload, headers);
    serverItems.value = data.items;
    totalItems.value = data.total;
    loading.value = false;
}, 500);
const itemsPerPage = ref(5);
onMounted(() => {

})
const handleParentAdded = async (msg) => {
    if (msg == 'success') {
        store.dispatch('updateIsLoading', true)
        loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] });
        store.dispatch('updateIsLoading', false)
    }
    // You can also trigger a toast or snackbar here to show the message
    // For example, if using Vuetify:
    // showSnackbar(msg)
}
</script>

<template>
    <v-row>
        <v-col cols="12" md="12">
            <VCard title="Roles">

                <VCardText>
                    <VRow>

                        <VCol cols="12" md="8" class="d-flex align-center">
                            <VBtn color="primary" prepend-icon="ri-add-line"
                                @click="isAddCustomerDrawerOpen = !isAddCustomerDrawerOpen" v-if="$can('read', 'Role Add')">
                                New Role
                            </VBtn>
                        </VCol>
                        <VCol cols="12" md="4" class="d-flex justify-end">
                            <VTextField v-model="search" label="Search" placeholder="Search ..."
                                append-inner-icon="ri-search-line" single-line hide-details dense outlined />
                        </VCol>
                    </VRow>
                </VCardText>
                <!-- <VDataTable
        :headers="headers"
        :items="medicineList"
        :search="search"
        :items-per-page="5"
        class="text-no-wrap"
      > -->
                <v-data-table-server v-model:items-per-page="itemsPerPage" :headers="headers" :items="serverItems"
                    :items-length="totalItems" :loading="loading" :search="search" :item-value="name"
                    @update:options="loadItems">
                    <!-- full name -->
                    <template #item.title="{ item }">
                        <div class="d-flex align-center">
                            <!-- avatar -->
                            <VAvatar size="32" :color="item.image_url ? '' : 'primary'"
                                :class="item.image_url ? '' : 'v-avatar-light-bg primary--text'"
                                :variant="!item.image_url ? 'tonal' : undefined">
                                <VImg v-if="item.image_url" :src="item.image_url" />
                                <span v-else class="text-sm">{{ avatarText(item.name) }}</span>
                            </VAvatar>

                            <div class="d-flex flex-column ms-3">
                                <span class="d-block font-weight-medium text-high-emphasis text-truncate">{{ item.title
                                    }}</span>

                            </div>
                        </div>
                    </template>

                    <!-- status -->
                    <template #item.status="{ item }">
                        <VChip :color="resolveStatusVariant(item.status).color" density="comfortable">
                            {{ resolveStatusVariant(item.status).text }}
                        </VChip>
                    </template>

                    <template #item.created_at="{ item }">
                        <span>{{ formatDateTime(item.created_at) }}</span>
                    </template>

                    <template #item.updated_at="{ item }">
                        <span>{{ formatDateTime(item.updated_at) }}</span>
                    </template>

                    <!-- Actions -->
                    <template #item.actions="{ item }">
                        <!-- <div class="d-flex gap-1">
                            <IconBtn size="small" @click="editItem(item)">
                                <VIcon icon="ri-pencil-line" />
                            </IconBtn>
                            <IconBtn size="small" @click="deleteItem(item)">
                                <VIcon icon="ri-delete-bin-line" />
                            </IconBtn>
                        </div> -->
                        <div class="demo-space-x">
                            <VMenu transition="scale-transition">
                                <template #activator="{ props }">
                                    <VBtn color="primary" v-bind="props">
                                        Options
                                    </VBtn>
                                </template>

                                <v-list lines="one" style="padding-right: 5px;padding-left: 5px;">
                                    <v-list-item
                                        style="border-bottom: 1px solid #eadfdf;padding-right: 15px;padding-left: 15px;"
                                        @click="editItem(item)"  v-if="$can('read', 'Role Edit')">
                                        <VIcon icon="ri-pencil-line" /> Edit
                                    </v-list-item>
                                    <v-list-item
                                        style="border-bottom: 1px solid #eadfdf;padding-right: 15px;padding-left: 15px;"
                                        @click="deleteItem(item)" v-if="$can('read', 'Role Delete')">
                                        <VIcon icon="ri-delete-bin-line" /> Delete
                                    </v-list-item>
                                    <v-list-item
                                        style="border-bottom: 1px solid #eadfdf;padding-right: 15px;padding-left: 15px;"
                                        @click="permissionRole(item)">
                                        <VIcon icon="ri-shield-check-line" v-if="$can('read', 'Role Permissions')"/> Permissions
                                    </v-list-item>
                                </v-list>
                            </VMenu>
                        </div>
                    </template>
                </v-data-table-server>
                <!-- </VDataTable> -->
            </VCard>
        </v-col>

        <AddRole v-model:is-drawer-open="isAddCustomerDrawerOpen" @addedMessage="handleParentAdded" />
        <EditRole v-model:is-drawer-open="isEditCustomerDrawerOpen" :user-data="store.getters.getSingleRole"
            @addedMessage="handleParentAdded" />
    </v-row>
    <!-- 👉 Edit Dialog  -->
    <VDialog v-model="editDialog" max-width="600px">
        <VForm ref="refVForm">
            <VCard>
                <VCardTitle>
                    <span class="headline">Edit Role</span>
                </VCardTitle>

                <VCardText>
                    <VContainer>

                        <VRow>
                            <!-- fullName -->
                            <VCol cols="12" md="12">
                                <VTextField v-model="editedItem.name" label="Name" :rules="[requiredValidator]" />
                            </VCol>
                            <VCol cols="12" md="12">
                                <VTextField v-model="editedItem.guard" label="Guard" :rules="[requiredValidator]" />
                            </VCol>

                        </VRow>
                    </VContainer>
                </VCardText>

                <VCardActions>
                    <VSpacer />

                    <VBtn color="error" variant="outlined" @click="close">
                        Cancel
                    </VBtn>

                    <VBtn color="success" variant="elevated" @click="update">
                        Save
                    </VBtn>

                </VCardActions>
            </VCard>
        </VForm>
    </VDialog>

    <!-- 👉 Delete Dialog  -->
    <VDialog v-model="deleteDialog" max-width="500px">
        <VCard>
            <VCardTitle>
                Are you sure you want to delete this item?
            </VCardTitle>

            <VCardActions>
                <VSpacer />

                <VBtn color="error" variant="outlined" @click="closeDelete">
                    Cancel
                </VBtn>

                <VBtn color="success" variant="elevated" @click="deleteItemConfirm">
                    OK
                </VBtn>

                <VSpacer />
            </VCardActions>
        </VCard>
    </VDialog>


    <!-- 👉 Add Dialog  -->
    <VDialog v-model="addDialog" max-width="600px">
        <VForm ref="refVFormAdd">
            <VCard>
                <VCardTitle>
                    <span class="headline">Add Role</span>
                </VCardTitle>

                <VCardText>
                    <VContainer>

                        <VRow>
                            <!-- fullName -->
                            <VCol cols="12" md="12">
                                <VTextField v-model="defaultItem.name" label="Name" :rules="[requiredValidator]" />
                            </VCol>
                            <VCol cols="12" md="12">
                                <VTextField v-model="defaultItem.guard" label="Guard" :rules="[requiredValidator]" />
                            </VCol>

                        </VRow>
                    </VContainer>
                </VCardText>

                <VCardActions>
                    <VSpacer />

                    <VBtn color="error" variant="outlined" @click="closeAdd">
                        Cancel
                    </VBtn>

                    <VBtn color="success" variant="elevated" @click="save">
                        Save
                    </VBtn>

                </VCardActions>
            </VCard>
        </VForm>
    </VDialog>
</template>
<style scoped>
.custom-button {
    width: 100%;
    height: 48px;
    /* This value should match the height of your input fields */
}
</style>
