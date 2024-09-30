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
                      <v-tabs v-model="tab_inside" color="primary">
                        <v-tab prepend-icon="mdi-account-cog" text="Основное" value="main"></v-tab>
                        <v-tab prepend-icon="mdi-shield-lock-outline" text="Образование" value="education"></v-tab>
                        <v-tab prepend-icon="mdi-shield-lock-outline" text="Контакты" value="contacts"></v-tab>
                      </v-tabs>
                      <template v-if="tab_inside === 'main'">
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
                          <template v-if="tab_inside === 'education'">
  <div class="text-subtitle-2">Сохранение данных аккаунта доступно только после внесения изменений.</div>

  <div class="text-subtitle-1 text-large-emphasis">Ученая степень</div>
  <v-text-field
    v-model="academicDegree"
    @input="markAsChanged"
    variant="outlined"
    placeholder="Введите ученую степень"
    density="compact"
  ></v-text-field>

  <div class="text-subtitle-1 text-large-emphasis">Ученое звание</div>
  <v-text-field
    v-model="academicTitle"
    @input="markAsChanged"
    variant="outlined"
    placeholder="Введите ученое звание"
    density="compact"
  ></v-text-field>

  <v-container>
    <v-row>
      <v-col v-for="(record, index) in educationRecords" :key="index" cols="12" md="6">
        <v-card @click="openDialogEducation(index)" class="mb-3">
          <v-card-title>
            <span class="text-h6">Запись {{ index + 1 }}</span>
            <v-spacer></v-spacer>
            <v-icon @click.stop="removeEducationRecord(index)" color="red">mdi-delete</v-icon>
          </v-card-title>
        </v-card>
      </v-col>
    </v-row>

    <v-btn @click="addEducationRecord" color="green">Добавить запись</v-btn>
  </v-container>

  <!-- Кнопка сохранения данных -->
  <v-btn v-if="isChanged" @click="saveData" color="blue">Сохранить изменения</v-btn>

  <v-dialog v-model="dialog" max-width="600px">
    <v-card>
      <v-card-title>
        <span class="text-h6">Детали записи об образовании</span>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12">
            <div class="text-subtitle-1 text-large-emphasis">Учебное заведение</div>
            <v-text-field
              v-model="selectedRecord.educational_institute"
              variant="outlined"
              @input="markAsChanged"
              placeholder="Введите учебное заведение"
              density="compact"
              dense
            ></v-text-field>
          </v-col>

          <v-col cols="12">
            <div class="text-subtitle-1 text-large-emphasis">Уровень образования</div>
            <v-text-field
              v-model="selectedRecord.educational_level"
              variant="outlined"
              @input="markAsChanged"
              placeholder="Введите уровень образования"
              density="compact"
              dense
            ></v-text-field>
          </v-col>

          <v-col cols="12">
            <div class="text-subtitle-1 text-large-emphasis">Специальность</div>
            <v-text-field
              v-model="selectedRecord.specialization"
              variant="outlined"
              @input="markAsChanged"
              placeholder="Введите специальность"
              density="compact"
              dense
            ></v-text-field>
          </v-col>

          <v-col cols="12">
            <div class="text-subtitle-1 text-large-emphasis">Квалификация</div>
            <v-text-field
              v-model="selectedRecord.qualification"
              variant="outlined"
              @input="markAsChanged"
              placeholder="Введите квалификацию"
              density="compact"
              dense
            ></v-text-field>
            <input type="hidden" v-model="selectedRecord.id" />
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-btn @click="dialog = false">Закрыть</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<template v-if="tab_inside === 'contacts'">
  <div class="text-subtitle-2">Сохранение данных аккаунта доступно только после внесения изменений.</div>

  <div class="text-subtitle-1 text-large-emphasis">Почта</div>
  <v-text-field
    v-model="contact_email"
    @input="markAsChanged"
    variant="outlined"
    placeholder="Введите почту"
    density="compact"
  ></v-text-field>

  <v-container>
    <v-row>
      <v-col v-for="(record, index) in contactRecords" :key="index" cols="12" md="6">
        <v-card @click="openDialogContact(index)" class="mb-3">
          <v-card-title>
            <span class="text-h6">Запись {{ index + 1 }}</span>
            <v-spacer></v-spacer>
            <v-icon @click.stop="removeContactRecord(index)" color="red">mdi-delete</v-icon>
          </v-card-title>
        </v-card>
      </v-col>
    </v-row>

    <v-btn @click="addContactRecord" color="green">Добавить запись</v-btn>
  </v-container>

  <!-- Кнопка сохранения данных -->
  <v-btn v-if="isChanged" @click="saveData" color="blue">Сохранить изменения</v-btn>

  <v-dialog v-model="dialog" max-width="600px">
    <v-card>
      <v-card-title>
        <span class="text-h6">Запись о журанле</span>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12">
            <div class="text-subtitle-1 text-large-emphasis">Название журнала</div>
            <v-text-field
              v-model="selectedRecord.journal_title"
              variant="outlined"
              @input="markAsChanged"
              placeholder="Введите название журнала"
              density="compact"
              dense
            ></v-text-field>
          </v-col>

          <v-col cols="12">
            <div class="text-subtitle-1 text-large-emphasis">Ссылка на профиль в журнале</div>
            <v-text-field
              v-model="selectedRecord.journal_link"
              variant="outlined"
              @input="markAsChanged"
              placeholder="Введите ссылку на журнал"
              density="compact"
              dense
            ></v-text-field>
            <input type="hidden" v-model="selectedRecord.id" />
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-btn @click="dialog = false">Закрыть</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
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
        educationRecords: [
        {
          educational_institute: '',
          educational_level: '',
          specialization: '',
          qualification: '',
          id: '',
          
        }
        ],
        contactRecords: [
        {
          journal_title: '',
          journal_link: '',         
        }
        ],

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
        academicTitle: '',
        contact_email: '',


        selectedDate: null, // Хранит выбранную дату
        column: null,
        inline: null,
        tab: 'option-1', // Значение по умолчанию для выбранной вкладки
        tab_inside:'main',

        dialog: false,
      selectedRecord: {},
      isChanged: false // Переменная для отслеживания изменений

      };
    },
    async mounted() {
      const token = localStorage.getItem('token'); // Получаем токен из localStorage
      await this.fetchData(token);
    },
    methods: {
      markAsChanged() {
      this.isChanged = true; // Устанавливаем флаг изменений
    },
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
    this.name = data.metadata.first_name || ''; // Устанавливаем имя
    this.surname = data.metadata.last_name || ''; // Устанавливаем фамилию
    this.patronymic = data.metadata.patronymic || ''; // Устанавливаем отчество
    this.academicTitle = data.metadata.academic_title || ''; 
    this.academicDegree = data.metadata.academic_degree || ''; 
    this.contact_email = data.metadata.contact_email || ''; 

    // Заполняем массив educationRecords
    this.educationRecords = data.education.map(record => ({
      id: record.id,
      educational_institute: record.educational_institute || '',
      educational_level: record.educational_level || '',
      specialization: record.specialization || '',
      qualification: record.qualification || '',
    })) || []; // Предполагается, что данные приходят в этом формате
    this.contactRecords = data.bibliografia.map(record => ({
      id: record.id,
      journal_title: record.journal_title,
      journal_link: record.journal_link,
    })) || [];

    console.log('Полученные данные:', data);
  } catch (error) {
    console.error('Ошибка при получении данных:', error);
  }
},


