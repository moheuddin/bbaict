<template>
  <div class="container">
    <AdminMenu />
    <div class="card mb-4">
      <div class="card-body">
        <strong>{{currentUser.username}}</strong> Profile
      </div>
    </div>
    <template>
      <div id="profile">
        <form name="form" @submit.prevent="handleSave">
          <div >
            <div class="form-group">
              <label for="username">Username</label>
              <input
                v-model="formData.username"
                v-validate="'required|min:3|max:20'"
                type="text"
                class="form-control"
                name="username"
              />
              <div
                v-if="submitted && errors.has('username')"
                class="alert-danger"
              >{{errors.first('username')}}</div>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input
                v-model="formData.email"
                v-validate="'required|email|max:50'"
                type="email"
                class="form-control"
                name="email"
              />
              <div
                v-if="submitted && errors.has('email')"
                class="alert-danger"
              >{{errors.first('email')}}</div>
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input
                v-model="formData.password"
                v-validate="'min:6|max:40'"
                type="password"
                class="form-control"
                name="password" ref="password"
              />
              <div
                v-if="submitted && errors.has('password')"
                class="alert-danger"
              >{{errors.first('password')}}</div>
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input
              class="form-control"
              v-validate="'confirmed:password'" name="password_confirmation" type="password" placeholder=" " data-vv-as="password">
              <div
                v-if="submitted && errors.has('password_confirmation')"
                class="alert-danger"
              >{{errors.first('password_confirmation')}}</div>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" :disabled="loading">
                <span v-show="loading" class="spinner-border spinner-border-sm"></span>
                <span>Save</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </template>
  </div>
</template>

<script>
import config from '../../config'
import User from '../models/user';
import  axios from 'axios'
//import Swal from 'sweetalert2'
import AdminMenu from '../view/components/Adminmenu.vue';


export default {
  name: 'Profile',
  components:{
    AdminMenu
  },
  data() {
    return {
      submitted: false,
      successful: false,
      message: '',
      loading: false,
      formData: {
        username: "",
        email: "",
        password:'',
        confirm:''
      }
    };
  },
  computed: {
    currentUser() {
      return this.$store.state.auth.user;
    },
    loggedIn(){
      //return this.$store.state.status.loggedIn
    }
  },
  mounted() {
    if (!this.currentUser) {
      this.$router.push('/login');
      return;
    }
    this.formData = this.$store.state.auth.user;

    const loggeduser = JSON.parse(localStorage.getItem('user'));
    if(loggeduser!=''){
      this.token = loggeduser.jwt;
    }
    axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`

  },
  methods:{
    handleSave: function() {
      this.message = '';
      this.submitted = true;

      let data = this.formData
      this.$validator.validate().then(isValid => {
        console.log(isValid)
        //console.log(data)
        if (!isValid) {
          this.loading = false;
          return;
        }
        this.loading=true
        axios.post(config.API_URL + 'profile.php', {
          //headers: { Authorization: `Bearer ${token}` },
             data

        }).then((response) => {
          //console.log(response);
          //this.items.splice(idx, 1)
          /*if(response.data.result=='updated'){
            Swal.fire({
              title: 'Success!',
              text: 'User has been updated!',
              icon: 'success',
              confirmButtonText: 'Close'
            })
          }*/
          this.loading=false;
          //this.$validator.clean();
          this.$validator.reset();


        })

      });
    }
  }
};
</script>
