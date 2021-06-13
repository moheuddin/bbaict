<AdminMenu/>
<template>
  <div class="container">
    <AdminMenu />
    <div class="card">

      <form name="form" @submit.prevent="handleRegister">
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
              v-validate="'required|min:6|max:40'"
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
            <button class="btn btn-primary btn-block">Register</button>
          </div>
        </div>
      </form>

      <div
        v-if="message"
        class="alert"
        :class="successful ? 'alert-success' : 'alert-danger'"
      >{{message}}</div>
    </div>
  </div>
</template>

<script>
    import axios from "axios";
    //axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
    //axios.defaults.headers.post['Content-Type'] ='application/x-www-form-urlencoded';
    import User from '../models/user';
    import AdminMenu from '../view/components/Adminmenu.vue';

    export default {
        name: 'Register',
        components: {
            AdminMenu
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
            regiser: function() {
                let formData = new FormData();
                formData.append("username", this.formData.username || "");
                formData.append("email", this.formData.email || "");
                formData.append("password", this.formData.password || "");
                //formData.append("designation", this.formData.designation || "");

                const auth = {
                    headers: {
                        Authorization: 'JWT ' + localStorage.getItem('user')
                    }
                }
                axios({
                        method: "post",
                        url: "http://bbaict.test/api/register.php",
                        data: formData,
                        config: auth

                    })
                    .then(function(response) {
                        //handle success
                        //console.log(response);
                        //app.filteredContacts.push(contact)
                        //document.getElementById("userform").reset();
                    })
                    .catch(function(response) {
                        //handle error
                        //console.log(response)
                    });

            },
            handleRegister: function() {
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

<style scoped>
    label {
        display: block;
        margin-top: 10px;
    }
    
    .card-container.card {
        max-width: 350px !important;
        padding: 40px 40px;
    }
    
    .card {
        background-color: #f7f7f7;
        padding: 20px 25px 30px;
        margin: 0 auto 25px;
        margin-top: 50px;
        -moz-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    }
    
    .profile-img-card {
        width: 96px;
        height: 96px;
        margin: 0 auto 10px;
        display: block;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        border-radius: 50%;
    }
</style>