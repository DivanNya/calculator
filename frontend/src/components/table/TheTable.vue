<template>
  <table class=" w-full divide-y  border-collapse">
    <thead class="bg-gray-100 rounded-t-lg">
      <tr>
        <th class="px-6 py-3 text-center text-sm font-bold text-slate-700 uppercase tracking-wider  border-gray-300">
          Т/M
        </th>
        <th
          v-for="tonnage in getTonnages"
          :key="tonnage.id"
          class="px-6 py-3 text-center text-sm font-bold text-slate-700 uppercase tracking-wider  border-gray-300"
        >
          {{ tonnage.value }}
        </th>
      </tr>
    </thead>
    <tbody class="text-center">
      <tr
        v-for="(month, index) in getMonths"
        :key="month.id"
        :class="index % 2 === 0 ? '' : 'bg-gray-100'"
      >
          <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700 text-center">
              {{ month.name }}
          </td>
          <td
              v-for="tonnage in getTonnages"
              :key="tonnage.id"
              class="px-6 py-4 whitespace-nowrap"
              :class="getStyles(month.id, tonnage.id)"
          >
              {{ getPrice(month.id, tonnage.id) }}
          </td>
      </tr>
    </tbody>
  </table>
</template>

<script>

import { mapState } from 'vuex';

export default {
  props: {
    selectedMonthId: Number,
    selectedTonnageId: Number,
    selectedTypeId: Number
  },
  computed: {
    ...mapState(['price_list']),
    ...mapState(['price']),
    ...mapState(['tonnage']),
    ...mapState(['months']),

    getTonnages() {
      return this.tonnage;
    },
    getMonths() {
      return this.months;
    },
  },
  methods: {
    getPrice(monthId, tonnageId){
        const found = this.price_list.find(item =>
            item.month_id === monthId
            && item.raw_type_id === this.selectedTypeId
            && item.tonnage_id === tonnageId
        );

        return found ? found.price : '';
    },
    getStyles(monthId, tonnageId) {
      if (this.selectedMonthId === monthId && this.selectedTonnageId === tonnageId) {
        return 'selected-cell';
      }

      return '';
    }
  }
};
</script>

<style scoped>

.selected-cell {
  background: orange;
  color: white;
  font-weight: bold;
  border-radius: 5px;
}
  table tr td:last-child {border-radius: 0 5px 5px 0}
  table tr td:first-child {border-radius: 5px 0 0 5px}

  table tr th:last-child {border-radius: 0 5px 5px 0}
  table tr th:first-child {border-radius: 5px 0 0 5px}
</style>