<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card>
          <v-card-title>
            <h1>Анализ белков</h1>
          </v-card-title>
          <v-card-text>
            <v-form @submit.prevent="submitForm">
              <v-file-input
                label="Загрузить файл"
                @change="handleFileUpload"
                required
              ></v-file-input>

              <v-select
                v-model="mode"
                :items="['DNA', 'Protein']"
                label="Режим"
                required
              ></v-select>

              <v-text-field
                v-model="pH"
                label="pH"
                type="number"
                step="0.1"
                required
              ></v-text-field>

              <v-subheader>Анализы:</v-subheader>
              <v-checkbox
                v-for="analysis in availableAnalyses"
                :key="analysis.value"
                :label="analysis.text"
                :value="analysis.value"
                v-model="analyses"
              ></v-checkbox>

              <v-divider v-if="analyses.includes('hydrophobicity')"></v-divider>

              <v-subheader v-if="analyses.includes('hydrophobicity')">Шкалы гидрофобности:</v-subheader>
              <v-checkbox
                v-for="scale in availableScales"
                :key="scale.value"
                :label="scale.text"
                :value="scale.value"
                v-model="scales"
                v-if="analyses.includes('hydrophobicity')"
              ></v-checkbox>

              <v-btn type="submit" color="primary">Отправить</v-btn>
            </v-form>
          </v-card-text>
        </v-card>

        <v-card v-if="results" class="mt-4">
          <v-card-title>
            <h2>Результаты анализа:</h2>
          </v-card-title>
          <v-card-text>
            <v-list dense>
              <v-list-item v-for="(value, key) in results" :key="key">
                <v-list-item-content>
                  <v-list-item-title>{{ key }}</v-list-item-title>
                  <v-list-item-subtitle>{{ value }}</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      file: null,
      mode: 'DNA',
      pH: 7.0,
      analyses: [],
      scales: [],
      results: null,
      availableAnalyses: [
        { text: 'Гидрофобность', value: 'hydrophobicity' },
        { text: 'Молекулярная масса', value: 'molecular_mass' },
        { text: 'Содержание аминокислот', value: 'amino_acid_content' },
        { text: 'Изоэлектрическая точка', value: 'isoelectric_point' },
        { text: 'Заряд белка', value: 'protein_charge' },
      ],
      availableScales: [
        { text: 'Kyte-Doolittle', value: 'kyteDoolittle' },
        { text: 'Hopfield', value: 'hopfield' },
        { text: 'Griffith', value: 'griffith' },
      ],
    };
  },
  methods: {
    handleFileUpload(event) {
      this.file = event.target.files[0];
    },
    async submitForm() {
      if (!this.file) {
        alert('Пожалуйста, загрузите файл.');
        return;
      }

      const formData = new FormData();
      formData.append('file', this.file);
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
        this.results = data;
        console.log(' Результаты анализа:', this.results);
      } catch (error) {
        console.error('Ошибка при отправке формы:', error);
        alert('Произошла ошибка при анализе. Пожалуйста, проверьте данные и попробуйте снова.');
      }
    },
 },
};
</script>
  
  <style scoped>
  /* Добавьте стили для вашего компонента */
  </style>