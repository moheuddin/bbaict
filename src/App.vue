
  <template>
    <div id="app">
      <div class="container">
        <h2 class="text-center">{{myAppName}}</h2>
        <Mainnav />

      </div>

      <div class="container pt-0">
        <router-view />
      </div>
    </div>
  </template>



<script>
import  Mainnav from './view/components/Mainnav';
export default {
    name: 'App',
    components: {Mainnav},
    data() {
      return {
        isOpen: false,
        showAdminMenu: false
      }
    },
    computed: {
      myAppName() {
        return this.$store.getters.getappname;
      }
    }
  }
</script>
