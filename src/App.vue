
  <template>
  <div id="app">

<div class="container">
  <h2  class="text-center">{{myAppName}}</h2>
	<nav v-if="homePage==false" class="navbar navbar-expand navar-bg">
      <a href class="navbar-brand" @click.prevent></a>
      <div class="navbar-nav mr-auto">
        <li class="nav-item">
          <router-link to="/" class="nav-link">
            <font-awesome-icon icon="home" />
            Home
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/program" class="nav-link">
            <font-awesome-icon icon="" />Program
          </router-link>
        </li>
        <li class="nav-item">
          <router-link v-if="currentUser" to="/user" class="nav-link">User</router-link>
        </li>
      </div>

      <div v-if="!currentUser" class="navbar-nav ml-auto">
        <li class="nav-item">
          <router-link to="/register" class="nav-link">
            <font-awesome-icon icon="user-plus" />Sign Up
          </router-link>
        </li>
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
          <a href="#"class="nav-link" @click.prevent="logOut"><font-awesome-icon icon="sign-out-alt" />LogOut</a>
        </li>
      </div>
    </nav>
  </div>

    <div class="container pt-0">
      <router-view />
    </div>


  </div>
</template>
<script>

export default {
  name: 'App',
  components: {

	},
  data () {
    return {
      isOpen: false,
      showAdminMenu:false
    }
  },
  computed:{
    myAppName(){
      return this.$store.getters.getappname;
    },
    currentUser() {
      return this.$store.state.auth.user;
    },
    homePage() {
      if(this.$route.path == "/" || this.$route.path == "/home" ) {
        return true;
      }else{
        return false;
      }
  },
  created() {

  },
  methods:{

    logOut(){
      this.$store.dispatch('auth/logout');
      localStorage.removeItem('user');
      this.$router.push('/');
    },

    }
  }
}
</script>
<style>
  .navar-bg{background-color:red;color:#fff;}
  .navar-bg .nav-item a{color:#fff;}
</style>
