<template>
	<v-container>
		<v-row justify="center" align="center" class="my-5">
			<v-col cols="12" md="8">
				<v-card class="elevation-12 rounded-lg" style="width: 100%;">
					<v-toolbar color="primary" dark flat>
						<v-toolbar-title class="text-h4 font-weight-bold">
							Поиск повторов и спэйсеров
						</v-toolbar-title>
					</v-toolbar>
					<v-card-text>
						<v-row>
							<v-col cols="12" sm="6">
								<v-select v-model="selectedStrain" :items="strains" item-text="name" item-value="id" label="Выберите штамм" @change="fetchAnalyzeStrains" outlined dense></v-select>
							</v-col>
							<v-col cols="12" sm="6" class="d-flex align-center justify-center">
								<v-btn @click="fetchAnalyzeStrains" :disabled="!selectedStrain" color="primary" outlined>
									Начать поиск
								</v-btn>
							</v-col>
						</v-row>
						<v-alert v-if="!selectedStrain" type="info" class="custom-alert" outlined dense>
							<strong>Примечание:</strong> Сначала выберите штамм, чтобы активировать кнопку поиск.
						</v-alert>
					</v-card-text>
				</v-card>
			</v-col>
		</v-row>
		<v-row justify="center" align="center" class="my-5">
			<v-col cols="12" md="8" class="d-flex justify-center align-center">
				<!-- Add a loading animation component -->
				<div v-if="loading">
					<v-progress-circular indeterminate color="primary" size="100"></v-progress-circular>
				</div>
				<!-- Conditionally render the results card -->
				<v-card v-else-if="numRepeats !== 0 && numSpacers !== 0" class="elevation-12 rounded-lg" style="width: 80vw">
					<v-card-title class="text-h6"> Результаты поиска </v-card-title>
					<v-card-text>
						<p>Найдено повторов: {{ numRepeats }}</p>
						<p>Найдено спейсеров: {{ numSpacers }}</p>
					</v-card-text>
					<v-card-title>Спэйсеры и повторяющиеся последовательности</v-card-title>
						<v-card-text>
							<v-data-table :headers="headers" :items="analyzeStrains" :items-per-page="5" class="elevation-1" dense>
							</v-data-table>
						</v-card-text>
					<v-alert type="info" class="custom-alert" outlined>
						<strong>Примечание:</strong> Для дальнейшего анализа и работы с полученными результатами, пожалуйста, воспользуйтесь нашим инструментом <strong>"Анализ нуклеотидов"</strong> в разделе <strong>"Инструменты анализа"</strong>!
					</v-alert>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
</template>
  
<script>
	export default {
		data() {
				return {
					strains: [], // Список штаммов
					selectedStrain: null, // Выбранный штамм
					headers: [{
						title: 'Повторяющиеся последовательности',
						key: 'repeat',
						align: 'start',
						sortable: false
					}, {
						title: 'Индексы повторяющихся последовательностей',
						key: 'repeat_positions',
						align: 'end',
						sortable: false
					}, {
						title: 'Спэйсеры',
						key: 'spacer',
						align: 'end',
						sortable: false
					}, {
						title: 'Индексы спэйсеров',
						key: 'spacer_positions',
						align: 'end',
						sortable: false
					}, ],
					numRepeats: 0,
					numSpacers: 0,
					loading: false,
				};
			},
			created() {
				this.fetchStrains();
			},
			methods: {
				async fetchStrains() {
					const token = localStorage.getItem('token');
					try {
						const response = await fetch('http://localhost:8000/api/strain/spacer_search', {
							method: 'GET',
							headers: {
								'Content-Type': 'application/json',
								'Authorization': `Bearer ${token}`, 
							},
						});
						if (!response.ok) {
							throw new Error('Ошибка сети: ' + response.statusText);
						}
						const data = await response.json();
						console.log(data);
						if (Array.isArray(data.data)) {
							this.strains = data.data;
						} else {
							console.error('Ожидается массив, но получен другой тип данных:', data.data);
						}
					} catch (error) {
						console.error('Ошибка при получении штаммов:', error);
					}
				},

				async fetchAnalyzeStrains() {
					this.loading = true;
					const token = localStorage.getItem('token');
					console.log(this.selectedStrain);
					if (!this.selectedStrain) return;
					try {
						const response = await fetch(`http://localhost:8000/api/strain/find-repeats?strain_name=${this.selectedStrain}`, {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'Authorization': `Bearer ${token}`,
							},
						});
						if (!response.ok) {
							throw new Error('Ошибка сети: ' + response.statusText);
						}
						const data = await response.json();
						console.log(data);
						this.analyzeStrains = data.spacers_info.map(item => ({
							repeat: item.repeat,
							repeat_positions: item.repeat_positions.map(position => `${position[0]} - ${position[1]}`).join(', '),
							spacer: item.spacer,
							spacer_positions: item.spacer_positions.join(', '),
						}));
						this.numRepeats = data.num_repeats || 0;
						this.numSpacers = data.num_spacers || 0;
					} catch (error) {
						console.error('Ошибка при получении записей анализа:', error);
						this.numRepeats = 0;
						this.numSpacers = 0;
					} finally {
						this.loading = false; 
					}
				},
			},
	};
</script>
  
  <style scoped>
  .custom-alert {
    background-color: rgba(0, 255, 255, 0.055) !important;
    color: #1bb40d !important;
  }
  
  .record-text {
    font-size: 16px;
    line-height: 1.5;
  }
  
  .record-text p {
    margin-bottom: 10px;
  }
  
  .record-text span {
    font-size: 14px;
    color: #666;
  }
  
  .custom-alert {
    font-size: 14px;
    margin-top: 10px;
  }
  </style>