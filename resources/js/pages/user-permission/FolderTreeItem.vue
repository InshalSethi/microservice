<template>
  <div class="folder-tree-item">
    <div
      @click="toggleExpanded"
      :class="['folder-tree-item-header', { 'is-expanded': isExpanded }]"
    >
      <v-checkbox
        :model-value="isSelected"
        :indeterminate="isIndeterminate"
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
          @toggle-permission="togglePermission"
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
        return props.item.children.every(child => {
          if (child.children) {
            return child.children.every(subChild => 
              (subChild.state && subChild.state.selected) || 
              props.selectedPermissions.includes(subChild.id)
            );
          }
          return (child.state && child.state.selected) || 
                 props.selectedPermissions.includes(child.id);
        });
      }
      return (props.item.state && props.item.state.selected) || 
             props.selectedPermissions.includes(props.item.id);
    });

    const isIndeterminate = computed(() => {
      if (props.item.children) {
        const totalCount = props.item.children.reduce((count, child) => {
          if (child.children) {
            return count + child.children.length;
          }
          return count + 1;
        }, 0);

        const selectedCount = props.item.children.reduce((count, child) => {
          if (child.children) {
            return count + child.children.filter(subChild => 
              (subChild.state && subChild.state.selected) || 
              props.selectedPermissions.includes(subChild.id)
            ).length;
          }
          return count + ((child.state && child.state.selected) || 
                         props.selectedPermissions.includes(child.id) ? 1 : 0);
        }, 0);

        return selectedCount > 0 && selectedCount < totalCount;
      }
      return false;
    });


    const toggleExpanded = () => {
      if (props.item.children) {
        isExpanded.value = !isExpanded.value;
      }
    };

    const togglePermission = (item) => {
      const newSelected = !isSelected.value;
      
      const updateItemState = (item, selected) => {
        if (item.children) {
          item.children.forEach(child => {
            if (child.state) {
              child.state.selected = selected;
            } else {
              child.state = { selected };
            }
            updateItemState(child, selected);
          });
        } else {
          if (item.state) {
            item.state.selected = selected;
          } else {
            item.state = { selected };
          }
        }
      };

      updateItemState(item, newSelected);

      const updateSelectedPermissions = (item, selected) => {
        if (item.children) {
          item.children.forEach(child => updateSelectedPermissions(child, selected));
        } else {
          const index = props.selectedPermissions.indexOf(item.id);
          if (selected && index === -1) {
            props.selectedPermissions.push(item.id);
          } else if (!selected && index > -1) {
            props.selectedPermissions.splice(index, 1);
          }
        }
      };

      updateSelectedPermissions(item, newSelected);

      emit('update:selected-permissions', [...props.selectedPermissions]);
    };

    return {
      isExpanded,
      isSelected,
      isIndeterminate,
      toggleExpanded,
      togglePermission,
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
