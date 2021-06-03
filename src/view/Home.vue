<template>
  <div>
       <h2 class="text-center mb-0 mt-0">সেতু বিভাগ</h2>
      <h4 class="text-center">সচিব মহোদয়ের দৈনন্দিন কর্মসুচি</h4>



      <v-app>
        <v-card-title>
            <div id="toolBtns" class="tabs noprint">
                  <button class="btn" type="button" @click="getButton('btn1')" :class="{active: activeBtn === 'btn1'}">চলমান</button>
                  <button class="btn" type="button" @click="getButton('btn2')" :class="{active: activeBtn === 'btn2' }">পূর্বের</button>
                  <button class="btn" @click="print"  :class="">মুদ্রণ</button>
                      <vue-blob-json-csv
                    @success=""
                    @error=""
                    file-type="csv"
                    file-name="program.csv"
                    :data="items"
                  >
                  <v-btn color="success">ডাউনলোড (CSV)</v-btn>
                  </vue-blob-json-csv>
            </div>
          <v-spacer></v-spacer>

          <v-text-field v-model="search" append-icon="" label="খুঁজুন" single-line hide-details></v-text-field>
        </v-card-title>
          <v-main class="container align-to">

              <v-card>
                <div v-if="loading" class="text-center">
                  <v-progress-circular indeterminate color="red"></v-progress-circular>
                </div>
                  <v-data-table
                  :headers="headers"
                  :items="items"
                  :search="search"
                  mobile-breakpoint="800"
                  class="elevation-0">
                  <template v-slot:item.id="{ item}">
                    <span>{{item.sl}}</span>
                  </template>

                  </v-data-table>

              </v-card>
          </v-main>
      </v-app>


  </div>
</template>
<AdminMenu />
<script>
import axios from 'axios'
import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import VueBlobJsonCsv from 'vue-blob-json-csv';
import AdminMenu from '../view/components/Adminmenu';
Vue.use(VueBlobJsonCsv)

Vue.use(Vuetify)

const opts = {}
import print from 'vue3-print-nb'
Vue.use( print);
export default {
  name: 'Home',
  vuetify: new Vuetify(),
  components: {
    AdminMenu
},
  data () {
    return {
      output: null,
      loading:false,
      activeBtn:'',
      apiURL:'',
      options: [
      {code: 'CA', country: 'Canada'},
      {code: 'VA', country: 'Bangladesh'}
      ],
      programType:'',
      search:'',
      select: { state: 'Florida', abbr: 'FL' },
      itemsfilter: [

    ],
        headers: [
            { text: '#', value: 'id', name:'id',sortable: true },
            { text: 'তারিখ', value: 'date', name: 'date' },
            { text: 'সময়', value: 'time', name:'time', sortable: false, width:"100" },
            { text: 'কার্যক্রম', value: 'program', name:'program', width:"180" },
            { text: 'স্থান', value: 'place', name:'place', width:"180" },
            { text: 'মন্তব্য', value: 'comments', name:'comments', width:"180" }
        ],
        items: []
    }
  },
  mounted() {
    this.activeBtn = 'btn1';
    let myapiurl =this.$API_URL;
    this.apiURL= myapiurl;
    this.loadItems(myapiurl);
  },
  computed: {

  },

methods:{
  replace:function(index){
    let bengali = ["০", "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯"];
    return bengali.indexOf(index);
  },

download() {
	// credit: https://www.bitdegree.org/learn/javascript-download
	let filename = 'program.csv';
	let text = Papa.unparse(this.items);

	let element = document.createElement('a');
	element.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(text));
	element.setAttribute('download', filename);

	element.style.display = 'none';
	document.body.appendChild(element);

	element.click();
	document.body.removeChild(element);

},
  print() {
    // Pass the element id here
   this.$htmlToPaper('printMe');
  },
    getButton: function(btn){

    if (btn=="btn1"){
      this.activeBtn='btn1';
      this.loadItems(this.apiURL);

    }else if(btn=="btn2"){
      this.activeBtn='btn2'

      this.loadItems(this.apiURL, 'previous');
    }

  },
  loadItems($app_url='',$filter='') {
    this.loading=true;
    let data={};
    if($filter!=''){
      data = 'previous'
    }
      //this.items = []
      //axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
      axios.get($app_url+'ftontend-program.php',{
          //headers: { Authorization: "Bearer "  },
          params: {
            data
          }
        }
          )
      .then((response) => {

          /*this.items = response.data.result.map((item)=>{
              return {
                  id: item.id,
                  date:item.date,
                  date:item.time,
                  date:item.program,
                  date:item.place,
                  date:item.comments,
              }
          })*/
          this.items = response.data.result;
          this.loading=false;
          console.log(response.data.result);
      }).catch((error) => {
          console.log(error)
      })
  }
}

};
</script>
<style>

</style>
