<template>
  <div>
    <!--<nav v-if="homePage==false" class="navbar navbar-expand navar-bg">-->
    <nav :class="`${currentUser ? 'navbar navbar-expand navar-bg' : 'visitor'}`">
      <a href class="navbar-brand" @click.prevent></a>
      <div class="navbar-nav mr-auto">
        <li v-if="!homePage" class="nav-item">
          <router-link to="/" class="nav-link">
            <font-awesome-icon icon="home" />
              Home
            </router-link>
          </li>
          <li v-if="currentUser != null" class="nav-item">
            <router-link to="/program" class="nav-link">
              <font-awesome-icon icon="" />Program
              </router-link>
            </li>
            <li class="nav-item">
              <router-link v-if="currentUser" to="/user" class="nav-link">User</router-link>
            </li>
          </div>

          <div v-if="!currentUser" class="navbar-nav ml-auto">

            <li class="nav-item" v-if="!currentUser">
              <router-link to="/login" class="nav-link">
                <font-awesome-icon icon="sign-in-alt" />Login
                </router-link>
              </li>
            </div>

            <div class="navbar-nav">
              <li v-if="currentUser != null" class="nav-item">
                <router-link to="/profile" class="nav-link">
                  <font-awesome-icon icon="user" />
                    {{ currentUser.username }}
                  </router-link>
                </li>
                <li v-if="currentUser != null" class="nav-item">
                  <a href="#" class="nav-link" @click.prevent="logOut">
                    <font-awesome-icon icon="sign-out-alt" />LogOut
                    </a>
                  </li>

                </div>
              </nav>
            </div>
          </template>

<script>
    import axios from "axios";
    //axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
    //axios.defaults.headers.post['Content-Type'] ='application/x-www-form-urlencoded';
    import User from '../../models/user.js';

    export default {
        name: 'Mainnav',
        components: {

        },
        data() {
            return {
                user: new User('', '', ''),
                submitted: false,
                successful: false,
                message: '',
                formData: {
                    username: "",
                    email: "",
                    password: ''
                }
            };
        },
        computed: {
            homePage() {
                if (this.$route.path == "/" || this.$route.path == "/home") {
                    return true;
                } else {
                    return false;
                }
                return true
            },
            currentUser() {
                return this.$store.state.auth.user;
            },
            loggedIn() {
                return this.$store.state.auth.status.loggedIn;
            }
        },
        mounted() {
            if (this.loggedIn) {
                //this.$router.push('/profile');
            }
        },
        methods: {
            logOut: function() {
                this.$store.dispatch('auth/logout');
                localStorage.removeItem('user');
                this.$router.push('/');
            },

        }

    };
</script>
<style scoped>
    .navbar-expand {
        justify-content: flex-end!important;
    }
    
    .navar-bg {
        background-color: red;
        color: #fff;
        padding: 0
    }
    
    .navar-bg .nav-item a {
        color: #fff;
    }
    
    nav.visitor {
        text-align: right;
        position: relative;
        height: 10px;
        top: -75px;
    }
</style>