import { registerPlugins } from '@/plugins'
import store from './store/index';


import App from './App.vue'

import { createApp } from 'vue'

const app = createApp(App)

registerPlugins(app)

app.use(store);

app.mount('#app')