async saveData() {
  const userId = this.$store.getters.userId;
  console.log('User ID:', userId);
  const token = localStorage.getItem('token');

  // Логика для сохранения данных на сервер
  const dataToSave = {
    academic_title: this.academicTitle,
    last_name: this.surname,
    first_name: this.name,
    patronymic: this.patronymic,
    academic_degree: this.academicDegree,
    contact_email: this.contact_email,
    education: this.educationRecords.map(record => ({
      id: record.id, // Включаем id для обновления
      educational_institute: record.educational_institute,
      educational_level: record.educational_level,
      specialization: record.specialization,
      qualification: record.qualification,
    })),
    bibliografia: this.contactRecords.map(record => ({
      id: record.id, // Включаем id для обновления
      journal_title: record.journal_title,
      journal_link: record.journal_link,
    })),
  };

  try {
    const response = await fetch(`http://localhost:8000/api/users/${userId}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(dataToSave),
    });

    if (!response.ok) {
      throw new Error('Ошибка при сохранении данных');
    }

    const result = await response.json();
    console.log('Данные успешно сохранены:', result);
    this.isChanged = false; // Сбрасываем флаг изменений после успешного сохранения
  } catch (error) {
    console.error('Ошибка при сохранении данных:', error);
  }
},


    updateEmail() {
      // Логика для обновления почты
      this.email = this.newEmail; // Обновляем email
      this.showDialog = false; // Закрываем диалог
    },
    addEducationRecord() {
      this.educationRecords.push({
        educational_institute: '',
        educational_level: '',
        specialization: '',
        qualification: '',
        id: '', // Для создания новых записей id необходимо установить пустую строку  
      });
    },
    addContactRecord() {
      this.contactRecords.push({
        journal_title: '',
        journal_link: '',
        id: '',
      });
    },
    removeEducationRecord(index) {
      this.educationRecords.splice(index, 1);
    },
    removeContactRecord(index) {
      this.contactRecords.splice(index, 1);
    },
    openDialogEducation(index) {
      this.selectedRecord = this.educationRecords[index];
      this.dialog = true; // Открываем диалог
    },

    openDialogContact(index) {
      this.selectedRecord = this.contactRecords[index];
      this.dialog = true; // Открываем диалог
    }
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