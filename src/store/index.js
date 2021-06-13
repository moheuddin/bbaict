import Vue from 'vue'
import Vuex from 'vuex'
import { auth } from './auth.module';
Vue.use(Vuex);

const store = new Vuex.Store({
  state: {
    appname:"Bridges Division Daily Program",
    posts: [],
    //API_URL:'',
    globalVar: require('../../config'),
    loading: true,
    count:1,
    currentUser: {
      username:''
    }
  },
  modules: {
    auth
  },
  getters:{
    getappname: state => {
      return state.appname;
    },
    posts: state => {
      return state.posts
    },
    currentuser: state => {
      //const loggeduser = JSON.parse(localStorage.getItem('user'))
      //state.currentUser.username= loggeduser;
      return state.currentUser.username;

    },
    getAPIURL: state => {
      //const loggeduser = JSON.parse(localStorage.getItem('user'))
      //state.currentUser.username= loggeduser;
      return state.API_URL;

    },

  },
  mutations: {
    updatePosts(state, posts) {
      state.posts = posts
    },
    changeLoadingState(state, loading) {
      state.loading = loading
    },
      increment (state, payload) {
        state.count += payload.amount
      },
      currentuser(state,payload){
        state.currentUser.username = payload
      }
  },
 actions: {

  }
})
export default store;
