<template>
	<v-container fluid class="pa-0">
		<v-row justify="center" class="my-10">
			<v-col cols="12" md="8" lg="6" class="pa-0">
				<v-card class="elevation-12 rounded-lg">
					<v-toolbar color="primary" dark flat>
						<v-toolbar-title class="text-h4 font-weight-bold">
							Анализ белков
						</v-toolbar-title>
					</v-toolbar>
					<v-card-text class="pa-6">
						<v-form @submit.prevent="submitForm">
							<v-radio-group v-model="uploadMode" row class="mb-4">
								<v-radio label="Загрузить файл" value="upload"></v-radio>
								<v-radio label="Выбрать из базы данных" value="select"></v-radio>
							</v-radio-group>

							<!-- File input for uploading a new file -->
							<v-file-input v-if="uploadMode === 'upload'" v-model="file" label="Загрузить файл" prepend-icon="mdi-file-upload" outlined dense class="mb-4" :rules="[v => !!v || 'Файл обязателен']"></v-file-input>

							<!-- Select input for choosing a file from the database -->
							<v-select v-if="uploadMode === 'select'" v-model="selectedFile" :items="databaseFiles" label="Выбрать файл из базы данных" prepend-icon="mdi-database" outlined item-text="name" item-value="name" dense class="mb-4" :rules="[v => !!v || 'Выбор файла обязателен']"></v-select>

							<v-select v-model="mode" :items="['DNA', 'Protein']" label="Режим анализа" prepend-icon="mdi-dna" outlined dense class="mb-4"></v-select>

							<v-text-field v-model="pH" label="pH" type="number" step="0.1" prepend-icon="mdi-flask" outlined dense class="mb-4" :rules="[
                  v => !!v || 'pH обязателен',
                  v => v >= 0 || 'pH не может быть меньше 0',
                  v => v <= 14 || 'pH не может быть больше 14'
                ]" min="0" max="14"></v-text-field>

							<v-divider class="mb-4"></v-divider>

							<v-subheader class="pl-0 text-subtitle-1 font-weight-bold">Выберите анализы:</v-subheader>
							<v-chip-group v-model="analyses" column multiple>
								<v-chip v-for="analysis in availableAnalyses" :key="analysis.value" :value="analysis.value" filter outlined>
									{{ analysis.text }}
								</v-chip>
							</v-chip-group>

							<v-expand-transition>
								<div v-if="analyses.includes('hydrophobicity')">
									<v-divider class="my-4"></v-divider>
									<v-subheader class="pl-0 text-subtitle-1 font-weight-bold">Шкалы гидрофобности:</v-subheader>
									<v-chip-group v-model="scales" column multiple>
										<v-chip v-for="scale in availableScales" :key="scale.value" :value="scale.value" filter outlined>
											{{ scale.text }}
										</v-chip>
									</v-chip-group>
								</div>
							</v-expand-transition>

							<v-row class="mt-6" align="center" justify="center">
								<v-col cols="12" md="6">
									<v-btn type="submit" color="primary" block large :loading="loading">
										Анализировать
									</v-btn>
								</v-col>
								<v-col cols="12" md="6">
									<v-btn color="red" block large @click="resetForm">
										Сбросить
									</v-btn>
								</v-col>
							</v-row>
						</v-form>
					</v-card-text>
				</v-card>

				<v-expand-transition>
					<v-card v-if="results" class="mt-6 elevation-12 rounded-lg">
						<v-toolbar color="success" dark flat>
							<v-toolbar-title class="text-h5 font-weight-bold">
								Результаты анализа
							</v-toolbar-title>
						</v-toolbar>
						<v-card-text class="pa-6">
							<v-row>
								<v-col cols="12" md="6">
									<canvas v-if="results.amino_acid_content" id="aminoAcidChart"></canvas>
								</v-col>
								<v-col cols="12" md="6">
									<canvas v-if="results.amino_acid_content" id="aminoAcidPercentageChart"></canvas>
								</v-col>
							</v-row>

							<v-list>
								<v-list-item v-for="(value, key) in formattedResults" :key="key">
									<v-list-item-content>
										<v-list-item-title class="text-h6 font-weight-bold">{{ key }}:</v-list-item-title>
										<div v-if="Array.isArray(value)">
											<div v-for="(item, index) in value" :key="index" style="display: block" class="text-subtitle-1 font-weight-normal ml-4">{{ item }}</div>
										</div>
										<div v-else class="text-subtitle-1 font-weight-normal ml-4">{{ value }}</div>
									</v-list-item-content>
								</v-list-item>
							</v-list>
						</v-card-text>
					</v-card>
				</v-expand-transition>
			</v-col>
		</v-row>

		<v-snackbar v-model="snackbar" :timeout="snackbarTimeout" :color="snackbarColor" rounded="pill" elevation="24" class="custom-snackbar">
			<div class="d-flex align-center">
				<v-icon :icon="snackbarIcon" class="mr-3" /> {{ snackbarText }}
			</div>
			<template v-slot:actions>
				<v-btn color="white" variant="text" @click="snackbar = false" class="custom-close-btn">
					Закрыть
				</v-btn>
			</template>
		</v-snackbar>
	</v-container>
