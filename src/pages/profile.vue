<template>
  <v-app>
    <v-main class="d-flex justify-center" style="height: 100vh; background-color:rgba(50, 132, 224, 0.6);">
      <v-container>
        <v-row class="d-flex flex-wrap justify-center">
          <v-col sm="12" md="8" class="ma-2"> <!-- Верхняя карточка -->
            <v-card>
              <v-row align="start">
                <v-col cols="auto">
                  <v-img
                    src="@/images/login.png" 
                    height="200" 
                    width="200" 
                    contain
                    class="ma-2"
                  ></v-img>
                </v-col>
                <v-col class="d-flex flex-column align-start"> <!-- Изменяем на flex-column для вертикального выравнивания -->
                  <v-card-title class="ma-2">{{ user_metadata }}</v-card-title>
                  <v-card-title class="ma-2">{{ user_age }}</v-card-title> <!-- Второй заголовок под первым -->
                </v-col>
              </v-row>
              <v-row align="start">
              </v-row>
            </v-card>
            <v-card-actions>
              <v-btn 
                :class="{'v-btn--active': showFirstGroup}" 
                @click="showFirstSet">Показать первую группу</v-btn>
              <v-btn 
                :class="{'v-btn--active': showSecondGroup}" 
                @click="showSecondSet">Показать вторую группу</v-btn>
            </v-card-actions>
          </v-col>

          <!-- Карточки первой группы -->
          <template v-if="showFirstGroup">
            <v-col sm="6" md="4"> 
              <v-card>
                <v-img
                  src="@/images/login.png" 
                  height="200" 
                  width="200" 
                  contain
                  class="mb-2"
                ></v-img>
                <v-row class="ml-4">
                  <v-col cols="">
                    <span>Ученая степень:</span>
                  </v-col>
                  <v-col cols="6">
                    <span>{{ academicDegree }}</span>
                  </v-col>
                </v-row>

                <v-row class="ml-4">
                  <v-col cols="6">
                    <span>Ученое звание:</span>
                  </v-col>
                  <v-col cols="6">
                    <span>{{ academicTitle }}</span>
                  </v-col>
                </v-row>
                <v-card-title>Первая карточка</v-card-title>
              </v-card>
            </v-col>

            <v-col sm="6" md="4"> 
              <v-card class="custom-card">
                <v-img
                  src="@/images/login.png" 
                  height="200" 
                  width="200" 
                  contain
                  class="mb-2"
                ></v-img>
                <v-card-title>Вторая карточка</v-card-title>
              </v-card>
            </v-col>
            <v-col sm="12" md="8"> 
              <v-card class="custom-card">
                <v-card-title>Образование</v-card-title>
                <v-divider></v-divider>
                <v-list>
                  <v-list-item-group>
                    <v-list-item v-for="(record, index) in records" :key="index">
                      <v-list-item-content>
                        <v-list-item-subtitle>Учебное заведение: {{ record.educational_institute }}</v-list-item-subtitle>
                        <v-list-item-subtitle>Образование: {{ record.educational_level }}</v-list-item-subtitle>
                        <v-list-item-subtitle>Специальность: {{ record.specialization }}</v-list-item-subtitle>
                        <v-list-item-subtitle>Квалификация: {{ record.qualification }}</v-list-item-subtitle>
                        <v-list-item-subtitle>Годы обучения: {{ record.years }}</v-list-item-subtitle>
                        <v-divider></v-divider>
                      </v-list-item-content>
                    </v-list-item>
                  </v-list-item-group>

                </v-list>
              </v-card>
            </v-col>
          </template>

          <!-- Карточки второй группы -->
          <template v-if="showSecondGroup">
            <v-col sm="12" md="6">
              <v-card>
                <v-img
                  src="@/images/login.png" 
                  height="200" 
                  width="200" 
                  contain
                  class="mb-2"
                ></v-img>
                <v-card-title>Шестая карточка</v-card-title>
              </v-card>
            </v-col>
          </template>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  data() {
    return {
      showFirstGroup: false,
      showSecondGroup: false,

      user_metadata: '',
      user_age: '',
      academicDegree: '',
      academicTitle: '',
      // TODO: изменить после связки с сервером
      records: []
    }

  },
  async mounted() {
  const token = localStorage.getItem('token'); // Получаем токен из localStorage
  await this.fetchData(token); // Вызываем метод fetchData с токеном
},
methods: {
  async fetchData(token) {
  console.log('Запрос данных с токеном:', token);
  try {
    const response = await fetch(`http://localhost:8000/api/auth`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`, // Добавляем токен в заголовок
      },
    });
    
    if (!response.ok) {
      throw new Error('Ошибка сети: ' + response.statusText);
    }

    const result = await response.json(); // Получаем ответ от сервера
    console.log('Данные профиля пользователя:', result);

    // Теперь обращаемся к полям внутри result.data
    const data = result.data; // Извлекаем объект data

    // Заполняем поля метаданных
    this.user_metadata = ` ${data.metadata.last_name}  ${data.metadata.first_name} ${data.metadata.patronymic}`;
    
    // Заполняем массив записей об образовании
    this.records = data.education.map(education => ({
      educational_institute: education.educational_institute || '',
      educational_level: education.educational_level || '',
      specialization: education.specialization || '',
      qualification: education.qualification || '',
      years: `${education.start_year} - ${education.end_year}` // Форматируем годы
    }));

    console.log('Полученные данные:', data);
  } catch (error) {
    console.error('Ошибка при получении данных:', error);
  }
},

    showFirstSet() {
      this.showFirstGroup = true;
      this.showSecondGroup = false;
    },
    showSecondSet() {
      this.showFirstGroup = false;
      this.showSecondGroup = true;
    },
  },
};
</script>

<style>
.v-btn--active {
  background-color: #1976d2; 
  color: white; 
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
}

.custom-card {
  background-color: #FDFFF5;
  border-radius: 20px;
  padding: 10px;
}
</style>
