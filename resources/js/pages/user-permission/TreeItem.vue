<template>
  <div class="folder-tree-item">
    <div 
      @click="toggleExpanded"
      :class="['folder-tree-item-header', { 'is-expanded': isExpanded }]"
    >
      <v-checkbox
        :model-value="isSelected"
        @change="togglePermission(item)"
        @click.stop
        class="folder-tree-checkbox"
      ></v-checkbox>
      <v-icon v-if="item.children" :class="isExpanded ? 'folder-yellow' : 'folder-light-gray'">
        {{ isExpanded ? 'ri-folder-open-line' : 'ri-folder-line' }}
      </v-icon>
      <v-icon v-else color="blue-grey">ri-file-list-line</v-icon>
      <span class="folder-tree-item-name">{{ item.text }}</span>
    </div>
    <v-expand-transition>
      <div v-if="item.children && isExpanded" class="folder-tree-item-children">
        <folder-tree-item
          v-for="child in item.children"
          :key="child.id"
          :item="child"
          :selected-permissions="selectedPermissions"
          @update:selected-permissions="updateSelectedPermissions"
        />
      </div>
    </v-expand-transition>
  </div>
</template>

<script>
import { computed, ref } from 'vue';

export default {
  name: 'FolderTreeItem',
  props: {
    item: Object,
    selectedPermissions: Array,
  },
  setup(props, { emit }) {
    const isExpanded = ref(false);

    const isSelected = computed(() => {
      if (props.item.children) {
        return props.item.children.every(child => props.selectedPermissions.includes(child.id));
      }
      return props.selectedPermissions.includes(props.item.id);
    });

    const toggleExpanded = () => {
      if (props.item.children) {
        isExpanded.value = !isExpanded.value;
      }
    };

    const togglePermission = (item) => {
      const toggleChildren = (item, isSelected) => {
        if (item.children) {
          item.children.forEach(child => {
            if (isSelected) {
              if (!props.selectedPermissions.includes(child.id)) {
                props.selectedPermissions.push(child.id);
              }
            } else {
              const index = props.selectedPermissions.indexOf(child.id);
              if (index > -1) {
                props.selectedPermissions.splice(index, 1);
              }
            }
            toggleChildren(child, isSelected);
          });
        }
      };

      if (item.children) {
        const isSelectedValue = !isSelected.value;
        toggleChildren(item, isSelectedValue);
      } else {
        const index = props.selectedPermissions.indexOf(item.id);
        if (index > -1) {
          props.selectedPermissions.splice(index, 1);
        } else {
          props.selectedPermissions.push(item.id);
        }
      }

      emit('update:selected-permissions', props.selectedPermissions);
    };

    const updateSelectedPermissions = (newSelectedPermissions) => {
      emit('update:selected-permissions', newSelectedPermissions);
    };

    return {
      isExpanded,
      isSelected,
      toggleExpanded,
      togglePermission,
      updateSelectedPermissions,
    };
  },
};
</script>

<style>
.folder-tree-item {
  margin-left: 10px;
}

.folder-tree-item-header {
  display: flex;
  align-items: center;
  cursor: pointer;
  margin-bottom: 5px;
}

.folder-tree-item-header.is-expanded .folder-yellow {
  color: #ffca28;
}

.folder-tree-item-header .folder-light-gray {
  color: #90a4ae;
}

.folder-tree-item-name {
  margin-left: 10px;
}

.folder-tree-checkbox {
  margin-right: 10px;
}

.folder-tree-item-children {
  margin-left: 20px;
}
</style>
