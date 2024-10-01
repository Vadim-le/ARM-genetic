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
      };
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
    },
  };
  </script>
  
  <style scoped>
  /* Добавьте стили для футера, если необходимо */
  </style>
  