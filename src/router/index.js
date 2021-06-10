import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../view/Home.vue'


import Login from '../view/Login.vue';
import Register from '../view/Register.vue';
import Profile from '../view/Profile.vue'
import Program from '../view/Program.vue'
//import User from '../view/User.vue'
import Allusers from '../view/Allusers.vue'
import store from '../store/index'

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
    component: Profile,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/register',
    component: Register,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/allusers',
    component: Allusers,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/login',
    component: Login
  },
  {
    path: '/program',
    component: Program,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/user',
    component: Profile,
    meta: {
      requiresAuth: true
    }
  },

]

const router = new VueRouter({
  mode: 'history',
  base: __dirname,
  routes,
})

router.beforeEach((to, from, next) => {
  const authenticatedUser = store.state.auth.status.loggedIn;
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

  // Check for protected route
  if (requiresAuth && !authenticatedUser) next('login')
  else next();
});

export default router
