<template>
    <v-app>
      <v-main class="d-flex align-top justify-center" style="height: 100vh; background-color:#FFFFFF;">
        <v-container>
          <v-row class="d-flex justify-center">
            <v-col cols="12" md="2" class="d-flex">
              <v-card class="custom-card d-flex flex-column" style="flex: 1;">
                <v-card-title>Аккаунт</v-card-title>
                <v-card-text>
                  <v-tabs v-model="tab" color="primary" direction="vertical">
                    <v-tab prepend-icon="mdi-account-cog" text="Аккаунт" value="option-1"></v-tab>
                    <v-tab prepend-icon="mdi-shield-lock-outline" text="Безопасность" value="option-2"></v-tab>
                  </v-tabs>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12" md="5" class="d-flex">
              <v-card class="custom-card d-flex flex-column" style="flex: 1;">
                <v-card-text>
                  <v-form v-if="isLogin">
                    <template v-if="tab === 'option-1'">
                      <v-card-title class="mt-n4 ml-n4">Настройки</v-card-title>
                      <div class="text-subtitle-2">Сохранение данных аккаунта доступно только после внесения изменений.</div>
                      <div class="text-subtitle-1 text-large-emphasis">Имя</div>
                      <v-text-field
                        v-model="name"
                        variant="outlined"
                        placeholder="Введите имя"
                        density="compact"
                        dense
                      ></v-text-field>
                      <div class="text-subtitle-1 text-large-emphasis">Фамилия</div>
                      <v-text-field
                        v-model="surname"
                        variant="outlined"
                        placeholder="Введите фамилию"
                        density="compact"
                      ></v-text-field>
                      <div class="text-subtitle-1 text-large-emphasis">Отчество</div>
                      <v-text-field
                        v-model="patronymic"
                        variant="outlined"
                        placeholder="Введите отчество"
                        density="compact"
                      ></v-text-field>
                     <div class="text-subtitle-1 text-large-emphasis">Пол</div>
                      <v-radio-group
                        v-model="inline"
                        inline
                      >
                        <v-radio
                          label="Мужской"
                          value="radio-1"
                        ></v-radio>
                        <v-radio
                          label="Женский"
                          value="radio-2"
                        ></v-radio>
                      </v-radio-group>
                      <v-divider class="border-opacity-100"></v-divider>
                      <v-card-title class="mt-n1 ml-n4">Почта</v-card-title>
                      <v-divider class="border-opacity-100"></v-divider>
                      <v-card-title class="mt-n1 ml-n4">Удалить аккаунт</v-card-title>
                      <div class="text-subtitle-1 text-large-emphasis">Удаление аккаунта — это необратимое действие. Все ваши данные будут удалены навсегда.</div>
                      <v-btn class="custom-button">Удалить аккаунт</v-btn>
                    </template>
                    <template v-if="tab === 'option-2'">
                      <v-card-title class="mt-n4 ml-n4">Пароль</v-card-title>
                      <v-text-field
                        v-model="password"
                        variant="outlined"
                        placeholder="Введите пароль"
                        clearable
                        label="Пароль"
                        prepend-inner-icon="mdi-lock"
                      ></v-text-field>
                      <v-text-field
                        v-model="password"
                        variant="outlined"
                        placeholder="Введите пароль"
                        clearable
                        label="Пароль"
                        prepend-inner-icon="mdi-lock"
                      ></v-text-field>
                    </template>
                  </v-form>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-main>
    </v-app>
  </template>
  
  <script>
  export default {
    data() {
      return {
        isLogin: true, // или false, в зависимости от вашей логики
        name: '',
        surname: '',
        patronymic: '',
        email: '',
        password: '',
        accessPoint: '',
        selectedDate: null, // Хранит выбранную дату
        showDatePicker: false, // Управляет видимостью календаря
        column: null,
        inline: null,
        tab: 'option-1' // Значение по умолчанию для выбранной вкладки
      };
    }
  };
  </script>

<style scoped>
.custom-card {
  background-color: #FFFFFF;
  border-radius: 30px;
  padding: 20px;
  border: 2px solid #3284e0;  
}

.text-subtitle-1 {
  margin-top: -15px; /* Задает отступы со всех сторон */
}

.text-subtitle-2 {
  margin-bottom: 20px;
  margin-top: 1px;
  color: #857f7f;
  font-size: 20px;
}

.custom-button {
  background-color: #d11d1ddc; /* Зеленый фон */
  color: #FDFFF5; /* Белый текст */
  border-radius: 20px; /* Закругленные углы */
  padding: 5px 5px; /* Отступы */
  font-size: 12px; /* Размер шрифта */
  transition: background-color 0.3s; /* Плавный переход */
  border: 2px solid #d11d1ddc;
  width: auto; /* Ширина кнопки 100% от родительского контейнера */
  max-width: 1000px; /* Максимальная ширина кнопки */
  height: auto; /* Высота автоматически подстраивается под содержимое */
}

/* Медиа-запрос для изменения размера шрифта на меньших экранах */
@media (max-width: 600px) {
  .custom-button {
    font-size: 12px; /* Уменьшаем размер шрифта на маленьких экранах */
    padding: 8px 16px; /* Уменьшаем отступы */
  }
}


</style>