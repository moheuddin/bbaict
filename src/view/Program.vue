<template>
  <div class="container">
    <div id="template">
      <v-app>
        <div v-if="loading" class="text-center">
          <v-progress-circular
            indeterminate
            color="red"></v-progress-circular>
        </div>
        <v-main class="container align-to">
          <v-card-title>
            <div class="btn-group" role="group" id="toolBtns">
              <button class="btn" type="button" @click="getButton('btn1')"
                :class="{active: activeBtn === 'btn1'}">On Going</button>
              <button class="btn" type="button" @click="getButton('btn2')"
                :class="{active: activeBtn === 'btn2' }">Previous</button>
              <button class="btn" @click="print" :class="">Printing</button>
            </div>
            <v-spacer></v-spacer>
            <v-text-field v-model="search" append-icon="" label="Search Program"
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

              <template v-slot:item.date="{ item}">
                <span>test </span>
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
            <!-- this dialog is used for both create and update -->
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

                      <v-col cols="12" sm="6">
                        <v-menu
                          v-model="menu1"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          transition="scale-transition"
                          offset-y
                          min-width="auto">
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              v-model="editedItem.date"
                              label="Date"
                              prepend-icon="mdi-calendar"
                              readonly
                              v-bind="attrs"
                              v-on="on"
                              name="date"
                              v-validate="'required'"></v-text-field>
                            <span v-show="errors.has('date')"
                              class="text-danger">{{ errors.first('date') }}</span>
                          </template>
                          <v-date-picker
                            v-model="editedItem.date"
                            @input="menu1= false"></v-date-picker>
                        </v-menu>
                      </v-col>
                      <v-col cols="12" sm="6">
                        <v-menu
                          ref="menu2"
                          v-model="menu2"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          :return-value.sync="time"
                          transition="scale-transition"
                          offset-y
                          max-width="290px"
                          min-width="290px">
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              v-model="editedItem.time"
                              label="Time"
                              prepend-icon="mdi-clock-time-four-outline"
                              readonly
                              v-bind="attrs"
                              v-on="on"
                              name="time"
                              v-validate="'required'"></v-text-field>
                            <span v-show="errors.has('time')"
                              class="text-danger">{{ errors.first('time') }}</span>
                          </template>
                          <v-time-picker
                            v-if="menu2"
                            v-model="editedItem.time"
                            full-width
                            @click:minute="$refs.menu2.save(time)"></v-time-picker>
                        </v-menu>
                      </v-col>
                      <v-col cols="12" sm="12">
                        <v-text-field name="program" v-validate="'required'"
                          v-model="editedItem.program"
                          label="Program"></v-text-field>
                        <span v-show="errors.has('program')"
                          class="text-danger">{{ errors.first('program') }}</span>
                      </v-col>
                      <v-col cols="12" sm="12">
                        <v-text-field name="place" v-validate="'required'"
                          v-model="editedItem.place" label="Place"></v-text-field>
                        <span class="text-danger">{{ errors.first('place') }}</span>
                      </v-col>
                      <v-col cols="12" sm="12">
                        <v-textarea v-model="editedItem.comments"
                          label="Comments"></v-textarea>
                      </v-col>
                    </v-row>
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" text
                      @click="showEditDialog()">Cancel</v-btn>
                    <v-btn color="success" text :disabled="errors.any()"
                      @click="saveItem(editedItem)">Save <span style="position:
                        absolute;top: -32px;bottom: 0;" v-if="loading">
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
    </div>
  </template>

  <script>

import Vue from 'vue'
import axios from 'axios'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'


/*const httpClient = axios.create({
  baseURL: "http://youradress",
  // baseURL: process.env.APP_API_BASE_URL,
});

httpClient.interceptors.request.use(function (config) {
  const token = localStorage.getItem('user').jwt;
  config.headers.Authorization =  token ? `Bearer ${token}` : '';
  return config;
});
*/


export default {
  name: 'Program',
  vuetify: new Vuetify(),
  components:{},
  data () {
    return {
      output: null,
      activeBtn:'',
      loading:false,
      search:'',
      valid: true,
        headers: [
            { text: '#', value: 'id' },
            { text: 'তারিখ', value: 'date' },
            { text: 'সময়', value: 'time', sortable: false, width:"100" },
            { text: 'কার্যক্রম', value: 'program', name:'url', width:"180" },
            { text: 'স্থান/মন্তব্য', value: 'place', name:'url', width:"180" },
            { text: '', value: 'actions', sortable: false },
        ],
        items: [],
        dialog: false,
        date: new Date().toISOString().substr(0, 10),
        menu1: false,
        time: null,
        menu2: false,
        modal2: false,
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
  print () {
    // Pass the element id here
    //await this.$htmlToPaper('printMe');
  },
  resetValidation () {
    this.$refs.form.resetValidation()
  },
    getButton: function(btn){
    if (btn=="btn1"){
      this.activeBtn='btn1';
    }else if(btn=="btn2"){
      this.activeBtn='btn2'
    }

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

    this.loading=true;
    this.items = []

    axios.get($app_url+'program.php',{
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
      axios.delete($api_url+'program.php',{
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

    this.$validator.validateAll()

    if (!this.formValid){
      return;
    }
    this.loading=true;
      let id = item.id

      let $api_url = this.$API_URL;
      //console.log(item);

      let data = { fields: item }
      if (id) {
        //update
        axios.patch($api_url + 'program.php', {
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
        axios.post($api_url + 'program.php', {
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
