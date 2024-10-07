<template>
    <v-container>
      <v-row justify="center" align="center" class="my-5">
        <v-col cols="12" md="8">
          <v-card class="elevation-12 rounded-lg" style="width: 100%;">
            <v-toolbar color="primary" dark flat>
              <v-toolbar-title class="text-h4 font-weight-bold">
                Поиск повторов и спэйсеров
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
                    Начать поиск
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row justify="center" class="my-5">
        <v-col cols="12" md="8">
          <v-card class="elevation-12 rounded-lg">
            <v-card-title class="text-h6">
              Результаты поиска
            </v-card-title>
            <v-card-text>
              <p>Найдено повторов: {{ numRepeats }}</p>
              <p>Найдено спейсеров: {{ numSpacers }}</p>
            </v-card-text>
          </v-card>
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
        numRepeats: 0,
        numSpacers: 0,
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
          const response = await fetch('http://localhost:8000/api/strain/spacer_search', {
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
        console.log(this.selectedStrain);
        if (!this.selectedStrain) return;
        try {
          const response = await fetch(`http://localhost:8000/api/strain/find-repeats?strain_name=${this.selectedStrain}`, {
            method: 'POST',
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
          this.analyzeStrains = data.spacers_info.map(item => ({
            repeat: item.repeat,
            repeat_positions: item.repeat_positions.map(position => `${position[0]} - ${position[1]}`).join(', '),
            spacer: item.spacer,
            spacer_positions: item.spacer_positions.join(', '),
          }));
          this.numRepeats = data.num_repeats || 0;
          this.numSpacers = data.num_spacers || 0;
        } catch (error) {
          console.error('Ошибка при получении записей анализа:', error);
          this.numRepeats = 0;
          this.numSpacers = 0;
        }
      },
    },
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