</template>

<script>
	import Chart from 'chart.js/auto';
	export default {
		data() {
				return {
					uploadMode: 'upload',
					loading: false,
					databaseFiles: [],
					selectedFile: '',
					snackbar: false,
					snackbarText: '',
					snackbarColor: '',
					snackbarIcon: '',
					file: null,
					mode: 'DNA',
					pH: 7.0,
					analyses: [],
					scales: [],
					results: null,
					loading: false,
					aminoAcidChartInstance: null,
					aminoAcidPercentageChartInstance: null,
					availableAnalyses: [{
						text: 'Гидрофобность',
						value: 'hydrophobicity'
					}, {
						text: 'Молекулярная масса',
						value: 'molecular_mass'
					}, {
						text: 'Содержание аминокислот',
						value: 'amino_acid_content'
					}, {
						text: 'Изоэлектрическая точка',
						value: 'isoelectric_point'
					}, {
						text: 'Заряд белка',
						value: 'protein_charge'
					}, ],
					availableScales: [{
						text: 'Kyte-Doolittle',
						value: 'kyteDoolittle'
					}, {
						text: 'Hopfield',
						value: 'hopfield'
					}, {
						text: 'Griffith',
						value: 'griffith'
					}, ],
				};
			},
			created() {
				this.fetchStrains();
			},
			computed: {
				formattedResults() {
					const results = this.results;
					const formattedResults = {};
					if (results.hydrophobicity) {
						const hydrophobicity = [];
						if (results.hydrophobicity.kyteDoolittle !== undefined) {
							hydrophobicity.push(`Кайт-Дули: ${results.hydrophobicity.kyteDoolittle}`);
						}
						if (results.hydrophobicity.hopfield !== undefined) {
							hydrophobicity.push(`Хопфилд: ${results.hydrophobicity.hopfield}`);
						}
						if (results.hydrophobicity.griffith !== undefined) {
							hydrophobicity.push(`Гриффит: ${results.hydrophobicity.griffith}`);
						}
						if (hydrophobicity.length > 0) {
							formattedResults['Гидрофобность'] = hydrophobicity;
						}
					}
					if (results.molecular_mass !== undefined) {
						formattedResults['Молекулярная масса'] = `${results.molecular_mass} г/моль`;
					}
					if (results.isoelectric_point !== undefined) {
						formattedResults['Изоэлектрическая точка'] = `${results.isoelectric_point} pH`;
					}
					if (results.protein_charge !== undefined) {
						formattedResults['Заряд белка'] = `${results.protein_charge} зарядных единиц`;
					}
					return formattedResults;
				}
			},
			methods: {
				async fetchStrains() {
					const token = localStorage.getItem('token');
					try {
						const response = await fetch('http://localhost:8000/api/strain/name', {
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
							this.databaseFiles = data.data;
						} else {
							console.error('Ожидается массив, но получен другой тип данных:', data.data);
						}
					} catch (error) {
						console.error('Ошибка при получении штаммов:', error);
					}
				},
				async submitForm() {
					if (this.uploadMode === 'upload' && !this.file) {
						this.snackbar = true;
						this.snackbarText = 'Пожалуйста, загрузите файл!';
						this.snackbarColor = 'warning';
						return;
					}
					if (this.uploadMode === 'select' && !this.selectedFile) {
						this.snackbar = true;
						this.snackbarText = 'Пожалуйста, выберите файл из базы данных!';
						this.snackbarColor = 'warning';
						return;
					}
					if (this.analyses.length === 0) {
						this.snackbar = true;
						this.snackbarText = 'Пожалуйста, выберите хотя бы один анализ!';
						this.snackbarColor = 'warning';
						return;
					}
					if (this.pH < 0 || this.pH > 14) {
						this.snackbar = true;
						this.snackbarText = 'Значение pH должно быть в диапазоне от 0 до 14. Пожалуйста, введите корректное значение.';
						this.snackbarColor = 'warning';
						return;
					}
					if (this.analyses.includes('hydrophobicity') && this.scales.length === 0) {
						this.snackbar = true;
						this.snackbarText = 'Пожалуйста, выберите хотя бы одну шкалу гидрофобности!';
						this.snackbarColor = 'warning';
						return;
					}
					this.loading = true;
					const formData = new FormData();
					if (this.uploadMode === 'upload') {
						formData.append('file', this.file);
					} else if (this.uploadMode === 'select') {
						formData.append('database_file_name', this.selectedFile);
					}
					formData.append('mode', this.mode);
					formData.append('pH', this.pH);
					formData.append('analyses', JSON.stringify(this.analyses));
					formData.append('scales', JSON.stringify(this.scales));
					try {
						const response = await fetch('http://localhost:8000/api/proteins/analyze', {
							method: 'POST',
							body: formData,
						});
						if (!response.ok) {
							throw new Error('Ошибка сети: ' + response.statusText);
						}
						const data = await response.json();
						console.log(data);
						this.results = data;
						this.$nextTick(() => {
							if (data.amino_acid_content) {
								this.renderAminoAcidChart(data.amino_acid_content);
								this.renderaminoAcidPercentageChart(data.amino_acid_content);
							}
						});
					} catch (error) {
						console.error('Ошибка при отправке формы:', error);
					} finally {
						this.loading = false;
					}
				},
				renderAminoAcidChart(aminoAcidContent) {
					if (this.aminoAcidChartInstance) {
						this.aminoAcidChartInstance.destroy();
					}
					const ctx = document.getElementById('aminoAcidChart').getContext('2d');
					this.aminoAcidChartInstance = new Chart(ctx, {
						type: 'bar',
						data: {
							labels: Object.keys(aminoAcidContent.count),
							datasets: [{
								label: 'Содержание аминокислот',
								data: Object.values(aminoAcidContent.count),
								backgroundColor: 'rgba(75, 192, 192, 0.2)',
								borderColor: 'rgba(75, 192, 192, 1)',
								borderWidth: 1
							}]
						},
						options: {
							plugins: {
								title: {
									display: true,
									text: 'Содержание аминокислот',
									font: {
										family: 'Arial',
										size: 18,
                    fontStyle: 'bold'
									}
								}
							},
							legend: {
								display: true,
								labels: {
									font: {
										family: 'Arial',
										size: 18,
                    fontStyle: 'bold'
									}
								}
							},
							scales: {
								y: {
									beginAtZero: true,
									ticks: {
										font: {
											family: 'Arial',
											size: 14,
                      fontStyle: 'bold'
										}
									}
								},
								x: {
									ticks: {
										font: {
											family: 'Arial',
											size: 14,
                      fontStyle: 'bold'
										}
									}
								}
							}
						},
					});
				},
				renderaminoAcidPercentageChart(aminoAcidContent) {
					if (this.aminoAcidPercentageChartInstance) {
						this.aminoAcidPercentageChartInstance.destroy();
					}
					const ctx = document.getElementById('aminoAcidPercentageChart').getContext('2d');
					this.aminoAcidPercentageChartInstance = new Chart(ctx, {
						type: 'pie',
						data: {
							labels: Object.keys(aminoAcidContent.percentage),
							datasets: [{
								label: 'Процентное содержание аминокислот',
								data: Object.values(aminoAcidContent.percentage),
								backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)'
									// Добавьте столько цветов, сколько необходимо для каждого сегмента
								],
								borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'
									// Добавьте соответствующие границы для каждого цвета
								],
								borderWidth: 1,
								fill: false
							}]
						},
						options: {
							plugins: {
								title: {
									display: true,
									text: 'Процентное содержание аминокислот',
									font: {
										family: 'Arial',
										size: 18,
                    fontStyle: 'bold'
									}
								}
							},
							scales: {
								x: {
									grid: {
										display: false
									}
								},
								y: {
									grid: {
										display: false
									},
									beginAtZero: false
								}
							}
						}
					});
				},
				resetForm() {
					this.uploadMode = 'upload';
					this.file = null;
					this.selectedFile = '';
					this.mode = 'DNA';
					this.pH = 7.0;
					this.analyses = [];
					this.scales = [];
					this.results = null;
					this.aminoAcidChartInstance = null;
					this.aminoAcidPercentageChartInstance = null;
				},
			}
	};
</script>

<style scoped>
.v-chip-group .v-chip {
  margin: 4px;
}
</style>