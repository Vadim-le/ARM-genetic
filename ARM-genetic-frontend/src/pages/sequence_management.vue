<template>
	<v-app>
		<v-main>
			<v-container fluid class="d-flex" style="max-width: 1400px; margin: auto;">
				<v-col cols="auto" class="d-flex flex-column align-start back-button">
					<v-btn icon @click="$router.go(-1)" class="mb-4">
						<v-icon>mdi-arrow-left</v-icon>
					</v-btn>
				</v-col>
				<v-col class="d-flex flex-column">
					<v-card class="my-4">
						<v-card-title>Общая информация о штамме</v-card-title>
						<v-card-text>
							<v-row>
								<v-col cols="12" sm="6" md="3">
									<v-text-field label="Name" v-model="name" :disabled="!isEditing" outlined dense></v-text-field>
								</v-col>
								<v-col cols="12" sm="6" md="3">
									<template v-if="isEditing">
										<v-select label="Тип бактерии" v-model="type_of_bacteria" :items="bacteries" :disabled="!isEditing" return-object outlined dense></v-select>
									</template>
									<template v-else>
										<v-text-field label="Тип бактерии" v-model="type_of_bacteria" :disabled="!isEditing" outlined dense></v-text-field>
									</template>
								</v-col>
								<v-col cols="12" sm="6" md="3">
									<v-autocomplete v-model="place_of_allocation" :items="countries" :search-input.sync="search" :disabled="!isEditing" label="Place Of Allocation" outlined dense></v-autocomplete>
								</v-col>
								<v-col cols="12" sm="6" md="3">
									<template v-if="isEditing">
										<v-select label="Год выделения штамма" v-model="year_of_allocation" :items="years" :disabled="!isEditing" return-object outlined dense></v-select>
									</template>
									<template v-else>
										<v-text-field label="Год выделения штамма" v-model="year_of_allocation" :disabled="!isEditing" outlined dense></v-text-field>
									</template>
								</v-col>
							</v-row>
						</v-card-text>
					</v-card>
					<v-card class="my-4">
						<v-card-title>Полная последовательность штамма</v-card-title>
						<v-card-text>
							<v-textarea v-model="sequence" :readonly="!isEditing" outlined dense rows="4"></v-textarea>
						</v-card-text>
					</v-card>
					<v-card class="my-4">
						<v-card-title>Спэйсеры и повторяющиеся последовательности</v-card-title>
						<v-card-text>
							<v-data-table :headers="headers" :items="analyzeStrains" :items-per-page="5" class="elevation-1" dense>
							</v-data-table>
						</v-card-text>
					</v-card>
					<v-row justify="center">
						<v-col cols="12" sm="6" md="4" class="d-flex justify-center">
							<v-btn @click="toggleEdit" v-if="isAdmin" color="primary" outlined class="w-100">
								{{ isEditing ? 'Сохранить изменения' : 'Редактировать' }}
							</v-btn>
						</v-col>
					</v-row>
				</v-col>
			</v-container>
		</v-main>
	</v-app>
</template>

