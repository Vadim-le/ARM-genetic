<template>
  <v-app>
    <v-main class="d-flex align-center justify-center" style="height: 100vh; background-color:rgba(24,103,192);">
      <v-container>
        <v-row class="d-flex justify-center">
          <v-col cols="12" md="3">
            <v-card align="center" class="custom-card">
              <v-img
              src="@/images/login.png" 
              height="200" 
              width="200" 
              contain
              class="mb-2"
            ></v-img>
              <v-card-title>{{ isLogin ? 'Вход в аккаунт' : 'Регистрация' }}</v-card-title>
              <v-card-text>
                <v-form v-if="isLogin">
                  <v-text-field
                    v-model="login_email"
                    variant="outlined"
                    placeholder="Введите почту"
                    clearable 
                    rounded                  
                    label="Почта"
                    prepend-inner-icon="mdi-account"
                  ></v-text-field>
                  <v-text-field
                    v-model="login_password"
                    :append-inner-icon="visible ?  'mdi-eye' : 'mdi-eye-off'"
                    :type="visible ? 'text' : 'password'"
                    placeholder="Введите пароль"
                    prepend-inner-icon="mdi-lock-outline"
                    variant="outlined"
                    label="Пароль"
                    rounded
                    @click:append-inner="visible = !visible"
                  ></v-text-field>
                  <v-btn block @click="login" variant="Tonal" class="custom-button">Войти</v-btn>
                </v-form>
                <v-form v-else>
                  <v-text-field
                    v-model="newEmail"
                    variant="outlined"
                    placeholder="Введите почту"
                    clearable
                    rounded
                    label="Почта"
                    prepend-inner-icon="mdi-email"
                  ></v-text-field>
                  <v-text-field
                    v-model="newPassword"
                    :append-inner-icon="visible ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="visible ? 'text' : 'password'"
                    placeholder="Введите пароль"
                    variant="outlined"
                    label="Пароль"
                    rounded
                    @click:append-inner="visible = !visible"
                    prepend-inner-icon="mdi-lock"
                  ></v-text-field>
                  <v-btn block @click="register" class="custom-button">ЗАРЕГИСТРИРОВАТЬСЯ</v-btn>
                  <div class="alert-container">
                  <v-alert
                    v-if="errorMessage"
                    type="warning"
                    :text="errorMessage"
                    variant="outlined"
                    closable
                    prominent
                  >
                  </v-alert>
                </div>
                </v-form>
              </v-card-text>
              <v-card-text class="text-center">
                <a
                  class="text-blue text-decoration-none"
                  href="#"
                  @click.prevent="toggleForm"
                >
                  <span v-if="isLogin">Нет аккаунта? Зарегистрируйся</span>
                  <span v-else>Уже есть аккаунт? Войти</span>
                  <v-icon icon="mdi-chevron-right"></v-icon>
                </a>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
	import {
		jwtDecode
	}
	from "jwt-decode";
	export default {
		data() {
				return {
					repeatvisible: false,
					visible: false,
					isLogin: true,
					login_email: '',
					login_password: '',
					newName: '',
					newSurname: '',
					newEmail: '',
					newPassword: '',
					errorMessage: '',
					rules: {
						required: value => !!value || 'Required.',
						min: v => v.length >= 8 || 'Min 8 characters',
					},
				};
			},
			methods: {
				toggleForm() {
					this.isLogin = !this.isLogin;
				},
				async login() {
					try {
						const loginData = {
							email: this.login_email,
							password: this.login_password,
						};
						const response = await fetch('http://localhost:8000/api/auth/login', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
							},
							body: JSON.stringify(loginData),
						});
						if (!response.ok) {
							throw new Error('Login failed');
						}
						const data = await response.json();

            const decodedToken = jwtDecode(data.access_token);
						console.log('Decoded token:', decodedToken);

            const userRoles = decodedToken.roles; 
						console.log('User roles:', userRoles);
						const userId = decodedToken.sub;
						console.log('User ID:', userId);

            localStorage.setItem('token', data.access_token);
						this.$store.commit('setUserRole', userRoles);
						this.$store.commit('setUserId', userId);
						this.$router.push({
							name: 'Main'
						});
					} catch (error) {
						console.error('Error during login:', error);
					}
				},
				async register() {
					this.errorMessage = '';
					try {
						const registrationData = {
							email: this.newEmail,
							password: this.newPassword,
						};
						console.log("Register data:", registrationData);
						const response = await fetch('http://localhost:8000/api/auth/register', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
							},
							body: JSON.stringify(registrationData),
						});
						const data = await response.json();
						console.log('Data:', data);
						if (response.status === 422 && data.error === 'Validation Error') {
							this.errorMessage = data.messages.email[0]; 
							return; 
						}

            const decodedToken = jwtDecode(data.access_token);
						console.log('Decoded token:', decodedToken);

            const userRoles = decodedToken.roles;
						console.log('User roles:', userRoles);

						localStorage.setItem('token', data.access_token);
						this.$store.commit('setUserRole', userRoles);
						this.$router.push({
							name: 'Main'
						});
						console.log('Registration successful:', data);
					} catch (error) {
						console.error('Error during registration:', error);
						this.errorMessage = 'Произошла непредвиденная ошибка. Пожалуйста, попробуйте снова.'; // Общее сообщение об ошибке
					}
				}
			},
	};
</script>

<style scoped>

.custom-card {
  background-color: #FDFFF5;
  border-radius: 20px;
  padding: 10px;
}

.custom-button {
  background-color: #3284e0; 
  color: #FDFFF5; 
  border-radius: 20px;
  padding: 18px 20px;
  font-size: 16px; 
  transition: background-color 0.3s;
  border: 2px solid #3284e0;
}

.custom-button:hover {
  background-color: #FDFFF5;
  color: #3284e0;
  border-color: #3284e0;
}

.alert-container {
  padding-top: 10px;
}
</style>
