import Vue from 'vue'
import App from './App.vue'


import store from './store'
import router from './router'// loads from src/router/index.js
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import VeeValidate from 'vee-validate';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {
  faHome,
  faUser,
  faUserPlus,
  faSignInAlt,
  faSignOutAlt,
  faSpinner
} from '@fortawesome/free-solid-svg-icons';

library.add(faHome, faUser, faUserPlus, faSignInAlt, faSignOutAlt,faSpinner);
// ES6 Modules or TypeScript

console.log(process.env.VUE_APP_API_URL);
Vue.config.productionTip = false;

Vue.use(VeeValidate);
Vue.component('font-awesome-icon', FontAwesomeIcon);

Vue.component("v-select", vSelect);

Vue.prototype.$API_URL='http://bbaict.test/api/';

//const API_URL = 'http://bbaict.test/api/';
//console.log(process.env.API_URL)
/*import axios from 'axios'
axios.interceptors.request.use(
  (config) => {
    let token = localStorage.getItem('authtoken');

    if (token) {
      //config.headers['Authorization'] = `Bearer ${ token }`;
      config.headers['Authorization'] = `Bearer`;
    }

    return config;
  },

  (error) => {
    return Promise.reject(error);
  }
);
*/
//console.log(process.env.VUE_APP_API_URL);
var app = new Vue({
  el: '#app',
  router,
  store,
  data: {
    isEditing: false,
    isModalOpen: false,
},
  template: '<App/>',
  components: { App }
})

