<template>
  <li class="nav-item" :class="item.class?item.class:''">
   
<div class="demo-space-x">
    <VMenu transition="scale-transition">
      <template #activator="{ props }">
        <Component
          :is="itemIcon(item)"
          v-if="!hideTitleAndIcon && item.icon"
          :class="itemIconClass(item)"
        />
        <VBtn v-bind="props" block    variant="text"
      color="rgb(46,38,61)">
          <VIcon v-if="item.icon" :icon="item.icon.icon" class="mr-2" size="20"/>{{ item.title }}
        </VBtn>
      </template>

      <VList class="list-reset">
        <VListItem
          v-for="(child, index) in item.children"
          :key="index"
          class="list-item-reset"
        >
        
          <VerticalNavLink :item="child" :hideTitleAndIcon="hideTitleAndIcon"  />
        </VListItem>
      </VList>
    </VMenu>
</div>
  </li>
</template>


<script setup>
import VerticalNavLink from './VerticalNavLink.vue';

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  hideTitleAndIcon: {
    type: Boolean,
    default: false,
  },
  hideMarker: {
    type: Boolean,
    default: true,
  },
})

const itemIcon = (item) => {
  return item.icon.icon ? item.icon.icon  : ''
}

const itemIconClass = (item) => {
  return item.icon ? item.icon.class : ''
}
</script>

<style scoped>
.nav-item {
  list-style-type: none; /* Removes default list styling */
  padding: 0; /* Removes default padding */
}

.list-reset {
  list-style: none; /* Removes bullets or numbers from the list */
  padding: 0; /* Removes default padding */
  margin: 0; /* Removes default margin */
}

.list-item-reset {
  list-style: none; /* Ensures each list item has no bullets or numbers */
  padding: 0; /* Removes default padding */
  margin: 0; /* Removes default margin */
}

.nav-item > .v-menu {
  width: 100%; /* Ensures the menu takes the full width */
}

.v-btn {
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.bottom-end {
    position: fixed;
    width: 100%;
    bottom: 20px;
   padding-inline: 18px;

}

</style>
