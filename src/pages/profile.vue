<template>
	<v-app>
		<v-main class="d-flex justify-center" style="background-color:#1867c0;">
			<v-container>
				<v-row class="d-flex flex-wrap justify-center">
					<v-btn icon @click="goBack" class="mt-3">
						<v-icon>mdi-arrow-left</v-icon>
					</v-btn>
					<v-col sm="12" md="8" class="ma-2">
						<!-- Верхняя карточка -->
						<v-card class="custom-card">
							<v-row align="start">
								<v-col cols="auto">
									<v-img src="@/images/login.png" height="200" width="200" contain class="ma-2"></v-img>
								</v-col>
								<v-col class="d-flex flex-column align-start">
									<!-- Изменяем на flex-column для вертикального выравнивания -->
									<v-card-title class="mr-2 ml-2 mt-2">ФИО: {{ user_metadata }}</v-card-title>
									<v-card-title class="mr-2 ml-2">Возраст: {{user_age }}</v-card-title>
									<!-- Второй заголовок под первым -->
									<v-card-title class="mr-2 ml-2">Ученая степень: {{academicDegree }}</v-card-title>
									<v-card-title class="mr-2 ml-2">Ученая звание: {{academicTitle }}</v-card-title>
								</v-col>
							</v-row>
						</v-card>
						<v-card class="custom-card mt-10">
							<v-card-title>Образование</v-card-title>
							<v-divider></v-divider>
							<v-list>
								<v-list-item-group>
									<v-list-item v-for="(record, index) in education_records" :key="index">
										<v-list-item-content>
											<v-list-item-subtitle class="text-h6 font-weight-bold mb-3">Учебное заведение: {{ record.educational_institute }}</v-list-item-subtitle>
											<v-list-item-subtitle class="text-subtitle-1 font-weight-medium mb-2">Образование: {{ record.educational_level }}</v-list-item-subtitle>
											<v-list-item-subtitle class="text-subtitle-1 font-weight-medium mb-2">Специальность: {{ record.specialization }}</v-list-item-subtitle>
											<v-list-item-subtitle class="text-subtitle-1 font-weight-medium mb-2">Квалификация: {{ record.qualification }}</v-list-item-subtitle>
											<v-list-item-subtitle class="text-subtitle-1 font-weight-medium mb-2">Годы обучения: {{ record.years }}</v-list-item-subtitle>
											<v-divider></v-divider>
										</v-list-item-content>
									</v-list-item>
								</v-list-item-group>
							</v-list>
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
					user_metadata: '',
					user_age: '',
					academicDegree: '',
					academicTitle: '',
					education_records: [],
				}
			},
			async mounted() {
				const token = localStorage.getItem('token');
				await this.fetchData(token);
			},
			methods: {
				goBack() {
					this.$router.go(-1);
				},
				calculateAge(birthday) {
					const birthDate = new Date(birthday);
					const today = new Date();
					let age = today.getFullYear() - birthDate.getFullYear();
					const monthDifference = today.getMonth() - birthDate.getMonth();
					if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
						age--;
					}
					return age;
				},
				async fetchData(token) {
					console.log('Запрос данных с токеном:', token);
					try {
						const response = await fetch(`http://localhost:8000/api/auth`, {
							method: 'GET',
							headers: {
								'Authorization': `Bearer ${token}`,
							},
						});
						if (!response.ok) {
							throw new Error('Ошибка сети: ' + response.statusText);
						}
						const result = await response.json();
						console.log('Данные профиля пользователя:', result);
						const data = result.data;
						this.user_metadata = ` ${data.metadata.last_name}  ${data.metadata.first_name} ${data.metadata.patronymic}`;
						this.user_age = this.calculateAge(data.metadata.birthday);
						this.academicDegree = data.metadata.academic_degree;
						this.academicTitle = data.metadata.academic_title;
						this.education_records = data.education.map(education => ({
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
  background-color: #ffffff;
  border-radius: 10px;
  padding: 10px;
}
</style>
