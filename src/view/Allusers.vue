<template>
  <div class="container">
    <Adminmenu />
      <v-app>
        <div v-if="loading" class="text-center">
          <v-progress-circular
            indeterminate
            color="red"></v-progress-circular>
        </div>

        <v-main class="container align-to">
          <v-card-title>
            <v-spacer></v-spacer>
            <v-text-field v-model="search" append-icon="" label="Search User"
              single-line hide-details></v-text-field>
            <v-btn color="success" dark class="ml-auto ma-3"
              @click="showEditDialog()">
              New
              <v-icon small>mdi-plus-circle-outline</v-icon>
            </v-btn>

          </v-card-title>
          <v-card>
            <v-data-table
              :headers="headers"
              :items="items"
              mobile-breakpoint="800"
              class="elevation-0">

              <template v-slot:item.id="{ item }">
                {{item.id}}
              </template>

              <template v-slot:item.username="{ item }">
                {{item.username}}
              </template>

              <template v-slot:item.actions="{ item }">
                <div class="text-truncate">
                  <v-icon
                    small
                    class="mr-2"
                    @click="showEditDialog(item)"
                    color="primary">
                    mdi-pencil
                  </v-icon>
                  <v-icon
                    small
                    @click="deleteItem(item)"
                    color="pink">
                    mdi-delete
                  </v-icon>
                </div>
              </template>

            </v-data-table>
            <!-- this dialog is used for both create and upusername -->
            <v-dialog v-model="dialog" max-width="500px">
              <v-form
                ref="form"
                v-model="valid"
                lazy-validation>
                <v-card>
                  <v-card-title>
                    <span v-if="editedItem.id">Edit {{editedItem.id}}</span>
                    <span v-else>Create</span>
                  </v-card-title>
                  <v-card-text>
                    <v-row>

                      <v-col cols="12" sm="12">
                        <v-text-field name="username" v-validate="'required'"
                          v-model="editedItem.username" label="User Name"></v-text-field>
                        <span v-show="errors.has('username')"
                          class="text-danger">{{ errors.first('username') }}</span>
                      </v-col>
                      <v-col cols="12" sm="12">
                        <v-text-field name="email" v-validate="'required'"
                          v-model="editedItem.email" label="Email"></v-text-field>
                        <span class="text-danger">{{ errors.first('email') }}</span>
                      </v-col>
                      <v-col cols="12" sm="12">

                          <model-select name="roleid" :options=" [
                                  { value: '1', text: 'Administrator' },
                                  { value: '2', text: 'User' },
                                ]"
                                v-model="roleselected" placeholder="Select User">
                          </model-select>

                          <span class="text-danger">{{ errors.first('roleid') }}</span>
                        </v-col>
                        <v-col cols="12" sm="12">

                          <model-select name="is_Active" :options=" [
                                  { value: '1', text: 'Active' },
                                  { value: '0', text: 'In Active' },
                                ]"
                                v-model="isactiveelected" placeholder="Select User">
                          </model-select>

                          <span class="text-danger">{{ errors.first('is_Active') }}</span>
                          </v-col>
                    </v-row>
                      </v-card-text>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="green darken-1" text
                          @click="showEditDialog()">Cancel</v-btn>
                        <v-btn color="success" text :disabled="errors.any()"
                          @click="saveItem(editedItem)">Save <span
                            style="position: absolute;top: -32px;bottom: 0;"
                            v-if="loading">
                            <font-awesome-icon style="color:red;" icon="spinner"
                              class="rotation-animation large" /></span></v-btn>
                        </v-card-actions>
                      </v-card>
                    </v-form>
                  </v-dialog>
                </v-card>
              </v-main>
            </v-app>

          </div>
        </template>

        <script>
