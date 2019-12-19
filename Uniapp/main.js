import Vue from 'vue'
import App from './App'
import http from './common/axios.js'

Vue.config.productionTip = false
Vue.prototype.$api = {http};

App.mpType = 'app'

const app = new Vue({
    ...App
})
app.$mount()
