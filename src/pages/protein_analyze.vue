<template>
    <div>
      <h1>Анализ белков</h1>
      <form @submit.prevent="submitForm">
        <div>
          <label for="file">Загрузить файл:</label>
          <input type="file" id="file" @change="handleFileUpload" required />
        </div>
        <div>
          <label for="mode">Режим:</label>
          <select v-model="mode" id="mode" required>
            <option value="DNA">ДНК</option>
            <option value="Protein">Белок</option>
          </select>
        </div>
        <div>
          <label for="pH">pH:</label>
          <input type="number" v-model="pH" id="pH" step="0.1" required />
        </div>
        <div>
          <h3>Анализы:</h3>
          <label><input type="checkbox" value="hydrophobicity" v-model="analyses" /> Гидрофобность</label>
          <label><input type="checkbox" value="molecular_mass" v-model="analyses" /> Молекулярная масса</label>
          <label><input type="checkbox" value="amino_acid_content" v-model="analyses" /> Содержание аминокислот</label>
          <label><input type="checkbox" value="isoelectric_point" v-model="analyses" /> Изоэлектрическая точка</label>
          <label><input type="checkbox" value="protein_charge" v-model="analyses" /> Заряд белка</label>
        </div>
        <div v-if="analyses.includes('hydrophobicity')">
          <h3>Шкалы гидрофобности:</h3>
          <label><input type="checkbox" value="kyteDoolittle" v-model="scales" /> Kyte-Doolittle</label>
          <label><input type="checkbox" value="hopfield" v-model="scales" /> Hopfield</label>
          <label><input type="checkbox" value="griffith" v-model="scales" /> Griffith</label>
        </div>
        <button type="submit">Отправить</button>
      </form>
      <div v-if="results">
        <h2>Результаты анализа:</h2>
        <pre>{{ results }}</pre>
      </div>
    </div>
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
          console.log('Результаты анализа:', this.results);
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