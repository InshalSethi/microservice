
<script setup>
import { computed, onMounted, ref } from 'vue';
import { useStore } from 'vuex';
import FolderTreeItem from './FolderTreeItem.vue';
const route = useRoute();
const store = useStore()
const loading = ref(true)
const searchQuery = ref('')
const permissionsTree = ref([])
const selectedPermissions = ref([])

const dummyPermissionsTree = [
  {
    id: 1,
    name: 'User Management',
    children: [
      { id: 2, name: 'Create User' },
      { id: 3, name: 'Edit User' },
      { id: 4, name: 'Delete User' },
      { id: 5, name: 'View User' }
    ]
  },
  {
    id: 6,
    name: 'Content Management',
    children: [
      { id: 7, name: 'Create Post' },
      { id: 8, name: 'Edit Post' },
      { id: 9, name: 'Delete Post' },
      { id: 10, name: 'Publish Post' }
    ]
  },
  {
    id: 11,
    name: 'Settings',
    children: [
      { id: 12, name: 'General Settings' },
      { id: 13, name: 'Security Settings' },
      { id: 14, name: 'Email Settings' }
    ]
  }
]

const selectedPermissionsIds = computed(() => {
  return selectedPermissions.value.join(',')
})

const filteredPermissionsTree = computed(() => {
  if (!searchQuery.value) return permissionsTree.value

  const searchLower = searchQuery.value.toLowerCase()

  function filterTree(nodes) {
    return nodes.reduce((filtered, node) => {
      const nameMatch = node.text.toLowerCase().includes(searchLower)
      if (node.children) {
        const filteredChildren = filterTree(node.children)
        if (nameMatch || filteredChildren.length) {
          filtered.push({
            ...node,
            children: filteredChildren
          })
        }
      } else if (nameMatch) {
        filtered.push(node)
      }
      return filtered
    }, [])
  }

  return filterTree(permissionsTree.value)
})

const fetchPermissions = async () => {
  await store.dispatch('permissionsRole', {
    id: route.params.id,
  });
  let permission = store.getters.getPermissionsRole;
  console.log(permission);
  
  // Process the permissions to add state if not present
  const processPermissions = (items) => {
    return items.map(item => {
      if (item.children) {
        item.children = processPermissions(item.children);
      }
      if (!item.state) {
        item.state = { selected: false };
      }
      return item;
    });
  };

  permissionsTree.value = processPermissions(permission.children);
  loading.value = false;
};

function searchPermissions() {
  // The filtering is handled by the computed property filteredPermissionsTree
}

const savePermissions1 = async () => {
  console.log('Saving permissions:', selectedPermissionsIds.value)
  await store.dispatch('permissionsUpdate', {
    id: route.params.id,
    permisssions:selectedPermissions.value.join(',')
   })
  console.log('Saving permissions:', selectedPermissionsIds.value)
  // You would typically make an API call here to save the permissions
}
const savePermissions = async () => {
  const getSelectedIds = (items) => {
    return items.reduce((acc, item) => {
      if (item.children) {
        acc.push(...getSelectedIds(item.children));
      } else if (item.state && item.state.selected) {
        acc.push(item.id);
      }
      return acc;
    }, []);
  };

  const selectedIds = getSelectedIds(permissionsTree.value);
  console.log('Saving permissions:', selectedIds.join(','));
  await store.dispatch('permissionsUpdate', {
    id: route.params.id,
    permisssions: selectedIds.join(',')
  });
};
function togglePermission(id) {
  const index = selectedPermissions.value.indexOf(id)
  if (index === -1) {
    selectedPermissions.value.push(id)
  } else {
    selectedPermissions.value.splice(index, 1)
  }
}
  const updateSelectedPermissions = (newSelectedPermissions) => {
      selectedPermissions.value = newSelectedPermissions;
    };
onMounted(() => {
  fetchPermissions()
  
  
})
</script>
<template>
  <v-container fluid>
    <v-row>
      <v-col>
        <v-card>
          <v-card-title>
            <h3>Assign Role Permissions</h3>
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <h4 v-if="loading">Loading.....</h4>
                <div v-else>
                  <v-text-field
                    v-model="searchQuery"
                    label="Search Permission..."
                    prepend-inner-icon="mdi-magnify"
                    @input="searchPermissions"
                  ></v-text-field>
                  <folder-tree-item
                    v-for="item in filteredPermissionsTree"
                    :key="item.id"
                    :item="item"
                      :selected-permissions="selectedPermissions"
                      @update:selected-permissions="updateSelectedPermissions"
                    @toggle-permission="togglePermission"
                  />
                  <v-form @submit.prevent="savePermissions">
                    <input type="hidden" name="permissions" :value="selectedPermissionsIds">
                    <v-btn
                      color="primary"
                      type="submit"
                      class="mt-4"
                    >
                      Save Permissions
                    </v-btn>
                  </v-form>
                </div>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>