import Vue from 'vue'
import axios from 'axios'
import authHeader from '../services/auth-header.js';
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import AdminMenu from '../view/components/Adminmenu.vue';
import { ModelSelect } from 'vue-search-select'
import 'vue-search-select/dist/VueSearchSelect.css';
export default {
  name: 'username',
  vuetify: new Vuetify(),
  components:{
   'Adminmenu':AdminMenu,ModelSelect
  },
  data () {
    return {
      options:[],
      loading:false,
      search:'',
      valid: true,
        headers: [
            { text: '#', value: 'id', name:'id' },
            { text: 'নাম', value: 'username' },
            { text: 'ই-মেইল', value: 'email', sortable: false, width:"100" },
            { text: 'কার্যকর কিনা', value: 'isActive', name:'isactive', width:"180" },
            { text: 'রোল', value: 'roleid', name:'roleid', width:"180" },
            { text: '', value: 'actions', sortable: false },
        ],
        items: [],
        dialog: false,
        roleselected:'',
        isactiveelected:'',
        editedItem: {}
    }
  },
  mounted() {
    this.activeBtn = 'btn1';
    this.loadItems()
  },
  computed: {

    currentUser() {
      //return this.$store.state.auth.user;
    },
    loggedIn(){
      //return this.$store.state.status.loggedIn
    },
    formValid () {
      // loop over all contents of the fields object and check if they exist and valid.
      return Object.keys(this.fields).every(field => {
        return this.fields[field] && this.fields[field].valid;
      });
    }
  },
  watch: {
    dialog() {
      //this.$validator.clean();
    }
},

methods:{

  resetValidation () {
    this.$refs.form.resetValidation()
  },
  loadItems(filter='') {
    let token='';
    let $app_url = this.$API_URL;
    let data='';
    if(filter!=''){
      data= "{previous:'previsou'}";
    }
    const loggeduser = JSON.parse(localStorage.getItem('user'));
    if(loggeduser!=''){
      token = loggeduser.jwt;
    }
//console.log(authHeader)
    this.loading=true;
    this.items = []
    let user = JSON.parse(localStorage.getItem('user'));

    axios.get($app_url+'alluser.php',{
      //{ headers: { Authorization: "Bearer " + apiToken }}
      headers: { Authorization: `Bearer ${token}` },
       //headers: { Authorization: `Bearer testest` },
        params: {
           data
          }
        }
      ).then((response) => {
        //console.log(response.data.result);
        this.items = response.data.result;
        this.loading=false;


    }).catch((error) => {
        console.log(error)
    })

},

showEditDialog(item) {
  this.editedItem = item||{}

  this.dialog = !this.dialog
},
deleteItem(item) {
  let $api_url = this.$API_URL;
  //console.log($api_url);
  //console.log('deleteItem', item)
  let id = item.id
  let idx = this.items.findIndex(item => item.id===id)
  if (confirm('Are you sure you want to delete this?')) {
      /* not really deleting in API for demo */
      this.loading=true;
      axios.delete($api_url+'username.php',{
        params: {
          id: id,
          action: 'delete'
        }
          /*{ headers: {
              Authorization: "Bearer " + apiToken,
              "Content-Type": "application/json"
          }*/
      }).then((response) => {
        this.loading=false;
        console.log(response);
          this.items.splice(idx, 1)
      })
      //this.items.splice(idx, 1)
  }
},
  saveItem(item) {

    this.$validator.valiusernameAll()

    if (!this.formValid){
      return;
    }
    this.loading=true;
      let id = item.id

      let $api_url = this.$API_URL;
      //console.log(item);

      let data = { fields: item }
      if (id) {
        //upusername
        axios.patch($api_url + 'username.php', {
          params: {
            data
          }
          /*{ headers: {
              Authorization: "Bearer " + apiToken,
              "Content-Type": "application/json"
          }*/
        }).then((response) => {
          console.log(response);
          //this.items.splice(idx, 1)
          this.loading=false;
          this.editedItem = {};
          this.$validator.clean();
          this.dialog = !this.dialog


        })
        //insert
      } else {
        axios.post($api_url + 'username.php', {
          params: {
            data
          }
          /*{ headers: {
              Authorization: "Bearer " + apiToken,
              "Content-Type": "application/json"
          }*/
        }).then((response) => {
          console.log(response);
          this.loading=false;
          //this.items.splice(idx, 1)
          this.editedItem = {};
         let resultid = response.data.result;
         let insert = Object.assign(item, {id:resultid});

          this.items.push(insert);

          this.dialog = !this.dialog
        })
    }


  },
}
};
</script>
        <style>
  #app {
    margin-top: 0px;
  }

</style>
