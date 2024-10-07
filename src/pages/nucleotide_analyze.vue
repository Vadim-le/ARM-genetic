<template>
  <v-container>
    <v-row justify="center" align="center" class="my-5">
      <v-col cols="12" md="8">
        <v-card class="elevation-12 rounded-lg" style="width: 100%;">
          <v-toolbar color="primary" dark flat>
              <v-toolbar-title class="text-h4 font-weight-bold">
                Анализ нуклеотидов
              </v-toolbar-title>
            </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" sm="6">
                <v-select
                  v-model="selectedStrain"
                  :items="strains"
                  item-text="name"
                  item-value="id"
                  label="Выберите штамм"
                  @change="fetchAnalyzeStrains"
                  outlined
                  dense
                ></v-select>
              </v-col>
              <v-col cols="12" sm="6" class="d-flex align-center justify-center">
                <v-btn @click="fetchAnalyzeStrains" :disabled="!selectedStrain" color="primary" outlined>
                  Загрузить записи
                </v-btn>
              </v-col>
            </v-row>
            <v-alert
        v-if="!selectedStrain"
        type="info"
        class="custom-alert"
        outlined
        dense
      >
        <strong>Примечание:</strong> Сначала выберите штамм, чтобы активировать кнопку анализа.
      </v-alert>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row justify="center" class="my-5">
      <v-col cols="12" md="10">
        <v-carousel v-if="analyzeStrains.length > 0" hide-delimiters>
          <v-carousel-item
            v-for="(record, index) in analyzeStrains"
            :key="record.id"
          >
            <v-card class="mx-auto elevation-8 rounded-lg" max-width="1180">
              <v-card-title class="text-h6">
                Запись {{ index + 1 }}
              </v-card-title>
              <v-card-text class="record-text">
  <p><strong>Известен:</strong> {{ record.is_known ? 'Да' : 'Нет' }}</p>
  <p><strong>Повторяющаяся последовательность:</strong> {{ record.repeat_sequence }}</p>
  <p><strong>Позиции повторов:</strong> {{ record.repeat_positions }}</p>
  <p><strong>Спейсер:</strong> {{ record.spacer_sequence }}</p>
  <p><strong>Позиции спейсера:</strong> {{ record.spacer_positions }}</p>
  <p><strong>Статус:</strong> {{ record.status }}</p>
  <p><strong>Полный контекст:</strong></p>
  <span v-html="highlightText(record.full_context, [record.repeat_sequence, record.spacer_sequence])"></span>
  <v-alert type="info" class="custom-alert" outlined>
    <strong>Примечание:</strong> Желтым цветом выделены повторяющиеся последовательности, а голубым цветом выделены спейсеры.
  </v-alert>
</v-card-text>
              <v-card-actions class="d-flex justify-center">
                <v-btn color="success" @click="updateStatus(record.id, 'approved')" outlined>
                  Одобрено
                </v-btn>
                <v-btn color="error" @click="updateStatus(record.id, 'rejected')" outlined>
                  Отклонено
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-carousel-item>
        </v-carousel>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      strains: [], // Список штаммов
      selectedStrain: null, // Выбранный штамм
      analyzeStrains: [],// Список записей анализа
      strainName: '',
    };
  },
  mounted() {
  if (this.$route.params.strainName) {
    this.selectedStrain = this.$refs.strainName;
    this.fetchAnalyzeStrains();
  }
},
props: {
    strainName: String,
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
        const response = await fetch(`http://localhost:8000/api/strain/analyze_records?name=${this.selectedStrain}`, {
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
        console.log(data);
        this.analyzeStrains = data; // Предполагается, что API возвращает массив объектов записей анализа
      } catch (error) {
        console.error('Ошибка при получении записей анализа:', error);
      }
    },
    // Обновляем статус записи
    async updateStatus(recordId, newStatus) {
      const token = localStorage.getItem('token');
      try {
        const response = await fetch(`http://localhost:8000/api/strain/update_status/${recordId}`, {
          method: 'PATCH', // Используйте PATCH, если вы частично обновляете ресурс
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
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
    },
    highlightText(text, words) {
      let highlighted = text;
      words.forEach((word, index) => {
        const color = index === 0 ? '#ffeb3b' : '#add8e6'; // Желтый для повторяющейся последовательности, голубой для спейсера
        const regex = new RegExp(word, 'gi');
        highlighted = highlighted.replace(regex, match => `<span style="background-color: ${color}; padding: 0 4px; border-radius: 4px;">${match}</span>`);
      });
      return highlighted;
    }
  }
};
</script>

<style scoped>
.custom-alert {
  background-color: rgba(0, 255, 255, 0.055) !important;
  color: #1bb40d !important;
}

.record-text {
  font-size: 16px;
  line-height: 1.5;
}

.record-text p {
  margin-bottom: 10px;
}

.record-text span {
  font-size: 14px;
  color: #666;
}

.custom-alert {
  font-size: 14px;
  margin-top: 10px;
}
</style>