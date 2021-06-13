import Vue from 'vue'
import App from './App.vue'
import axios from 'axios';
//console.log("Vue Version "+ Vue.version);

import store from './store'
import router from './router'// loads from src/router/index.js
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import globals from '../config.js'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import VeeValidate from 'vee-validate';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import moment from 'moment'

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

//const axios = axiosApi.create({
   // baseURL:`your_base_url`,
    //headers:{ header:value }
//});
   //axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`

//Use the window object to make it available globally.
//window.axios = axios;

window.axios = require('axios');
const base = axios.create({
  baseURL: "http://127.0.0.1:8000/", 
});

Vue.prototype.$http = base;
let token='';
const loggeduser = JSON.parse(localStorage.getItem('user'));
console.log(loggeduser)
    if(loggeduser != null){
      token = loggeduser.jwt;
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    }


Vue.config.productionTip = false;

Vue.use(VeeValidate);
Vue.component('font-awesome-icon', FontAwesomeIcon);

//console.log(process.env.VUE_APP_API_ROOT)
Vue.prototype.$API_URL='https://eservice.bba.gov.bd/bridgediv-program/api/';
Vue.prototype.moment = moment
import localization from 'moment/locale/bn';



moment.updateLocale('bn', localization);
Vue.mixin({
  data: function() {
    return {
      globalVar:'global'
    }
  }
})

new Vue({
  el: '#app',
  router,
  store,
  data: {
    isEditing: false,
    isModalOpen: false,
    API:'test'
},
  template: '<App/>',
  components: { App }
})

