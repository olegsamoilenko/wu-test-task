<script setup lang="ts">
import Modal from './Modal.vue'
import { reactive } from 'vue'
import Input from './Input.vue'
import Button from './Button.vue'

const props = defineProps<{ open: boolean }>()

const emit = defineEmits(['update:open'])

const form = reactive({
  post_office_post_code: '',
  postal_code: '',
  region_ua: '',
  region_en: '',
  district_old_ua: '',
  district_new_ua: '',
  district_new_en: '',
  settlement_ua: '',
  settlement_en: '',
  post_office_ua: '',
  post_office_en: ''
})

async function handleSubmit() {
  try {
    const res = await fetch('/api/postcodes', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form)
    })

    if (!res.ok) throw new Error('Не вдалося додати запис')

    const data = await res.json()
    console.log(data)

    alert('Запис успішно додано!')
    emit('update:open', false)
    Object.keys(form).forEach(key => {
      form[key as keyof typeof form] = ''
    })
  } catch (err) {
    alert('Сталася помилка при додаванні')
    console.error(err)
  }
}
</script>

<template>
  <Modal
    title="Додати новий поштовий індекс"
    :open="open"
    @update:open="$emit('update:open', false)"
  >
    <form class="new-postindex-form" @submit.prevent="handleSubmit">
      <Input
        id="region-ua"
        v-model="form.region_ua"
        placeholder="Область"
        label="Область"
        required
      />
      <Input
        id="district-old-ua"
        v-model="form.district_old_ua"
        placeholder="Район (старий)"
        label="Район (старий)"
        required
      />
      <Input
        id="district-new-ua"
        v-model="form.district_new_ua"
        placeholder="Район (новий)"
        label="Район (новий)"
        required
      />
      <Input
        id="settlement-ua"
        v-model="form.settlement_ua"
        placeholder="Населений пункт"
        label="Населений пункт"
        required
      />
      <Input
        id="postal-code"
        v-model="form.postal_code"
        placeholder="Поштовий індекс (Postal code)"
        label="Поштовий індекс (Postal code)"
        required
      />
      <Input
        id="region-en"
        v-model="form.region_en"
        placeholder="Region (Oblast)"
        label="Region (Oblast)"
        required
      />
      <Input
        id="district-new-en"
        v-model="form.district_new_en"
        placeholder="District new (Raion new)"
        label="District new (Raion new)"
        required
      />
      <Input
        id="settlement-en"
        v-model="form.settlement_en"
        placeholder="Settlement"
        label="Settlement"
        required
      />
      <Input
        id="post-office-ua"
        v-model="form.post_office_ua"
        placeholder="Відділення зв'язку"
        label="Відділення зв'язку"
        required
      />
      <Input
        id="post-office-en"
        v-model="form.post_office_en"
        placeholder="Post office"
        label="Post office"
        required
      />
      <Input
        id="post-office-post-code"
        v-model="form.post_office_post_code"
        placeholder="Поштовий індекс відділення зв'язку (Post code of post office)"
        label="Поштовий індекс відділення зв'язку (Post code of post office)"
        required
      />

      <div class="actions">
        <Button @click="$emit('update:open', false)">Скасувати</Button>
        <Button type="submit">Зберегти</Button>
      </div>
    </form>
  </Modal>
</template>

<style scoped>
.new-postindex-form > * {
  margin-bottom: 1rem;
}

.actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}
</style>