<script>
	import {
		mapGetters
	}
	from 'vuex';
	export default {
		data() {
				return {
					id: '',
					name: '',
					place_of_allocation: '',
					sequence: '',
					year_of_allocation: '',
					type_of_bacteria: '',
					isEditing: false,
					link: '',
					headers: [{
						title: 'Повторяющиеся последовательности',
						key: 'repeat_sequence',
						align: 'start',
						sortable: false
					}, {
						title: 'Индексы повторяющихся последовательностей',
						key: 'repeat_positions',
						align: 'end',
						sortable: false
					}, {
						title: 'Спэйсеры',
						key: 'spacer_sequence',
						align: 'end',
						sortable: false
					}, {
						title: 'Индексы спэйсеров',
						key: 'spacer_positions',
						align: 'end',
						sortable: false
					}, ],
					years: this.generateYears(),
					bacteries: ['Стафилококк', 'Чума'],
					countries: ['Афганистан', 'Албания', 'Алжир', 'Андорра', 'Ангола', 'Антигуа и Барбуда', 'Аргентина', 'Армения', 'Австралия', 'Австрия', 'Азербайджан', 'Багамские Острова', 'Бахрейн', 'Бангладеш', 'Барбадос', 'Беларусь', 'Бельгия', 'Белиз', 'Бенин', 'Бутан', 'Боливия', 'Босния и Герцеговина', 'Ботсвана', 'Бразилия', 'Бруней', 'Болгария', 'Буркина-Фасо', 'Бурунди', 'Камбоджа', 'Камерун', 'Канада', 'Центральноафриканская Республика', 'Чад', 'Чили', 'Китай', 'Колумбия', 'Коморы', 'Конго (Браззавиль)', 'Конго (Киншаса)', 'Острова Кука', 'Коста-Рика', 'Кот-д-Ивуар', 'Хорватия', 'Куба', 'Кипр', 'Чехия', 'Дания', 'Джибути', 'Доминика', 'Доминиканская Республика', 'Эквадор', 'Египет', 'Сальвадор', 'Экваториальная Гвинея', 'Эритрея', 'Эстония', 'Эфиопия', 'Фиджи', 'Финляндия', 'Франция', 'Габон', 'Гамбия', 'Грузия', 'Германия', 'Гана', 'Греция', 'Гренада', 'Гватемала', 'Гвинея', 'Гвинея-Бисау', 'Гайана', 'Гаити', 'Гондурас', 'Венгрия', 'Исландия', 'Индия', 'Индонезия', 'Иран', 'Ирак', 'Ирландия', 'Израиль', 'Италия', 'Ямайка', 'Япония', 'Иордания', 'Казахстан', 'Кения', 'Кирибати', 'Северная Корея', 'Южная Корея', 'Косово', 'Кувейт', 'Кыргызстан', 'Лаос', 'Латвия', 'Ливан', 'Лесото', 'Либерия', 'Ливия', 'Лихтенштейн', 'Литва', 'Люксембург', 'Македония', 'Мадагаскар', 'Малави', 'Малайзия', 'Мальдивы', 'Мали', 'Мальта', 'Маршалловы Острова', 'Мавритания', 'Маврикий', 'Мексика', 'Микронезия', 'Молдова', 'Монако', 'Монголия', 'Черногория', 'Марокко', 'Мозамбик', 'Мьянма', 'Намибия', 'Науру', 'Непал', 'Нидерланды', 'Новая Зеландия', 'Никарагуа', 'Нигер', 'Нигерия', 'Норвегия', 'Оман', 'Пакистан', 'Палау', 'Панама', 'Папуа-Новая Гвинея', 'Парагвай', 'Перу', 'Филиппины', 'Польша', 'Португалия', 'Катар', 'Румыния', 'Россия', 'Руанда', 'Сент-Китс и Невис', 'Сент-Люсия', 'Сент-Винсент и Гренадины', 'Самоа', 'Сан-Марино', 'Сан-Томе и Принсипи', 'Саудовская Аравия', 'Сенегал', 'Сербия', 'Сейшельские Острова', 'Сьерра-Леоне', 'Сингапур', 'Синт-Мартен', 'Словакия', 'Словения', 'Соломоновы Острова', 'Сомали', 'Южная Африка', 'Южный Судан', 'Испания', 'Шри-Ланка', 'Судан', 'Суринам', 'Свазиленд', 'Швеция', 'Швейцария', 'Сирия', 'Таджикистан', 'Танзания', 'Таиланд', 'Восточный Тимор', 'Того', 'Тонга', 'Тринидад и Тобаго', 'Тунис', 'Турция', 'Туркменистан', 'Тувалу', 'Уганда', 'Украина', 'Объединенные Арабские Эмираты', 'Великобритания', 'Соединенные Штаты', 'Уругвай', 'Узбекистан', 'Вануату', 'Ватикан', 'Венесуэла', 'Вьетнам', 'Йемен', 'Замбия', 'Зимбабве'],
					analyzeStrains: [],
				}
			},
			watch: {
				search(val) {
					if (val) {
						this.countries = this.countries.filter(country => country.toLowerCase().includes(val.toLowerCase()));
					} else {
						this.countries = ['Афганистан', 'Албания', 'Алжир', 'Андорра', 'Ангола', 'Антигуа и Барбуда', 'Аргентина', 'Армения', 'Австралия', 'Австрия', 'Азербайджан', 'Багамские Острова', 'Бахрейн', 'Бангладеш', 'Барбадос', 'Беларусь', 'Бельгия', 'Белиз', 'Бенин', 'Бутан', 'Боливия', 'Босния и Герцеговина', 'Ботсвана', 'Бразилия', 'Бруней', 'Болгария', 'Буркина-Фасо', 'Бурунди', 'Камбоджа', 'Камерун', 'Канада', 'Центральноафриканская Республика', 'Чад', 'Чили', 'Китай', 'Колумбия', 'Коморы', 'Конго (Браззавиль)', 'Конго (Киншаса)', 'Острова Кука', 'Коста-Рика', 'Кот-д-Ивуар', 'Хорватия', 'Куба', 'Кипр', 'Чехия', 'Дания', 'Джибути', 'Доминика', 'Доминиканская Республика', 'Эквадор', 'Египет', 'Сальвадор', 'Экваториальная Гвинея', 'Эритрея', 'Эстония', 'Эфиопия', 'Фиджи', 'Финляндия', 'Франция', 'Габон', 'Гамбия', 'Грузия', 'Германия', 'Гана', 'Греция', 'Гренада', 'Гватемала', 'Гвинея', 'Гвинея-Бисау', 'Гайана', 'Гаити', 'Гондурас', 'Венгрия', 'Исландия', 'Индия', 'Индонезия', 'Иран', 'Ирак', 'Ирландия', 'Израиль', 'Италия', 'Ямайка', 'Япония', 'Иордания', 'Казахстан', 'Кения', 'Кирибати', 'Северная Корея', 'Южная Корея', 'Косово', 'Кувейт', 'Кыргызстан', 'Лаос', 'Латвия', 'Ливан', 'Лесото', 'Либерия', 'Ливия', 'Лихтенштейн', 'Литва', 'Люксембург', 'Македония', 'Мадагаскар', 'Малави', 'Малайзия', 'Мальдивы', 'Мали', 'Мальта', 'Маршалловы Острова', 'Мавритания', 'Маврикий', 'Мексика', 'Микронезия', 'Молдова', 'Монако', 'Монголия', 'Черногория', 'Марокко', 'Мозамбик', 'Мьянма', 'Намибия', 'Науру', 'Непал', 'Нидерланды', 'Новая Зеландия', 'Никарагуа', 'Нигер', 'Нигерия', 'Норвегия', 'Оман', 'Пакистан', 'Палау', 'Панама', 'Папуа-Новая Гвинея', 'Парагвай', 'Перу', 'Филиппины', 'Польша', 'Португалия', 'Катар', 'Румыния', 'Россия', 'Руанда', 'Сент-Китс и Невис', 'Сент-Люсия', 'Сент-Винсент и Гренадины', 'Самоа', 'Сан-Марино', 'Сан-Томе и Принсипи', 'Саудовская Аравия', 'Сенегал', 'Сербия', 'Сейшельские Острова', 'Сьерра-Леоне', 'Сингапур', 'Синт-Мартен', 'Словакия', 'Словения', 'Соломоновы Острова', 'Сомали', 'Южная Африка', 'Южный Судан', 'Испания', 'Шри-Ланка', 'Судан', 'Суринам', 'Свазиленд', 'Швеция', 'Швейцария', 'Сирия', 'Таджикистан', 'Танзания', 'Таиланд', 'Восточный Тимор', 'Того', 'Тонга', 'Тринидад и Тобаго', 'Тунис', 'Турция', 'Туркменистан', 'Тувалу', 'Уганда', 'Украина', 'Объединенные Арабские Эмираты', 'Великобритания', 'Соединенные Штаты', 'Уругвай', 'Узбекистан', 'Вануату', 'Ватикан', 'Венесуэла', 'Вьетнам', 'Йемен', 'Замбия', 'Зимбабве']
					}
				}
			},
			computed: {...mapGetters(['userRole']),
				isAdmin() {
					return Array.isArray(this.userRole) && this.userRole.includes('admin');
				},
			},
			async mounted() {
				this.id = this.$route.params.id;
				const token = localStorage.getItem('token');
				await this.fetchData(token);
			},
			methods: {
				async fetchData(token) {
					console.log(this.userRole);
					console.log('Запрос данных с токеном:', token);
					const id = this.id;
					try {
						const url = new URL('http://localhost:8000/api/strain');
						url.searchParams.append('id', id);
						const response = await fetch(url, {
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
						if (result.data) {
							const data = result.data[0];
							this.name = data.name || '';
							this.place_of_allocation = data.place_of_allocation || '';
							this.sequence = data.file_content || '';
							this.year_of_allocation = data.year_of_allocation || '';
							this.link = data.link || '';
							this.type_of_bacteria = data.type_of_bacteria || '';
							if (data.analyze_strains) {
								this.analyzeStrains = data.analyze_strains;
							}
						} else {
							console.error('Нет данных в ответе:', result);
						}
					} catch (error) {
						console.error('Ошибка при получении данных:', error);
					}
				},
				toggleEdit() {
					if (this.isEditing) {
						this.saveChanges();
					}
					this.isEditing = !this.isEditing;
				},
				async saveChanges() {
					const token = localStorage.getItem('token');
					try {
						const url = `http://localhost:8000/api/strain/${this.id}`;
						const response = await fetch(url, {
							method: 'PUT',
							headers: {
								'Authorization': `Bearer ${token}`,
								'Content-Type': 'application/json',
							},
							body: JSON.stringify({
								name: this.name,
								place_of_allocation: this.place_of_allocation,
								file_content: this.sequence,
								year_of_allocation: this.year_of_allocation,
								type_of_bacteria: this.type_of_bacteria,
								link: this.link,
							}),
						});
						if (!response.ok) {
							throw new Error('Ошибка сети: ' + response.statusText);
						}
						const result = await response.json();
						console.log('Изменения сохранены:', result);
						this.isEditing = false;
					} catch (error) {
						console.error('Ошибка при сохранении данных:', error);
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
			}
	}
</script>

<style scoped>
@media (max-width: 600px) {
  .back-button {
    display: none !important;
  }
}
</style>