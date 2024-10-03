<template>
  <v-container>
    <v-row>
      <v-col cols="12" sm="6">
        <v-select
          v-model="selectedStrain"
          :items="strains"
          item-text="name"
          item-value="id"
          label="Выберите штамм"
          @change="fetchAnalyzeStrains"
        ></v-select>
      </v-col>
      <v-col cols="12" sm="6">
        <v-btn @click="fetchAnalyzeStrains" :disabled="!selectedStrain">
          Загрузить записи
        </v-btn>
      </v-col>
    </v-row>

    <v-carousel v-if="analyzeStrains.length > 0">
      <v-carousel-item
        v-for="(record, index) in analyzeStrains"
        :key="record.id"
      >
        <v-card>
          <v-card-title>
            Запись {{ index + 1 }}
          </v-card-title>
          <v-card-text>
            <p><strong>Повторяющаяся последовательность:</strong> {{ record.repeat_sequence }}</p>
            <p><strong>Статус:</strong> {{ record.status }}</p>
          </v-card-text>
          <v-card-actions>
            <v-btn color="green" @click="updateStatus(record.id, 'approved')">
              Одобрено
            </v-btn>
            <v-btn color="red" @click="updateStatus(record.id, 'rejected')">
              Отклонено
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-carousel-item>
    </v-carousel>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      strains: [], // Список штаммов
      selectedStrain: null, // Выбранный штамм
      analyzeStrains: [] // Список записей анализа
    };
  },
  created() {
    this.fetchStrains();
  },
  methods: {
    // Получаем список штаммов
    async fetchStrains() {
      const token = localStorage.getItem('token');
      try {
        const response = await fetch('http://localhost:8000/api/strain/name', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`, // Добавляем токен в заголовок
          },
        });

        if (!response.ok) {
          throw new Error('Ошибка сети: ' + response.statusText);
        }

        const data = await response.json();
        console.log(data);

        // Извлекаем имена штаммов из ключа 'data'
        if (Array.isArray(data.data)) {
          this.strains = data.data; // Убедитесь, что это массив объектов с id и name
        } else {
          console.error('Ожидается массив, но получен другой тип данных:', data.data);
        }
      } catch (error) {
        console.error('Ошибка при получении штаммов:', error);
      }
    },
    // Получаем записи анализа для выбранного штамма
    async fetchAnalyzeStrains() {
      const token = localStorage.getItem('token');
      if (!this.selectedStrain) return;
      console.log(this.selectedStrain)
      try {
        const response = await fetch(`/api/analyze_strains?name=${this.selectedStrain}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
        });

        if (!response.ok) {
          throw new Error('Ошибка сети: ' + response.statusText);
        }

        const data = await response.json();
        this.analyzeStrains = data; // Предполагается, что API возвращает массив объектов записей анализа
 } catch (error) {
        console.error('Ошибка при получении записей анализа:', error);
      }
    },
    // Обновляем статус записи
    async updateStatus(recordId, newStatus) {
      try {
        const response = await fetch(`/api/analyze_strains/${recordId}`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ status: newStatus }),
        });

        if (!response.ok) {
          throw new Error('Ошибка сети: ' + response.statusText);
        }

        // Если обновление прошло успешно, удаляем запись из списка
        this.analyzeStrains = this.analyzeStrains.filter(record => record.id !== recordId);
      } catch (error) {
        console.error('Ошибка при обновлении статуса:', error);
      }
    }
  }
};
</script>

<style scoped>
/* Добавьте стили по необходимости */
</style>