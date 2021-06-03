<template>
  <div class="container">
    <div class="card mb-4">
      <div class="card-body">
        <strong>{{currentUser.username}}</strong> Profile
      </div>
    </div>
    <template>
      <div id="profile">
        <form name="form" @submit.prevent="handleSave">
          <div v-if="!successful">
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
                name="password"
              />
              <div
                v-if="submitted && errors.has('password')"
                class="alert-danger"
              >{{errors.first('password')}}</div>
            </div>
            <div class="form-group">
              <button class="btn btn-success">Save</button>
            </div>
          </div>
        </form>
      </div>
    </template>
  </div>
</template>

<script>
import User from '../models/user';
export default {
  name: 'Profile',
  data() {
    return {
      submitted: false,
      successful: false,
      message: '',
      formData: {
        username: "",
        email: "",
        password:''
      }
    };
  },
  created:{

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
  },
  methods:{
    handleSave: function() {
      this.message = '';
      this.submitted = true;
      this.$validator.validate().then(isValid => {
        if (isValid) {
          this.regiser();
          /*this.$store.dispatch('auth/register', this.user).then(
            data => {
              this.message = data.message;
              this.successful = true;
            },
            error => {
              this.message =
                (error.response && error.response.data) ||
                error.message ||
                error.toString();
              this.successful = false;
            }
          )*/
        }
      });
    }
  }
};
</script>
