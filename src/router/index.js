import Vue from 'vue'
import VueRouter from  'vue-router'
import Home from '../view/Home.vue'


import Login from '../view/Login.vue';
import Register from '../view/Register.vue';
import Profile from '../view/Profile.vue'
import Program from '../view/Program.vue'
import User from '../view/User.vue'
import Allusers from '../view/Allusers.vue'


Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: "home",
    component: Home
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile
  },
  {
    path: '/register',
    component: Register
  },
  {
    path: '/allusers',
    component: Allusers
  },
  {
    path: '/login',
    component: Login
  },
  {
    path: '/program',
    component: Program
  },
  {
    path: '/user',
    component: User
  },

]

const router = new VueRouter({
  mode: 'history',
  routes
})
/*
router.beforeEach((to, from, next) => {
  const publicPages = ['/login', '/'];
  const authRequired = !publicPages.includes(to.path);
  const loggedIn = localStorage.getItem('user');

  // try to access a restricted page + not logged in
  if (authRequired && !loggedIn) {
    return next('/login');
  }

  next();
});
*/

export default router
