<script setup lang="ts">
const emit = defineEmits(['handleClick'])
const props = withDefaults(
  defineProps<{
    type?: string
  }>(),
  {
    type: 'button'
  }
)
</script>

<template>
  <button class="button" @click="emit('handleClick')">
    <slot></slot>
  </button>
</template>

<style scoped>
.button {
  background-color: #ffffff;
  border: 1px solid #a9a3a3;
  padding: 7px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  cursor: pointer;
}
</style>
