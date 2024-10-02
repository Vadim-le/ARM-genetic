<template>
  <v-responsive class="border rounded">
    <v-app>
      <v-navigation-drawer app permanent>
        <v-list density="default">
          <v-list-item title="Штаммы"></v-list-item> 
          <v-divider></v-divider>
          <v-list-item link @click="updateItems('Стафилококк')" title="Стафилококк" value="stafilococus"></v-list-item>
          <v-list-item link @click="updateItems('Чума')" title="Чума" value="chuma"></v-list-item>
        </v-list>
      </v-navigation-drawer>
      <v-main class="flex-grow-1">
        <v-container fluid class="d-flex flex-column" style="max-width: 1600px; margin: auto;">  
          <v-btn color="primary" class="mt-4" @click="showForm = true">Добавить</v-btn>
          <v-dialog v-model="showForm" max-width="500">
            <v-card>
              <v-form ref="form" @submit.prevent="saveStrainData"> 
                <v-card-title class="text-h5">Добавить штамм</v-card-title>
                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="12">
                        <v-text-field
                          v-model="title"
                          label="Название штамма *"
                          required
                          :rules="[rules.required]"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12">
                        <v-select
                          v-model="year_of_allocation"
                          :items="years"
                          label="Год выделения *"
                          required
                          :rules="[rules.required]"
                        ></v-select>
                      </v-col>
                      <v-col cols="12" md="12">
                        <v-text-field
                          v-model="location"
                          label="Место выделения *"
                          required
                          :rules="[rules.required]"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="12">
                        <v-file-input
                          v-model="file"
                          label="Штамм *"
                          accept=".txt"
                          required
                          :rules="[rules.required]"
                        ></v-file-input>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" text @click="showForm = false">Отмена</v-btn>
                  <v-btn color="blue darken-1" text type="submit">Сохранить</v-btn>
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
        { title: 'ID', value: 'id' },
        { title: 'Name', value: 'name' },
        { title: 'Link', value: 'link' },
        { title: 'Place of Allocation', value: 'place_of_allocation' },
        { title: 'Year of Allocation', value: 'year_of_allocation' },
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
      rules: {
        required: (value) => !!value || 'Обязательное поле',
      },
    }
  },
  methods: {
    async updateItems(selectedItem) {
      console.log('Выбранный элемент:', selectedItem);
      await this.fetchData(selectedItem);
    },
    async fetchData(selectedItem) {
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
/* Добавьте стили для футера, если необходимо */
</style>