<template>
    <v-app>
      <v-main class="d-flex align-top justify-center" style="height: 100vh; background-color:#3284e0;">
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
                        <div>
                          <div class="d-flex align-center">
                            <v-text-field
                              v-model="email"
                              readonly
                              density="compact"
                              variant="plain"
                              outlined
                            ></v-text-field>
                            <v-btn class="custom-button" @click="showDialog = true">изменить почту</v-btn>
                          </div>
                          <v-dialog v-model="showDialog" max-width="800px">
                            <v-card>
                              <v-card-title>
                                <span class="headline">Изменение почты</span>
                              </v-card-title>
                              <v-divider class="border-opacity-100"></v-divider>
                              <v-card-text>
                                <div class="text-subtitle-1 text-large-emphasis">Пароль</div>
                                <v-text-field
                                  v-model="newPassword"
                                  :append-inner-icon="visible ?  'mdi-eye' : 'mdi-eye-off'"
                                  :type="visible ? 'text' : 'password'"
                                  placeholder="Введите пароль"
                                  density="compact"

                                  variant="outlined"
                                  @click:append-inner="visible = !visible"
                                ></v-text-field>
                                <div class="text-subtitle-3 text-large-emphasis">Введите новый email. Вам придет письмо для подтверждения — перейдите по ссылке в письме.</div>
                                <div class="text-subtitle-1 text-large-emphasis">Новая почта</div>
                                <v-text-field
                                  v-model="name"
                                  variant="outlined"
                                  placeholder="Введите новую почту"
                                  density="compact"
                                  dense
                                ></v-text-field>
                                
                              </v-card-text>
                              <v-card-actions>
                                <v-btn @click="showDialog = false">Назад</v-btn>
                                <v-btn @click="updateEmail">Отправить письмо</v-btn>
                              </v-card-actions>
                            </v-card>
                          </v-dialog>
                        </div>
                      <v-divider class="border-opacity-100"></v-divider>
                      <v-card-title class="mt-n1 ml-n4">Удалить аккаунт</v-card-title>
                      <div class="text-subtitle-1 text-large-emphasis">Удаление аккаунта — это необратимое действие. Все ваши данные будут удалены навсегда.</div>
                      <v-btn class="custom-button" @click="showDialogDelete = true">Удалить аккаунт</v-btn>
                      <v-dialog v-model="showDialogDelete" max-width="800px">
                            <v-card>
                              <v-card-title>
                                <span class="headline">Внимание</span>
                              </v-card-title>
                              <v-divider class="border-opacity-100"></v-divider>
                              <div class="text-subtitle-3 text-large-emphasis">Удаление аккаунта приведет к безвозвратной потере всех данных. Вам придет письмо для подтверждения — перейдите по ссылке в письме.</div>                        
                              <v-card-text>
                                <div class="text-subtitle-1 text-large-emphasis">Пароль</div>
                                <v-text-field
                                  v-model="newPassword"
                                  :append-inner-icon="visible ?  'mdi-eye' : 'mdi-eye-off'"
                                  :type="visible ? 'text' : 'password'"
                                  placeholder="Введите пароль"
                                  density="compact"

                                  variant="outlined"
                                  @click:append-inner="visible = !visible"
                                ></v-text-field>
                                <div class="text-subtitle-3 text-large-emphasis">Введите новый email. Вам придет письмо для подтверждения — перейдите по ссылке в письме.</div>
                                Для удаления аккаунта введите фразу: Удалить аккаунт admin@example.org
                                <v-text-field
                                  v-model="name"
                                  variant="outlined"
                                  placeholder="Введите фразу"
                                  density="compact"
                                  dense
                                ></v-text-field>                               
                              </v-card-text>
                              <v-card-actions>
                                <v-btn @click="showDialogDelete = false">Назад</v-btn>
                                <v-btn @click="updateEmail">Отправить письмо</v-btn>
                              </v-card-actions>
                            </v-card>
                          </v-dialog>
                    </template>
                    <template v-if="tab === 'option-2'">
                      <v-card-title class="mt-n4 ml-n4">Пароль</v-card-title>
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
        visible: false,
        email: 'adm@example.org',
        showDialog: false,
        showDialogDelete: false,
        newEmail: '',
        password: '',
        accessPoint: '',
        newPassword: '',

        selectedDate: null, // Хранит выбранную дату
        column: null,
        inline: null,
        tab: 'option-1' // Значение по умолчанию для выбранной вкладки
      };
    },
    methods: {
    updateEmail() {
      // Логика для обновления почты
      this.email = this.newEmail; // Обновляем email
      this.showDialog = false; // Закрываем диалог
    },
  },
  };
  </script>

<style scoped>

.v-overlay {
  background-color: rgba(104, 103, 103, 0.9); /* Затемнение с 50% непрозрачностью */
}

.custom-card {
  background-color: #FFFFFF;
  border-radius: 30px;
  padding: 20px;
}

.text-subtitle-1 {
  margin-top: -15px; /* Задает отступы со всех сторон */
}

.text-subtitle-2 {
  margin-bottom: 20px;
  margin-top: 1px;
  color: #2c2c2c;
  font-size: 20px;
}

.text-subtitle-3{
  margin-bottom: 25px;
  margin-top: 0px;
  color: #1a0202c7;
  font-size: 16px;
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