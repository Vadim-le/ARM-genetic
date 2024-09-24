<template>
  <v-app>
    <v-main class="d-flex justify-center" style="height: 100vh; background-color:rgba(50, 132, 224, 0.6);">
      <v-container>
        <v-row class="d-flex flex-wrap justify-center">
          <v-col sm="12" md="8" class="ma-2"> <!-- Верхняя карточка -->
            <v-card>
              <v-img
                src="@/images/login.png" 
                height="200" 
                width="200" 
                contain
                class="mb-2"
              ></v-img>
              <v-card-title>Верхняя карточка</v-card-title>
            </v-card>
            <v-card-actions>
              <v-btn 
                :class="{'v-btn--active': showFirstGroup}" 
                @click="showFirstSet">Показать первую группу</v-btn>
              <v-btn 
                :class="{'v-btn--active': showSecondGroup}" 
                @click="showSecondSet">Показать вторую группу</v-btn>
            </v-card-actions>
          </v-col>

          <!-- Карточки первой группы -->
          <template v-if="showFirstGroup">
            <v-col sm="6" md="4"> 
              <v-card>
                <v-img
                  src="@/images/login.png" 
                  height="200" 
                  width="200" 
                  contain
                  class="mb-2"
                ></v-img>
                <v-card-title>Первая карточка</v-card-title>
              </v-card>
            </v-col>

            <v-col sm="6" md="4"> 
              <v-card>
                <v-img
                  src="@/images/login.png" 
                  height="200" 
                  width="200" 
                  contain
                  class="mb-2"
                ></v-img>
                <v-card-title>Вторая карточка</v-card-title>
              </v-card>
            </v-col>

            <v-col sm="12" md="8"> 
              <v-card>
                <v-img
                  src="@/images/login.png" 
                  height="200" 
                  width="200" 
                  contain
                  class="mb-2"
                ></v-img>
                <v-card-title>Третья карточка</v-card-title>
              </v-card>
            </v-col>
          </template>

          <!-- Карточки второй группы -->
          <template v-if="showSecondGroup">
            <v-col sm="12" md="6">
              <v-card>
                <v-img
                  src="@/images/login.png" 
                  height="200" 
                  width="200" 
                  contain
                  class="mb-2"
                ></v-img>
                <v-card-title>Шестая карточка</v-card-title>
              </v-card>
            </v-col>
          </template>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  data() {
    return {
      showFirstGroup: false,
      showSecondGroup: false,
    };
  },
  methods: {
    showFirstSet() {
      this.showFirstGroup = true;
      this.showSecondGroup = false;
    },
    showSecondSet() {
      this.showFirstGroup = false;
      this.showSecondGroup = true;
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
</style>
