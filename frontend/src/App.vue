<script setup lang="ts">
import { onMounted, ref } from 'vue'
import type { Postcode } from './types'
import Button from './components/Button.vue'
import Input from './components/Input.vue'
import Select from './components/Select.vue'
import AddNewPostindexModal from './components/AddNewPostindexModal.vue'

const postcodes = ref<Postcode[]>([])
const page = ref(1)
const selected = ref('postcode')
const searchValue = ref('')
const modalOpen = ref(false)

async function handleFetchPostcodes(params: {
  page?: number
  postcode?: string
  address?: string
}) {
  const query = new URLSearchParams()

  if (params.page) query.append('page', String(params.page))
  if (params.postcode) query.append('postcode', params.postcode)
  if (params.address) query.append('address', params.address)

  const res = await fetch(`/api/postcodes?${query.toString()}`)
  const data = await res.json()
  postcodes.value = data.data
  page.value = data.page
  console.log('postcodes', postcodes.value)
}

async function handleSearch(value: string) {
  if (value.length > 2) {
    if (selected.value === 'postcode') {
      await handleFetchPostcodes({ postcode: value })
    } else {
      await handleFetchPostcodes({ address: value })
    }
  }
}

async function handleDelete(postcode: string) {
  const res = await fetch(`/api/postcodes/${postcode}`, {
    method: 'DELETE'
  })
  if (res.ok) {
    postcodes.value = postcodes.value.filter(item => item.post_office_post_code !== postcode)
    alert('Запис успішно видалено!')
  } else {
    alert('Не вдалося видалити запис')
  }
}

async function paginate(direction: 'prev' | 'next') {
  const query: { postcode: string } | { address: string } =
    selected.value === 'postcode' ? { postcode: searchValue.value } : { address: searchValue.value }
  if (direction === 'prev' && page.value > 1) {
    page.value--
    await handleFetchPostcodes({ page: page.value, ...query })
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } else if (direction === 'next') {
    page.value++
    await handleFetchPostcodes({ page: page.value, ...query })
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

onMounted(async () => {
  console.log('fetching postcodes')
  await handleFetchPostcodes({ page: page.value })
})
</script>

<template>
  <div>
    <div class="actions-block">
      <div class="actions-block__search">
        <Select
          id="search-criteria"
          label="Пошук за"
          v-model="selected"
          :options="[
            { label: 'Поштовий індекс', value: 'postcode' },
            { label: 'Адреса', value: 'address' }
          ]"
        />
        <Input
          id="search-input"
          :label="`Пошук за ${selected === 'postcode' ? 'поштовим індексом' : 'адресою'}`"
          :placeholder="`Введіть ${selected === 'postcode' ? 'поштовий індекс' : 'адресу'}`"
          v-model="searchValue"
          @update:model-value="handleSearch"
        />
      </div>
      <Button @handleClick="modalOpen = true"> Додати новий поштовий індекс </Button>
    </div>
    <table class="postcode-table">
      <thead>
        <tr>
          <th>Область</th>
          <th>Район (старий)</th>
          <th>Район (новий)</th>
          <th>Населений пункт</th>
          <th>Поштовий індекс (Postal code)</th>
          <th>Region (Oblast)</th>
          <th>District new (Raion new)</th>
          <th>Settlement</th>
          <th>Відділення зв'язку</th>
          <th>Post office</th>
          <th>Поштовий індекс відділення зв'язку (Post code of post office)</th>
          <th>Дії</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in postcodes" :key="item.post_office_post_code">
          <td>{{ item.region_ua }}</td>
          <td>{{ item.district_old_ua }}</td>
          <td>{{ item.district_new_ua }}</td>
          <td>{{ item.settlement_ua }}</td>
          <td>{{ item.postal_code }}</td>
          <td>{{ item.region_en }}</td>
          <td>{{ item.district_new_en }}</td>
          <td>{{ item.settlement_en }}</td>
          <td>{{ item.post_office_ua }}</td>
          <td>{{ item.post_office_en }}</td>
          <td>{{ item.post_office_post_code }}</td>
          <td>
            <Button @handle-click="() => handleDelete(item.post_office_post_code)">Delete</Button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="pagination-buttons">
      <Button @handleClick="() => paginate('prev')"> Prev </Button>
      <Button @handleClick="() => paginate('next')"> Next </Button>
    </div>
    <AddNewPostindexModal v-model:open="modalOpen" />
    <!--    <Modal v-model:open="modalOpen" />-->
  </div>
</template>

<style scoped>
.actions-block {
  display: flex;
  justify-content: space-between;
  align-items: end;
  gap: 40px;
  margin-bottom: 10px;
}

.actions-block > * {
  flex: 1;
}

.actions-block__search {
  display: flex;
  gap: 10px;
}

.postcode-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 10px;
}

.postcode-table th,
.postcode-table td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: left;
}

.postcode-table th {
  background-color: #f0f0f0;
}

.pagination-buttons {
  display: flex;
  justify-content: end;
  gap: 5px;
}
</style>
