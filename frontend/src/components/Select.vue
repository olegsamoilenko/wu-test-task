<script setup lang="ts">
defineEmits(['update:modelValue'])

withDefaults(
  defineProps<{
    id: string
    label?: string
    modelValue?: string | number
    options: { label: string; value: string | number }[]
  }>(),
  {}
)
</script>

<template>
  <div class="select-container">
    <label :for="id" class="label">
      {{ label }}
    </label>
    <select
      :id="id"
      class="select"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target?.value)"
    >
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>
  </div>
</template>

<style scoped>
.label {
  display: block;
  margin-bottom: 5px;
}

.select {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}
</style>
