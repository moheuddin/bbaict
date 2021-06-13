<template>
  <div>
    <h2 class="text-center mb-0 mt-0">সেতু বিভাগ</h2>
    <h4 class="text-center">সচিব মহোদয়ের দৈনন্দিন কর্মসুচি</h4>
    <template>
      <div>
        <div id="example" v-if="getprint">
          <div class="box wide hidden-on-narrow">

            <div class="box-col no-print">
              <button class="export-pdf k-button btn" @click="sendPrint">Print</button>
              <button class="export-pdf k-button btn" @click="closePrint">Close</button>
            </div>
          </div>
          <div class="page-container hidden-on-narrow">
            <div class="pdf-page size-a4">
              <div class="pdf-header">
                <span class="company-logo">
                  সচিব মহোদয়ের দৈনন্দিন কর্মসুচি
                </span><br>
                সেতু বিভাগ, সেতু ভবন, বনানী, ঢাকা।
                <span class="invoice-number">মুদ্রণ তারিখ: {{currentDate}}</span>
              </div>
              <!--<div class="pdf-footer">
                <p>Bridges Division<br />
                  Developed By: Md. Moheuddin, Assitant Programmer, BBA
                </p>
              </div>-->
              <div class="pdf-body">

                <table class="table">
                  <tr><th>#</th><th>তারিখ</th><th>সময়</th><th>কার্যক্রম</th><th>স্থান</th><th>মন্তব্য</th></tr>
                  <tr v-for="(item,i) in items" :key="i">
                    <td>{{ item.sl }}</td>
                    <td>{{ item.date }}</td>
                    <td>{{ item.time }}</td>
                    <td>{{ item.program }}</td>
                    <td>{{ item.location }}</td>
                    <td>{{ item.comments }}</td>
                  </tr>
                </table>


              </div>

            </div>
            <footer>
              <p>Bridges Division<br />
                Developed By: Md. Moheuddin, Assitant Programmer, BBA
              </p>
            </footer>
          </div>

        </div>

        <button @click="print"></button>
      </div>
    </template>
<v-app v-if="!getprint">
    <v-card-title>
        <div id="toolBtns" class="tabs noprint">
            <button class="btn" type="button" @click="getButton('btn1')" :class="{active: activeBtn === 'btn1'}">চলমান</button>
            <button class="btn" type="button" @click="getButton('btn2')" :class="{active: activeBtn === 'btn2' }">পূর্বের</button>
            <button class="btn" @click="print" :class="">মুদ্রণ</button>
            <vue-blob-json-csv @success="" @error="" file-type="csv" file-name="program.csv" :data="items">
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
            <v-data-table :headers="headers" :items="items" :search="search" mobile-breakpoint="800" class="elevation-0">
                <template v-slot:item.id="{ item}">
              <span>{{item.sl}}</span>
            </template>
            </v-data-table>
        </v-card>
    </v-main>
</v-app>
</div>
</template>

<script>
    import axios from 'axios'
    import Vue from 'vue'
    import Vuetify from 'vuetify'
    import 'vuetify/dist/vuetify.min.css'
    import VueBlobJsonCsv from 'vue-blob-json-csv';
    import AdminMenu from '../view/components/Adminmenu';
    //import moment from 'moment'
    import localization from 'moment/locale/bn';
    import config from '../../config';
    import jQuery from 'jQuery'
    Vue.use(VueBlobJsonCsv)
        //Vue.prototype.moment = moment
    Vue.use(Vuetify)

    const opts = {}
    export default {
        name: 'Home',
        vuetify: new Vuetify(),
        components: {
            AdminMenu
        },
        data() {
            return {
                output: null,
                getprint: false,
                loading: false,
                currentDate: new Date().toLocaleString(),
                activeBtn: '',
                apiURL: '',
                options: [{
                    code: 'CA',
                    country: 'Canada'
                }, {
                    code: 'VA',
                    country: 'Bangladesh'
                }],
                programType: '',
                search: '',
                select: {
                    state: 'Florida',
                    abbr: 'FL'
                },
                itemsfilter: [

                ],
                headers: [{
                    text: '#',
                    value: 'id',
                    name: 'id',
                    sortable: true
                }, {
                    text: 'তারিখ',
                    value: 'date',
                    name: 'date'
                }, {
                    text: 'সময়',
                    value: 'time',
                    name: 'time',
                    sortable: false,
                    width: "100"
                }, {
                    text: 'কার্যক্রম',
                    value: 'program',
                    name: 'program',
                    width: "180"
                }, {
                    text: 'স্থান',
                    value: 'place',
                    name: 'place',
                    width: "180"
                }, {
                    text: 'মন্তব্য',
                    value: 'comments',
                    name: 'comments',
                    width: "180"
                }],
                items: []
            }
        },
        mounted() {
            this.activeBtn = 'btn1';
            this.loadItems(config.API_URL);

            let self = this;
            window.addEventListener('keyup', function(event) {
                // If  ESC key was pressed...
                if (event.keyCode === 27) {
                    // try close your dialog
                    self.getprint = false;
                }
            });
            //foo()
        },
        methods: {
            replace: function(index) {
                let bengali = ["০", "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯"];
                return bengali.indexOf(index);
            },
            /* filters: {
               moment: function (date) {
                 return moment(date).format('MMMM Do YYYY, h:mm:ss a');
               }
             },*/
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
            print: function() {
                // Pass the element id here
                this.getprint = true;
            },
            sendPrint: function() {
                // Get HTML to print from element
                const prtHtml = document.getElementById('example').innerHTML;

                // Get all stylesheets HTML
                let stylesHtml = '';
                for (const node of[...document.querySelectorAll('link[rel="stylesheet"], style')]) {
                    stylesHtml += node.outerHTML;
                }

                // Open the print window
                const WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');

                WinPrint.document.write(`<!DOCTYPE html>
              <html>
                <head>
                  ${stylesHtml}
                </head>
                <body>
                  ${prtHtml}
                </body>
              </html>`);

                //WinPrint.document.close();
                WinPrint.focus();
                WinPrint.print();
                WinPrint.close();
            },
            closePrint: function() {
                this.getprint = false
            },
            getButton: function(btn) {

                if (btn == "btn1") {
                    this.activeBtn = 'btn1';
                    this.loadItems(config.API_URL);

                } else if (btn == "btn2") {
                    this.activeBtn = 'btn2'

                    this.loadItems(config.API_URL, 'previous');
                }

            },
            loadItems($app_url = '', $filter = '') {
                this.loading = true;
                let data = {};
                if ($filter != '') {
                    data = 'previous'
                }
                //this.items = []
                //axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
                axios.get($app_url + 'ftontend-program.php', {
                        //headers: { Authorization: "Bearer "  },
                        params: {
                            data
                        }
                    })
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
                        this.loading = false;
                        //console.log(response.data.result);
                    }).catch((error) => {
                        //console.log(error)
                    })
            }
        }
    };
</script>
<style>
    .box.wide.hidden-on-narrow {
        overflow: hidden;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
        margin-top: 30px;
        padding: 5px;
    }
</style>