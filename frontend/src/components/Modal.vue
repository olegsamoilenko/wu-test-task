<script setup lang="ts">
const props = defineProps<{ open: boolean; title: string }>()

const emit = defineEmits(['update:open'])
</script>

<template>
  <div v-if="open" class="modal-backdrop" @click.self="$emit('update:open', false)">
    <div class="modal">
      <h2>{{ title }}</h2>
      <div>
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  width: 600px;
  max-width: 90%;
}
.modal input {
  display: block;
  width: 100%;
  margin-bottom: 0.75rem;
  padding: 0.5rem;
}
.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}
</style>
