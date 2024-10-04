<template>
  <v-responsive class="border rounded">
    <v-app>
      <v-main class="flex-grow-1">
        <v-container
          fluid
          class="d-flex flex-column"
          :class="{ 'blur-background': showForm }"
          style="max-width: 1600px; margin: auto;"
        >
          <v-btn color="primary" class="mt-4" @click="showForm = true">Добавить</v-btn>
          <v-dialog v-model="showForm" max-width="800">
            <v-card class="rounded-xl">
              <v-form ref="form" @submit.prevent="saveStrainData">
                <v-card-title class="text-h5 d-flex justify-center">Добавить штамм</v-card-title>
                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="12">
                        <v-text-field
                          v-model="title"
                          label="Название штамма *"
                          required
                          :rules="[rules.required]"
                          variant="outlined"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12">
                        <v-text-field
                          v-model="location"
                          label="Место выделения *"
                          required
                          :rules="[rules.required]"
                          variant="outlined"
                        ></v-text-field>
                      </v-col>
                      <v-row justify="center" class="w-100">
                        <v-col cols="12" md="6" class="d-flex justify-center">
                          <v-select
                            variant="outlined"
                            v-model="year_of_allocation"
                            :items="years"
                            label="Год выделения *"
                            required
                            :rules="[rules.required]"
                          ></v-select>
                        </v-col>
                      </v-row>
                      <v-col cols="12" md="12">
                        <v-card class="dashed-border pa-0" variant="outlined">
                          <div class="d-flex justify-center">
                            <v-file-input
                              v-model="file"
                              label="Штамм *"
                              accept=".txt"
                              required
                              :rules="[rules.required]"
                              variant="solo"
                              prepend-icon=""
                              class="file-input-width"
                            ></v-file-input>
                          </div>
                          <v-card-text class="d-flex justify-center align-center" style="height: 150px;">
                            <v-icon class="custom-icon-size" color="blue lighten-1">mdi-file-upload-outline</v-icon>
                          </v-card-text>
                        </v-card>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" text type="submit">Сохранить</v-btn>
                  <v-btn color="red darken-1" text @click="showForm = false">Отмена</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-form>
            </v-card>
          </v-dialog>
          <v-data-table
            :headers="headers"
            :items="items"
            :search="search"
            :items-per-page="5"
            :pagination.sync="pagination"
          >
            <template v-slot:top>
              <v-text-field
                v-model="search"
                label="Search"
                density="compact"
              ></v-text-field>
            </template>
            <template v-slot:item.name="{ item }">
              <a @click.prevent="goToItemDetail(item.id)" style="cursor: pointer; color: blue; text-decoration: underline;">{{ item.name }}</a>
            </template>
          </v-data-table>
        </v-container>
        <!-- Добавляем v-snackbar для уведомлений -->
        <v-snackbar v-model="snackbar" :timeout="snackbarTimeout">
          {{ snackbarText }}
 <template v-slot:actions>
            <v-btn color="pink" variant="text" @click="snackbar = false">Закрыть</v-btn>
          </template>
        </v-snackbar>
      </v-main>
    </v-app>
  </v-responsive>
</template>

<script>
export default {
  data() {
    return {
      search: '',
      years: this.generateYears(),
      headers: [
        { title: 'Название', value: 'name' },
        { title: 'Место выделения', value: 'place_of_allocation' },
        { title: 'Год выделения', value: 'year_of_allocation' },
      ],
      items: [],
      pagination: {
        page: 1,
        rowsPerPage: 5,
      },
      showForm: false,
      title: '',
      year_of_allocation: '',
      location: '',
      file: null,
      snackbar: false, // Состояние snackbar
      snackbarText: '', // Текст snackbar
      snackbarTimeout: 5000, // Время показа snackbar в миллисекундах
      rules: {
        required: (value) => !!value || 'Обязательное поле',
      },
    }
  },
  async mounted() {
        await this.fetchData();
    },
  methods: {
    async fetchData() {
      const selectedItem = 'Стафилококк';
      console.log('Запрос данных для элемента:', selectedItem);
      try {
        const response = await fetch(`http://localhost:8000/api/strain?type_of_bacteria=${selectedItem}`, {
          method: 'GET',
        });
        
        if (!response.ok) {
          throw new Error('Ошибка сети: ' + response.statusText);
        }

        const data = await response.json();
        this.items = data.data; // Предполагается, что данные находятся в data.data
        console.log('Полученные данные:', this.items);
      } catch (error) {
        console.error('Ошибка при получении данных:', error);
      }
    },
    goToItemDetail(id) {
      console.log('Переход к детальному просмотру элемента:', id);
      this.$router.push({ name: 'SequenceManagement', params: { id } });
    },
    async saveStrainData() {
      const token = localStorage.getItem('token');
      // Собираем данные из формы
      const strainData = {
        name: this.title,
        place_of_allocation: this.location,
        year_of_allocation: this.year_of_allocation,
        file: this.file,
        type_of_bacteria: 'Стафилококк', // Получаем ID из параметров роута
      };

      try {
        // Создаем FormData для отправки файла
        const formData = new FormData();
        for (const key in strainData) {
          formData.append(key, strainData[key]); 
        }

        // Отправляем POST запрос
        const response = await fetch('http://localhost:8000/api/strain', {
          headers: {
                        'Authorization': `Bearer ${token}`, // Добавляем токен в заголовок
                    },
          method: 'POST',
          body: formData
        });

        if (!response.ok) {
          throw new Error(`Ошибка сети: ${response.statusText}`);
        }

        const data = await response.json();
        console.log('Данные успешно сохранены:', data);
        await this.fetchData();

        // Показываем уведомление и закрываем форму
        this.snackbarText = 'Данные успешно сохранены!';
        this.snackbar = true;
        this.showForm = false; // Закрываем форму
        // Дополнительные действия после успешного сохранения:
        // - Очистить форму
        // - Показать сообщение об успехе
        // - Обновить список штаммов

      } catch (error) {
        console.error('Ошибка при сохранении данных:', error);
        // Обработка ошибки: 
        // - Показать сообщение об ошибке пользователю
      }
    },
    generateYears() {
      const currentYear = new Date().getFullYear();
      const years = [];
      for (let year = currentYear; year >= 1980; year--) {
        years.push(year);
      }
      return years;
    },
  },
}
</script>

<style scoped>
.dashed-border {
  border: 2px dashed #ccc;
  border-radius: 48px;
}
.file-input-width {
  max-width: 300px; /* Ширина по умолчанию */
}

@media (max-width: 600px) {
  .file-input-width {
    max-width: 100%; /* Полная ширина на экранах до 600px */
  }
}

@media (max-width: 480px) {
  .file-input-width {
    max-width: 70%; /* 90% ширины на экранах до 480px */
  }
}
.custom-icon-size {
  font-size: 64px; /* Установите желаемый размер */
}
/* Добавляем класс для эффекта размытия */
.blur-background {
  filter: blur(10px);
  pointer-events: none;
  user-select: none;
}

/* Чтобы диалоговое окно не было размыто, добавляем исключение */
.blur-background .v-dialog {
  filter: none;
  pointer-events: auto;
  user-select: auto;
}
</style>