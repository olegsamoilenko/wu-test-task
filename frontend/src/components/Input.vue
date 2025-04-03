<script setup lang="ts">
defineEmits(['update:modelValue'])

withDefaults(
  defineProps<{
    id: string
    type?: string
    label?: string
    modelValue?: string | number
    placeholder?: string
    required?: boolean
  }>(),
  {
    type: 'text',
    required: false
  }
)
</script>

<template>
  <div class="input-container">
    <label :for="id" class="label">
      {{ label }}
    </label>
    <input
      :id="id"
      :type="type"
      :placeholder="placeholder"
      class="input"
      :required="required"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target?.value)"
    />
  </div>
</template>

<style scoped>
.input-container {
  width: 100%;
}
.label {
  display: block;
  margin-bottom: 5px;
}
.input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
</style>
